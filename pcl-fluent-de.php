<?php
/**
 * Plugin Name:       PC’L Übersetzungen für Fluent-Plugins
 * Plugin URI:        https://github.com/blocoder/pcl-fluent-de
 * Update URI:        https://github.com/blocoder/pcl-fluent-de
 * Description:       Liefert die deutschen Übersetzungen für FluentCommunity, FluentCommunity Pro, FluentMessaging, FluentPlayer, FluentAuth, FluentSMTP, FluentSnippets und FluentCRM aus. Lädt sie vor allen anderen Katalogen, damit die eigene Fassung gewinnt. Jede Übersetzung schaltet sich selbst ein, sobald ihr Plugin da ist, und lässt sich einzeln abschalten oder ganz auf Englisch stellen.
 * Version:           2.6.20
 * Requires at least: 6.5
 * Requires PHP:      7.4
 * Author:            Peter Claus Lamprecht (PC’L)
 * Author URI:        https://barmbek-nerd.de/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       pcl-fluent-de
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

/* -------------------------------------------------------------------------
 * Updates von GitHub
 * ---------------------------------------------------------------------- */

/**
 * Warum eine mitgelieferte Bibliothek und nicht der Core-Weg?
 *
 * WordPress kann das seit 5.8 selbst: Der Header `Update URI` oben nennt einen
 * Host, und der Core feuert dann `update_plugins_github.com`
 * (wp-includes/update.php). Wer sich daran hängt, braucht keine Zeile
 * Fremdcode.
 *
 * Nur muss dieser Jemand auf der Installation vorhanden sein. Dieses Plugin
 * läuft auch dort, wo es keinen SSH-Zugang und kein WP-CLI gibt und jede
 * Datei einzeln per FTP hochgeht — und genau dort ist ein Plugin, das seine
 * Updates selbst mitbringt, den Unterschied wert.
 *
 * Der `Update URI`-Header steht trotzdem oben, und zwar aus einem zweiten
 * Grund: Ohne ihn fragt WordPress für jedes Plugin bei wordpress.org nach.
 * Liegt dort je ein Plugin mit dem Ordnernamen `pcl-fluent-de`, bekäme diese
 * Installation dessen Update untergeschoben. Der Header schließt das aus.
 *
 * Die Bibliothek liest das neueste Release des Repos, nimmt die Versionsnummer
 * aus dem Tag-Namen (`v1.5.0` → `1.5.0`) und lädt das angehängte ZIP.
 * Vorabversionen überspringt sie.
 *
 * Was hier NICHT passiert: eine Signatur- oder Prüfsummenkontrolle. WordPress
 * bringt dafür einen Rahmen mit (`verify_file_signature()`, Ed25519), wendet
 * ihn aber nur auf Downloads von wordpress.org an — `wp_signature_hosts`
 * führt github.com nicht, und `wp_signature_softfail` steht ohnehin auf true.
 * Was schützt, ist HTTPS und GitHub. Entscheidung PC’L vom 06.09.2026; der
 * Weg dahin wäre, das ZIP zu signieren und die `.sig` als zweites
 * Release-Asset abzulegen.
 */

