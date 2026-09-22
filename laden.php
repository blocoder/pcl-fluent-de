<?php
/**
 * Der Ladeweg: wer zuerst lädt, gewinnt.
 *
 * WordPress lädt für eine Textdomain mehrere Kataloge und fragt sie der Reihe
 * nach ab. WP_Translation_Controller::locate_translation() nimmt die *erste*
 * Datei, die den String kennt — spätere füllen nur noch Lücken. Gemessen an
 * WordPress 7.0.2, nicht aus der Dokumentation abgeleitet.
 *
 * Daraus folgt der frühe Hook: `plugins_loaded` Priorität 1, bevor eines der
 * übersetzten Plugins seine erste Zeichenkette anfordert und bevor das
 * Sprachpaket von wordpress.org zum Zug kommt.
 *
 * Dass dieser Weg auf jedem Request alle Kataloge einliest, auch auf Seiten,
 * die keinen einzigen ihrer Strings ausgeben, ist bekannt und der Grund für
 * Etappe 3 des Umbaus: `load_textdomain_mofile` wird auch aus
 * _load_textdomain_just_in_time() heraus gefragt (wp-includes/l10n.php:795,
 * aufgerufen aus Zeile 1466), der Vorrang ließe sich also auch ohne
 * Vorausladen herstellen. Solange das nicht an den 531 Divergenz-Proben
 * gemessen ist, bleibt es beim bewährten Weg.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Unser languages-Ordner, normalisiert. Die Sperre vergleicht Pfade damit.
 */
function pcl_fluent_de_languages_dir() {
    return plugin_dir_path(__FILE__) . 'languages/';
}

/**
 * Welche Domains sind gerade wirksam — Plugin vorhanden und nicht abgeschaltet?
 *
 * Eine Domain im Zustand `englisch` ist hier nicht dabei: Ihr Katalog wird
 * nicht geladen. Die Sperre gegen fremde Kataloge hängt an einer eigenen
 * Prüfung, weil sie auch dann greifen muss.
 */
function pcl_fluent_de_aktive_domains() {
    $aktiv = array();

    foreach (pcl_fluent_de_registry() as $domain => $_) {
        if (pcl_fluent_de_zustand($domain) !== 'auto') {
            continue;
        }

        if (!pcl_fluent_de_plugin_da($domain)) {
            continue;
        }

        // Solange der Vorgänger diese Domain noch bedient, hält sich dieses
        // Plugin heraus — sonst laden zwei Kataloge und zwei gleichnamige
        // Funktionen. Der Hinweis im Backend sagt, was zu tun ist.
        if (pcl_fluent_de_vorgaenger_aktiv($domain)) {
            continue;
        }

        $aktiv[] = $domain;
    }

    return $aktiv;
}

/**
 * Braucht diese Domain den frühen Ladeweg?
 *
 * Seit 2.2.0 gibt es zwei Wege, und die Registry entscheidet, welcher gilt:
 *
 * **Spät** (`paket` => 'sperren'). Wir nennen WordPress nur den Ordner
 * (`set_custom_path()`) und lassen es laden, wenn der erste `__()` für die
 * Domain kommt. Auf einer Seite, die die Domain nie anfasst, wird gar nichts
 * gelesen. Der Vorrang bleibt, weil unser Ordner der einzige Pfad ist, den
 * WordPress für die Domain kennt — und die Sperre hält den Rest fern.
 *
 * **Früh** (`paket` => 'fuellen'). Hier laden wir weiter voraus, und zwar aus
 * einem Grund, der sich nicht wegoptimieren lässt: Nach unserem Katalog muss
 * das Sprachpaket von wordpress.org als Lückenfüller kommen — bei
 * FluentCommunity rund 950 Strings. Beim Laden auf Abruf lädt WordPress
 * genau *eine* Datei, und die Reihenfolge ist nicht mehr unsere. Wer den
 * Lückenfüller vorher lädt, verliert die eigene Terminologie; wer ihn
 * nachher lädt, kommt zu spät.
 *
 * Wenn die Lücken eines Tages im eigenen Katalog stehen, fällt dieser
 * Unterschied weg und alle Domains können spät laden.
 */
function pcl_fluent_de_laedt_frueh($domain) {
    $eintrag = pcl_fluent_de_eintrag($domain);

    return $eintrag && $eintrag['paket'] === 'fuellen';
}

