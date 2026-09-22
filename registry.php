<?php
/**
 * Was dieses Plugin übersetzt, und in welchem Zustand.
 *
 * Bis 1.7.1 stand die Domainliste als flaches Array in der Hauptdatei, und
 * drei Schwester-Plugins hielten je ihre eigene. Seit 2.0.0 ist es eine
 * Registry: Sie beantwortet alles, was der Ladeweg, die Migration und die
 * Einstellungsseite über eine Domain wissen müssen.
 *
 * Bewusst nicht per glob() über den languages-Ordner ermittelt: Eine
 * versehentlich dort abgelegte Datei soll keine fremde Domain kapern.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Die Textdomains, für die dieses Plugin Kataloge mitbringt.
 *
 * Felder je Eintrag:
 *
 *   slug     Ordnername unter wp-content/plugins/. Meist gleich der Domain,
 *            aber nicht zwingend — FluentPDF führt zwei Domains aus einem
 *            Ordner. Deshalb ein eigenes Feld und keine Ableitung.
 *   titel    Wie das Plugin sich nennt. Für die Einstellungsseite.
 *   anreden  Welche Locales im Paket liegen. Geladen wird ohnehin nur die
 *            aktive; die Liste steuert die Anzeige und das Bauen.
 *   paket    Was mit dem Sprachpaket von wordpress.org geschieht:
 *            'fuellen' — nach dem eigenen Katalog nachladen, es deckt Lücken
 *                        ab, die wir nicht haben (bei FluentCommunity rund
 *                        950 Strings).
 *            'sperren' — gar nicht laden. Für Domains, deren Paket so
 *                        lückenhaft oder abweichend ist, dass es mehr
 *                        Terminologie kaputtmacht als es füllt.
 *            Das ist die einzige Stelle, an der sich die vier
 *            zusammengeführten Plugins im Verhalten unterschieden haben.
 *   extras   Dateien unter extras/, die nur geladen werden, wenn die Domain
 *            aktiv ist. Sie enthalten, was der Katalog nicht kann.
 *   vorgaenger  Das Plugin, das diese Domain bis 2.0.0 bedient hat, als Pfad
 *            relativ zum Plugin-Ordner. Solange es aktiv ist, hält sich
 *            dieses Plugin für diese Domain komplett zurück — sonst laden
 *            zwei Plugins denselben Katalog und deklarieren dieselben
 *            Funktionen. Siehe migration.php.
 */
function pcl_fluent_de_registry() {
    return apply_filters('pcl_fluent_de/registry', array(

        // --- FluentCommunity (vormals pcl-fluent-de bis 1.7.1) -------------
        'fluent-community' => array(
            'slug'    => 'fluent-community',
            'titel'   => 'FluentCommunity',
            'anreden' => array('de_DE'),
            'paket'   => 'fuellen',
            'extras'  => array('fluentcommunity.php'),
        ),
        'fluent-community-pro' => array(
            'slug'    => 'fluent-community-pro',
            'titel'   => 'FluentCommunity Pro',
            'anreden' => array('de_DE'),
            'paket'   => 'fuellen',
            'extras'  => array(),
        ),
        'fluent-messaging' => array(
            'slug'    => 'fluent-messaging',
            'titel'   => 'FluentMessaging',
            'anreden' => array('de_DE'),
            'paket'   => 'fuellen',
            'extras'  => array(),
        ),
        'fluent-player' => array(
            'slug'    => 'fluent-player',
            'titel'   => 'FluentPlayer',
            'anreden' => array('de_DE'),
            'paket'   => 'fuellen',
            'extras'  => array(),
        ),
        'fluent-player-pro' => array(
            'slug'    => 'fluent-player-pro',
            'titel'   => 'FluentPlayer Pro',
            'anreden' => array('de_DE'),
            'paket'   => 'fuellen',
            'extras'  => array(),
        ),

        // --- FluentAuth (vormals pcl-fluentauth-de) ------------------------
        'fluent-security' => array(
            'slug'    => 'fluent-security',
            'titel'   => 'FluentAuth',
            'anreden' => array('de_DE'),
            'paket'   => 'sperren',
            'extras'  => array('fluentauth-mail.php', 'fluentauth-map.php'),
            'vorgaenger' => 'pcl-fluentauth-de/pcl-fluentauth-de.php',
        ),

        // --- FluentSMTP (vormals pcl-fluentsmtp-de) ------------------------
        'fluent-smtp' => array(
            'slug'    => 'fluent-smtp',
            'titel'   => 'FluentSMTP',
            'anreden' => array('de_DE', 'de_DE_formal'),
            'paket'   => 'sperren',
            'extras'  => array('fluentsmtp-datum.php'),
            'vorgaenger' => 'pcl-fluentsmtp-de/pcl-fluentsmtp-de.php',
        ),

        // --- FluentSnippets (vormals pcl-fluentsnippets-de) ----------------
        'easy-code-manager' => array(
            'slug'    => 'easy-code-manager',
            'titel'   => 'FluentSnippets',
            'anreden' => array('de_DE', 'de_DE_formal'),
            'paket'   => 'sperren',
            'extras'  => array('fluentsnippets-relative-zeit.php'),
            'vorgaenger' => 'pcl-fluentsnippets-de/pcl-fluentsnippets-de.php',
        ),
    ));
}