/**
 * Does this request need the update checker at all?
 *
 * Until 2.6.12 the library was built on every request, frontend and REST API
 * included: its files, a dozen hooks, its text domain, a look at the cron
 * schedule. None of that does anything there.
 *
 * What this does NOT change: in wp-admin and admin-ajax.php the library
 * still checks on `admin_init` once its 12 hours have passed. That is its
 * fallback for a late cron, and it means a synchronous call to the GitHub
 * API (3 s timeout per request) from the shared IP of the hosting, which
 * GitHub allows 60 unauthenticated requests per hour. With the cron running
 * on time, the cron event gets there first (it checks 20 minutes early).
 *
 * Every path that shows, checks or installs an update runs in one of three
 * contexts, and only there is the library built:
 *
 *   is_admin()       wp-admin and admin-ajax.php: the update notice in the
 *                    plugin list, the details popup (`plugins_api`), "update
 *                    now" (Ajax action `update-plugin`), bulk updates through
 *                    update.php, the manual check link. WP_ADMIN is defined
 *                    before WordPress loads, so this is reliable on `init`.
 *   wp_doing_cron()  wp-cron.php defines DOING_CRON before WordPress loads,
 *                    whether a visitor, the server's cron job or a loopback
 *                    request calls it. That carries the library's own event
 *                    `puc_cron_check_updates-pcl-fluent-de`, core's
 *                    `wp_update_plugins` and the background auto-update
 *                    (`wp_maybe_auto_update`). The auto-update reads the
 *                    `update_plugins` transient and only finds this plugin
 *                    through the library's `site_transient_update_plugins`
 *                    filter, and installs through its `upgrader_*` hooks.
 *   WP-CLI           `wp plugin list|status|update` call wp_update_plugins(),
 *                    and the library hooks into exactly these commands to
 *                    run its own check first. (`wp cron event run` defines
 *                    DOING_CRON before loading WordPress and is covered by
 *                    the line above either way.)
 *
 * Left out on purpose: frontend, REST API, XML-RPC. Nothing there installs
 * plugin updates. The one thing lost is this plugin in the update count of
 * the toolbar a logged-in administrator sees on the frontend
 * (wp_get_update_data() reads the same transient); wp-admin shows it as
 * before. A remote management tool that updates through a frontend request
 * (MainWP, ManageWP, Jetpack) would no longer see the update. None of the
 * installations this plugin is made for runs one (checked 25.10.2026); if
 * one ever does, this condition has to learn its request.
 */
function pcl_fluent_de_update_pruefer_noetig() {
    return is_admin() || wp_doing_cron() || (defined('WP_CLI') && WP_CLI);
}

add_action('init', function () {
    if (!pcl_fluent_de_update_pruefer_noetig()) {
        return;
    }

    // Loaded here and not at file level, so a frontend request does not even
    // read the library's files.
    require_once __DIR__ . '/plugin-update-checker/plugin-update-checker.php';

    // On `init`, because the library prints translated messages and
    // WordPress 6.7+ complains about any earlier load_textdomain().
    // __FILE__ is still this file inside the closure: it is resolved at
    // compile time, not when the closure runs. The library needs the main
    // plugin file to find the folder and the installed version.
    $pruefer = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
        'https://github.com/blocoder/pcl-fluent-de/',
        __FILE__,
        'pcl-fluent-de'
    );

    // Ohne das lädt die Bibliothek den Quelltext-Tarball von GitHub — also den
    // Repo-Inhalt mit den .po, aber ohne die .mo und .l10n.php, die erst
    // package.sh erzeugt. Ein Update daraus wäre eine Installation ohne
    // Übersetzung. Das Muster nimmt gezielt das ZIP und nichts sonst.
    $pruefer->getVcsApi()->enableReleaseAssets('/\.zip($|[?&#])/i');
});

/* -------------------------------------------------------------------------
 * Die Teile
 * ---------------------------------------------------------------------- */

/**
 * Warum dieses Plugin überhaupt?
 *
 * WordPress lädt für eine Textdomain mehrere Kataloge und fragt sie der Reihe
 * nach ab. Der Controller nimmt die *erste* Datei, die den String kennt —
 * spätere füllen nur noch Lücken. Wer zuerst lädt, gewinnt. Dieses Plugin
 * lädt deshalb früh, und seine Kataloge wandern mit ihm von Installation zu
 * Installation statt verstreut in wp-content/languages/ zu liegen.
 *
 * Seit 2.0.0 steckt die Arbeit in drei Dateien statt in dieser einen:
 *
 *   registry.php   Was übersetzt wird und in welchem Zustand.
 *   laden.php      Der Ladeweg, die Sperre und die Extras.
 *   migration.php  Der Weg von den vier Vorgänger-Plugins hierher.
 *   einstellungen.php  Die Seite unter Einstellungen.
 *
 * Was ein Katalog nicht kann, liegt unter extras/ und wird nur gelesen, wenn
 * die zugehörige Domain aktiv ist.
 */