/**
 * Den späten Weg vorbereiten: WordPress unseren Ordner nennen, mehr nicht.
 *
 * `set_custom_path()` ist eine Array-Operation, kein Dateizugriff. Danach
 * kennt `_load_textdomain_just_in_time()` den Pfad und baut sich den
 * Dateinamen daraus selbst zusammen (`{$pfad}{$domain}-{$locale}.mo`) — genau
 * so heißen unsere Kataloge.
 *
 * Gemessen am 22.09.2026 auf dev: Der Weg trägt, die Übersetzung kommt, und
 * `load_textdomain_mofile` wird dabei gefragt. Wer das nachprüfen will, muss
 * `unload_textdomain($domain, true)` benutzen — ohne das zweite Argument
 * trägt WordPress die Domain in `$l10n_unloaded` ein und überspringt sie
 * beim Laden auf Abruf für den Rest des Requests. Eine Messung ohne das
 * Argument meldet „Filter feuerte nie“ und misst dabei sich selbst.
 */
function pcl_fluent_de_pfade_eintragen($locale = '') {
    global $wp_textdomain_registry;

    if (!$wp_textdomain_registry) {
        return;
    }

    $locale = $locale ? $locale : determine_locale();
    $dir    = pcl_fluent_de_languages_dir();

    foreach (pcl_fluent_de_aktive_domains() as $domain) {
        if (pcl_fluent_de_laedt_frueh($domain)) {
            continue;
        }

        // Beides, und beides ist nötig.
        //
        // set_custom_path() ist der dokumentierte Weg für ein Plugin, dessen
        // Kataloge woanders liegen — es greift aber nur, wenn WordPress für
        // die Domain noch keinen eigenen Pfad gefunden hat. Hat es einen
        // (`languages/plugins/`, weil dort ein Sprachpaket liegt), gewinnt
        // der: `get()` fragt zuerst die selbst ermittelten Pfade ab, und
        // `set_custom_path()` räumt die nur weg, wenn sie leer sind.
        //
        // Ohne die zweite Zeile baut das Laden auf Abruf deshalb den Pfad zur
        // *fremden* Datei zusammen — und unsere eigene Sperre blockt sie.
        // Ergebnis: gar keine Übersetzung. Genau so am 22.09.2026 auf dev
        // gemessen, bei allen drei Domains mit vorhandenem Sprachpaket.
        $wp_textdomain_registry->set_custom_path($domain, $dir);
        $wp_textdomain_registry->set($domain, $locale, $dir);
    }
}
add_action('plugins_loaded', 'pcl_fluent_de_pfade_eintragen', 1);

// Beim Sprachwechsel zeigt der Eintrag sonst weiter auf die alte Locale.
add_action('change_locale', 'pcl_fluent_de_pfade_eintragen', 1);

/**
 * Lädt die Kataloge, die den frühen Weg brauchen.
 *
 * @param string $locale Optional. Leer heißt: die gerade gültige Locale. Beim
 *                       Hook `change_locale` reicht WordPress die neue herein.
 */