/**
 * Ein einzelner Registry-Eintrag, mit Vorgaben für fehlende Felder.
 *
 * Wer die Registry filtert, soll nicht jedes Feld setzen müssen — ein Eintrag
 * mit nur 'titel' funktioniert.
 */
function pcl_fluent_de_eintrag($domain) {
    $registry = pcl_fluent_de_registry();

    if (!isset($registry[$domain])) {
        return null;
    }

    return array_merge(array(
        'slug'       => $domain,
        'titel'      => $domain,
        'anreden'    => array('de_DE'),
        'paket'      => 'fuellen',
        'extras'     => array(),
        'vorgaenger' => '',
    ), (array) $registry[$domain]);
}

/**
 * Bedient noch ein Vorgänger-Plugin diese Domain?
 *
 * Gelesen wird `active_plugins` direkt statt über is_plugin_active(): Die
 * Prüfung läuft auf `plugins_loaded` Priorität 1, und dort ist
 * wp-admin/includes/plugin.php im Frontend nicht geladen. Die Option ist
 * autoload, steht also ohnehin im Speicher.
 *
 * Warum das beim *Laden* geprüft wird und nicht nur beim Aktivieren: Der
 * Aktivierungs-Hook kommt zu spät. WordPress lädt dieses Plugin, bevor er
 * feuert — und wenn dann zwei Dateien dieselbe Funktion deklarieren, steht
 * die Seite, bevor irgendeine Migration laufen kann. Am 22.09.2026 auf dev
 * genau so passiert: Fatal error, HTTP 500, und der Aktivierungs-Hook hatte
 * nie eine Gelegenheit.
 */
function pcl_fluent_de_vorgaenger_aktiv($domain) {
    static $aktiv = null;

    if ($aktiv === null) {
        $aktiv = (array) get_option('active_plugins', array());

        if (is_multisite()) {
            $aktiv = array_merge(
                $aktiv,
                array_keys((array) get_site_option('active_sitewide_plugins', array()))
            );
        }
    }

    $eintrag = pcl_fluent_de_eintrag($domain);

    if (!$eintrag || !$eintrag['vorgaenger']) {
        return false;
    }

    return in_array($eintrag['vorgaenger'], $aktiv, true);
}

/**
 * Ist das übersetzte Plugin überhaupt installiert?
 *
 * Gemessen am Ordner, nicht an einer Konstanten: Die Prüfung läuft, bevor ein
 * fremdes Plugin geladen ist, und sie muss auch für ein deaktiviertes Plugin
 * stimmen — dessen Einstellung soll erhalten bleiben, nicht verschwinden.
 */
function pcl_fluent_de_plugin_da($domain) {
    $eintrag = pcl_fluent_de_eintrag($domain);

    if (!$eintrag) {
        return false;
    }

    return is_dir(WP_PLUGIN_DIR . '/' . $eintrag['slug']);
}

/* -------------------------------------------------------------------------
 * Zustand je Domain
 * ---------------------------------------------------------------------- */

/**
 * Die drei Zustände, die eine Domain haben kann.
 *
 *   auto      Übersetzung an, sobald das Plugin da ist. Vorgabe.
 *   aus       Unsere Übersetzung nicht laden. Was WordPress sonst findet —
 *             das Sprachpaket von wordpress.org — greift weiter.
 *   englisch  Keine deutsche Übersetzung, auch nicht die fremde. Das Plugin
 *             bleibt englisch.
 *
 * Der Unterschied zwischen `aus` und `englisch` ist der Punkt: Das eine nimmt
 * unsere Fassung weg, das andere alle.
 */
function pcl_fluent_de_zustaende() {
    return array('auto', 'aus', 'englisch');
}

/**
 * Der gespeicherte Zustand aller Domains.
 *
 * Eine Option für alle, nicht eine je Domain: Das ist ein Eintrag im
 * Autoload-Cache statt neun, und sie wird auf jedem Request gelesen.
 *
 * Die Option entsteht erst, wenn jemand etwas umstellt. Solange sie fehlt,
 * gilt überall `auto` — eine frische Installation schreibt nichts in die
 * Datenbank.
 */