require_once __DIR__ . '/registry.php';
require_once __DIR__ . '/laden.php';
require_once __DIR__ . '/migration.php';
require_once __DIR__ . '/einstellungen.php';

/* -------------------------------------------------------------------------
 * Quellcode-Installation erkennen
 * ---------------------------------------------------------------------- */

/**
 * Stammt dieses Plugin aus dem Quellcode-Archiv statt aus dem Release?
 *
 * Im Repo stehen nur die `.po`; `.mo` und `.l10n.php` entstehen beim Bauen und
 * liegen allein im Release-Archiv. Wer auf der Repo-Startseite „Code → Download
 * ZIP“ nimmt, bekommt deshalb ein Plugin, das vollständig aussieht – der Ordner
 * `languages/` ist ja gefüllt – und trotzdem nichts übersetzt. Von außen ist
 * das nicht zu sehen; gemeldet von einem Nutzer am 20.09.2026, bei drei von
 * vier Plugins auf einmal.
 *
 * Erkennungszeichen ist der Ordner als Ganzes, nicht die Datei zur aktuellen
 * Locale: mindestens eine `.po`, aber kein einziger gebauter Katalog. Eine
 * fehlende Datei zu genau einer Locale ist etwas anderes und hat ihren eigenen
 * Hinweis.
 *
 * Kein `glob()`: Eckige Klammern im Installationspfad wären dort ein Muster.
 */
function pcl_fluent_de_quellinstallation() {
    static $ergebnis = null;

    if (null !== $ergebnis) {
        return $ergebnis;
    }

    $ergebnis = false;
    $dir      = plugin_dir_path(__FILE__) . 'languages/';

    if (!is_dir($dir)) {
        return $ergebnis;
    }

    $dateien = scandir($dir);

    if (!$dateien) {
        return $ergebnis;
    }

    $po = false;

    foreach ($dateien as $datei) {
        if (substr($datei, -3) === '.mo' || substr($datei, -9) === '.l10n.php') {
            return $ergebnis;
        }
        if (substr($datei, -3) === '.po') {
            $po = true;
        }
    }

    $ergebnis = $po;

    return $ergebnis;
}

/**
 * Der Link auf die Releases, aus denen das fertige Paket kommt.
 */
function pcl_fluent_de_release_link() {
    return sprintf(
        '<a href="https://github.com/blocoder/pcl-fluent-de/releases/latest" target="_blank" rel="noopener">%s</a>',
        esc_html__('Releases', 'pcl-fluent-de')
    );
}

/**
 * Hinweis im Backend, solange die gebauten Kataloge fehlen.
 *
 * Auf jeder Seite der Verwaltung und ohne Wegklicken: Das Plugin tut in diesem
 * Zustand gar nichts, und wer es installiert hat, merkt das sonst erst, wenn
 * ihm die englische Oberfläche auffällt. Zu sehen bekommt den Hinweis nur, wer
 * Plugins aktualisieren darf – alle anderen können ohnehin nichts ausrichten.
 */
function pcl_fluent_de_quellinstallation_hinweis() {
    if (!current_user_can('update_plugins') || !pcl_fluent_de_quellinstallation()) {
        return;
    }

    printf(
        '<div class="notice notice-warning"><p>%s</p></div>',
        wp_kses(
            sprintf(
                /* translators: %s: link to the releases page */
                __('<strong>PC’L Übersetzungen für Fluent-Plugins:</strong> Die gebauten Kataloge fehlen, das Plugin übersetzt deshalb nichts. Es stammt offenbar aus dem Quellcode-Archiv von GitHub („Code → Download ZIP“); darin stehen nur die Ausgangsdateien. Bitte das ZIP aus den %s herunterladen und unter Plugins → Installieren → Plugin hochladen darüberspielen.', 'pcl-fluent-de'),
                pcl_fluent_de_release_link()
            ),
            array(
                'strong' => array(),
                'a'      => array('href' => array(), 'target' => array(), 'rel' => array()),
            )
        )
    );
}
add_action('admin_notices', 'pcl_fluent_de_quellinstallation_hinweis');