function pcl_fluent_de_load($locale = '') {
    $locale = $locale ? $locale : determine_locale();
    $dir    = pcl_fluent_de_languages_dir();

    foreach (pcl_fluent_de_aktive_domains() as $domain) {
        if (!pcl_fluent_de_laedt_frueh($domain)) {
            continue;
        }

        // Die Datei bestimmt die Anrede, die Locale nur die Zuordnung: Wer
        // „Sie“ erzwingt, bekommt fluent-smtp-de_DE_formal.mo unter de_DE
        // geladen. Ohne erzwungene Anrede sind beide gleich.
        $katalog = pcl_fluent_de_katalog_locale($domain, $locale);
        $eigen   = $dir . $domain . '-' . $katalog . '.mo';

        // Fehlt die erzwungene Fassung, gilt wieder die der Seite. Lieber die
        // andere Anrede als gar keine Übersetzung.
        if (!is_readable($eigen) && $katalog !== $locale) {
            $eigen = $dir . $domain . '-' . $locale . '.mo';
        }

        if (!is_readable($eigen)) {
            continue;
        }

        // load_textdomain() statt load_plugin_textdomain(): Letzteres würde
        // zuerst in WP_LANG_DIR nachsehen und damit genau die Reihenfolge
        // herstellen, die wir vermeiden wollen.
        load_textdomain($domain, $eigen, $locale);

        $eintrag = pcl_fluent_de_eintrag($domain);

        if ($eintrag['paket'] !== 'fuellen') {
            continue;
        }

        // Das Sprachpaket von wordpress.org direkt danach — falls vorhanden.
        //
        // Ohne diese Zeile gingen bei FluentCommunity rund 950 Strings
        // verloren. Grund: _load_textdomain_just_in_time() überspringt jede
        // Domain, die bereits geladen ist. Wer früh lädt, verhindert damit
        // ungewollt, dass WordPress das Sprachpaket überhaupt noch anfasst.
        // Gemessen am 31.07.2026 — vorher füllte das Paket die Lücken, danach
        // standen „Target Lessons“ und „Public only“ wieder englisch da.
        //
        // Die Reihenfolge macht die Musik: Der Controller nimmt die erste
        // Datei, die den String kennt. Unsere steht davor und gewinnt, das
        // Paket kommt nur dort zum Zug, wo wir nichts haben.
        //
        // Für Domains mit 'paket' => 'sperren' ist das anders entschieden; die
        // Begründung steht in der Registry.
        $paket = WP_LANG_DIR . '/plugins/' . $domain . '-' . $locale . '.mo';

        if (is_readable($paket)) {
            load_textdomain($domain, $paket, $locale);
        }
    }
}
add_action('plugins_loaded', 'pcl_fluent_de_load', 1);

// Beim Sprachwechsel noch einmal. WordPress lädt die Kataloge der betroffenen
// Domains dann neu, aber nur aus den Orten, die es kennt: WP_LANG_DIR und der
// Domain Path des jeweiligen Plugins. Unser Ordner gehört zu keinem von
// beiden, und die bereits geladene Fassung bleibt nicht stehen — sie wird
// verworfen. Für Bestell- und Benachrichtigungsmails, die durch
// switch_to_locale() laufen, ist das kein Randfall.
//
// Bis 1.7.1 fehlte diese Zeile hier, während die drei Schwester-Plugins sie
// hatten. Aufgefallen beim Zusammenführen.
add_action('change_locale', 'pcl_fluent_de_load', 1);

/**
 * Auf dem späten Weg die erzwungene Anredefassung unterschieben.
 *
 * Beim Laden auf Abruf baut WordPress den Dateinamen selbst aus dem Ordner
 * und der Locale der Seite (`{$pfad}{$domain}-{$locale}.mo`). Wer eine andere
 * Anrede will, muss an dieser Stelle eingreifen — der Filter ist der einzige
 * Ort, an dem der fertige Pfad noch zu ändern ist.
 *
 * Der Filter hängt nur dann etwas um, wenn es wirklich etwas umzuhängen gibt:
 * Ohne erzwungene Anrede, für eine fremde Datei oder für eine Domain auf dem
 * frühen Weg bleibt der Pfad unberührt.
 */
function pcl_fluent_de_anrede_unterschieben($mofile, $domain) {
    if (!pcl_fluent_de_eintrag($domain) || pcl_fluent_de_laedt_frueh($domain)) {
        return $mofile;
    }

    $locale  = determine_locale();
    $katalog = pcl_fluent_de_katalog_locale($domain, $locale);

    if ($katalog === $locale) {
        return $mofile;
    }

    $eigen = pcl_fluent_de_languages_dir() . $domain . '-' . $katalog . '.mo';

    // Nur tauschen, wenn die gewünschte Fassung auch da ist. Sonst bleibt es
    // bei der Anrede der Seite.
    return is_readable($eigen) ? $eigen : $mofile;
}
add_filter('load_textdomain_mofile', 'pcl_fluent_de_anrede_unterschieben', 10, 2);

/* -------------------------------------------------------------------------
 * Fremde deutsche Kataloge fernhalten
 * ---------------------------------------------------------------------- */

/**
 * Soll für diese Domain jeder fremde deutsche Katalog gesperrt werden?
 *
 * Zwei Gründe, aus denen das zutrifft:
 *
 * 1. Zustand `englisch` — ausdrücklich gewünscht, das Plugin soll englisch
 *    bleiben. Dann ist auch unser eigener Katalog nicht geladen.
 * 2. Registry-Feld 'paket' => 'sperren' — das Sprachpaket von wordpress.org
 *    ist für diese Domain lückenhaft oder widerspricht der Terminologie, und
 *    unser Katalog deckt sie vollständig ab.
 *
 * Im Zustand `aus` wird *nicht* gesperrt: Dort soll unsere Übersetzung weg
 * sein, nicht jede.
 */