function pcl_fluent_de_alle_zustaende($neu_lesen = false) {
    static $cache = null;

    if ($cache === null || $neu_lesen) {
        $cache = (array) get_option('pcl_fluent_de_zustand', array());
    }

    return $cache;
}

/**
 * Der Zustand einer Domain. Unbekannte Werte fallen auf `auto` zurück.
 */
function pcl_fluent_de_zustand($domain) {
    $alle = pcl_fluent_de_alle_zustaende();

    if (!isset($alle[$domain]) || !in_array($alle[$domain], pcl_fluent_de_zustaende(), true)) {
        return 'auto';
    }

    return $alle[$domain];
}

/* -------------------------------------------------------------------------
 * Anrede je Domain
 * ---------------------------------------------------------------------- */

/**
 * Welche Anrede gilt für diese Domain?
 *
 * Rückgabe ist eine Locale: `de_DE` für Du, `de_DE_formal` für Sie — oder ein
 * leerer String für „die der Seite“, die Vorgabe.
 *
 * Erzwingen heißt: Wir laden eine andere Katalogdatei, als die Locale der
 * Seite vorgibt. Das geht, weil die Datei den Inhalt bestimmt und die Locale
 * nur die Zuordnung. Eine Seite auf `de_DE` kann so die Sie-Fassung eines
 * einzelnen Plugins zeigen, ohne dass der Rest der Seite umschaltet.
 *
 * Gespeichert wird in einer eigenen Option, nicht in der des Zustands: Die
 * beiden ändern sich unabhängig voneinander, und eine gemeinsame Struktur
 * hätte bei jedem Lesen eine Verzweigung mehr.
 */
function pcl_fluent_de_alle_anreden($neu_lesen = false) {
    static $cache = null;

    if ($cache === null || $neu_lesen) {
        $cache = (array) get_option('pcl_fluent_de_anrede', array());
    }

    return $cache;
}

/**
 * Die erzwungene Anrede einer Domain, oder '' für „die der Seite“.
 *
 * Eine Anrede, die es für diese Domain gar nicht gibt, wird ignoriert — sonst
 * lüde das Plugin eine Datei, die nicht existiert, und die Domain bliebe
 * stumm englisch. Das kann passieren, wenn ein Katalog später wegfällt.
 */
function pcl_fluent_de_anrede($domain) {
    $alle = pcl_fluent_de_alle_anreden();

    if (empty($alle[$domain])) {
        return '';
    }

    $eintrag = pcl_fluent_de_eintrag($domain);

    if (!$eintrag || !in_array($alle[$domain], $eintrag['anreden'], true)) {
        return '';
    }

    return $alle[$domain];
}

/**
 * Hat diese Domain überhaupt etwas zu wählen?
 */
function pcl_fluent_de_hat_anredewahl($domain) {
    $eintrag = pcl_fluent_de_eintrag($domain);

    return $eintrag && count($eintrag['anreden']) > 1;
}

/**
 * Die Locale, unter der der Katalog dieser Domain gelesen wird.
 *
 * Ohne erzwungene Anrede ist das die Locale der Seite. Mit erzwungener Anrede
 * die gewählte — aber nur, wenn die Seite überhaupt deutsch ist. Auf einer
 * englischen oder französischen Seite hat unsere Anredefrage keinen Sinn, und
 * ein deutscher Katalog wäre dort schlicht falsch.
 */
function pcl_fluent_de_katalog_locale($domain, $locale) {
    if (strpos($locale, 'de_') !== 0) {
        return $locale;
    }

    $anrede = pcl_fluent_de_anrede($domain);

    return $anrede ? $anrede : $locale;
}

/**
 * Zustand setzen. `auto` wird nicht gespeichert, sondern entfernt — so bleibt
 * die Option klein und enthält nur, was vom Normalfall abweicht.
 */
function pcl_fluent_de_zustand_setzen($domain, $zustand) {
    if (!in_array($zustand, pcl_fluent_de_zustaende(), true)) {
        return false;
    }

    $alle = (array) get_option('pcl_fluent_de_zustand', array());

    if ($zustand === 'auto') {
        unset($alle[$domain]);
    } else {
        $alle[$domain] = $zustand;
    }

    $ergebnis = $alle
        ? update_option('pcl_fluent_de_zustand', $alle)
        : delete_option('pcl_fluent_de_zustand');

    // Der statische Cache hält sonst den Stand von vor dem Schreiben, und wer
    // im selben Request nachliest, bekommt den alten Wert.
    pcl_fluent_de_alle_zustaende(true);

    return $ergebnis;
}