/**
 * Hinweis im Plugin-Verzeichnis, welche Kataloge tatsächlich greifen.
 *
 * Ohne das ist von außen nicht erkennbar, ob eine Datei fehlt oder nur der
 * Dateiname nicht zur Locale passt.
 */
function pcl_fluent_de_row_meta($links, $file) {
    if (plugin_basename(__FILE__) !== $file) {
        return $links;
    }

    // Fehlt der ganze Satz gebauter Kataloge, ist die Locale nicht die
    // Ursache. Dann hilft nur der Hinweis auf das Release-Archiv.
    if (pcl_fluent_de_quellinstallation()) {
        $links[] = sprintf(
            /* translators: %s: link to the releases page */
            __('Kompilierte Kataloge fehlen – aus dem Quellcode-Archiv installiert, bitte das ZIP aus den %s einspielen', 'pcl-fluent-de'),
            pcl_fluent_de_release_link()
        );

        return $links;
    }

    $locale  = determine_locale();
    $dir     = pcl_fluent_de_languages_dir();
    $aktiv   = pcl_fluent_de_aktive_domains();
    $geladen = array();

    // Welche Datei gelesen wird, entscheidet nicht die Locale der Seite
    // allein: Bei erzwungener Anrede ist es die Datei der gewählten Anrede.
    foreach ($aktiv as $domain) {
        $datei = $domain . '-' . pcl_fluent_de_katalog_locale($domain, $locale) . '.mo';

        if (is_readable($dir . $datei)) {
            $geladen[] = $domain;
        }
    }

    if ($geladen) {
        $links[] = sprintf(
            /* translators: 1: locale, 2: comma separated list of text domains */
            esc_html__('Aktiv für %1$s: %2$s', 'pcl-fluent-de'),
            esc_html($locale),
            esc_html(implode(', ', $geladen))
        );
    } elseif ($aktiv) {
        // Domains sind eingeschaltet, aber keine Datei passt zur Locale.
        $links[] = sprintf(
            /* translators: %s: locale */
            esc_html__('Keine Kataloge für %s gefunden', 'pcl-fluent-de'),
            esc_html($locale)
        );
    } else {
        // Kein Katalog greift – entweder ist kein übersetztes Plugin da oder
        // alle Domains stehen auf „aus“ bzw. „englisch“.
        $links[] = esc_html__('Zurzeit greift kein Katalog', 'pcl-fluent-de');
    }

    return $links;
}
add_filter('plugin_row_meta', 'pcl_fluent_de_row_meta', 10, 2);

/**
 * „Einstellungen“ neben „Deaktivieren“ in der Plugin-Liste.
 *
 * Nicht `plugin_row_meta` — das ist die zweite Zeile mit „Details anzeigen“ und
 * den Hinweisen. Die Handlungen stehen in `plugin_action_links_<datei>`, und
 * die Vorgabe ist, den eigenen Eintrag **vorn** anzustellen: WordPress hängt
 * „Deaktivieren“ ans Ende, und dazwischen zu rutschen liest sich falsch.
 *
 * Der Filtername trägt den Dateinamen relativ zum Plugin-Ordner, deshalb
 * `plugin_basename(__FILE__)` und keine feste Zeichenkette — sonst greift der
 * Filter nicht mehr, sobald der Ordner anders heißt.
 */
function pcl_fluent_de_action_links($links) {
    $link = sprintf(
        '<a href="%s">%s</a>',
        esc_url(admin_url('options-general.php?page=pcl-fluent-de')),
        esc_html__('Einstellungen', 'pcl-fluent-de')
    );

    array_unshift($links, $link);

    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'pcl_fluent_de_action_links');