function pcl_fluent_de_sperrt($domain) {
    // Gehört die Domain noch dem Vorgänger, entscheidet er auch über die
    // Sperre. Zwei Plugins, die gegenläufig sperren, wären ein Fehler, den
    // niemand findet.
    if (pcl_fluent_de_vorgaenger_aktiv($domain)) {
        return false;
    }

    $zustand = pcl_fluent_de_zustand($domain);

    if ($zustand === 'englisch') {
        return true;
    }

    if ($zustand === 'aus') {
        return false;
    }

    $eintrag = pcl_fluent_de_eintrag($domain);

    return $eintrag && $eintrag['paket'] === 'sperren';
}

/**
 * Fremde deutsche Kataloge für unsere Domains nicht laden lassen.
 *
 * load_textdomain() fragt diesen Filter vor dem Lesen. `true` heißt „ist
 * erledigt“: Die Datei wird nicht gelesen. Getroffen werden nur Dateien
 * unserer Domains, deren Name auf eine deutsche Locale lautet und die nicht
 * aus unserem languages/-Ordner kommen — das Sprachpaket in WP_LANG_DIR und
 * Locos Ordner. Andere Sprachen bleiben unberührt.
 *
 * Priorität 1, weil ein spätes `true` zu spät käme: Loco Translate hängt im
 * selben Filter und lädt *im* Callback. Gemessen an Loco 2.8.7 — es
 * respektiert einen hereingereichten Wert, die Sperre greift also auch bei
 * vorhandenem Loco-Katalog. Nach einem Loco-Update nachprüfen.
 */
function pcl_fluent_de_block_foreign($override, $domain, $mofile = '') {
    if ($override || !$mofile) {
        return $override;
    }

    if (!pcl_fluent_de_eintrag($domain) || !pcl_fluent_de_sperrt($domain)) {
        return $override;
    }

    $name = basename($mofile);

    // Nur deutsche Kataloge dieser Domain. `de_DE`, `de_DE_formal`, `de_CH` —
    // alles, was mit `de_` beginnt. Andere Sprachen gehen uns nichts an.
    if (strpos($name, $domain . '-de_') !== 0) {
        return $override;
    }

    // Die eigene Datei niemals sperren.
    $eigen = wp_normalize_path(pcl_fluent_de_languages_dir());

    if (strpos(wp_normalize_path($mofile), $eigen) === 0) {
        return $override;
    }

    return true;
}
add_filter('override_load_textdomain', 'pcl_fluent_de_block_foreign', 1, 3);

/* -------------------------------------------------------------------------
 * Extras
 * ---------------------------------------------------------------------- */

/**
 * Lädt die Zusatzdateien der aktiven Domains.
 *
 * Was in extras/ liegt, ist das, was ein Katalog nicht kann: fest verdrahtete
 * Texte über Filter, Datumsformate, die Fußzeile einer Mail. Diese Dateien
 * werden nur eingebunden, wenn ihre Domain aktiv ist — auf einer Installation
 * ohne FluentSMTP wird dessen Datumslogik nie gelesen.
 *
 * Auf `plugins_loaded` Priorität 2, also nach dem Laden der Kataloge: Die
 * Extras dürfen `__()` benutzen, und dann muss der Katalog stehen.
 */
function pcl_fluent_de_extras_laden() {
    $dir    = plugin_dir_path(__FILE__) . 'extras/';
    $fertig = array();

    foreach (pcl_fluent_de_aktive_domains() as $domain) {
        $eintrag = pcl_fluent_de_eintrag($domain);

        foreach ((array) $eintrag['extras'] as $datei) {
            // Mehrere Domains können sich eine Datei teilen; jede nur einmal.
            if (isset($fertig[$datei])) {
                continue;
            }

            $pfad = $dir . $datei;

            if (is_readable($pfad)) {
                require_once $pfad;
                $fertig[$datei] = true;
            }
        }
    }
}
add_action('plugins_loaded', 'pcl_fluent_de_extras_laden', 2);
