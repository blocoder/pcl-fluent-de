<?php
/**
 * Plugin Name:       PC’L Übersetzungen für Fluent-Plugins
 * Plugin URI:        https://github.com/blocoder/pcl-fluent-de
 * Update URI:        https://github.com/blocoder/pcl-fluent-de
 * Description:       Liefert die deutschen Übersetzungen für FluentCommunity, FluentCommunity Pro, FluentMessaging und FluentPlayer aus. Lädt sie vor allen anderen Katalogen, damit die eigene Fassung gewinnt und mitgelieferte Sprachpakete nur noch Lücken füllen. Legt außerdem den Zustimmungs-Link bei der Registrierung auf die echte AGB-Seite.
 * Version:           1.6.0
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
require_once __DIR__ . '/plugin-update-checker/plugin-update-checker.php';

add_action('init', function () {
    // Erst auf `init`, weil die Bibliothek übersetzte Meldungen ausgibt und
    // WordPress 6.7+ jedes frühere load_textdomain() anmeckert.
    // __FILE__ bleibt auch in der Closure diese Datei — es wird beim Übersetzen
    // aufgelöst, nicht beim Aufruf. Die Bibliothek braucht die Hauptdatei des
    // Plugins, um den Ordner und den Versionsstand zu finden.
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

/**
 * Warum dieses Plugin überhaupt?
 *
 * WordPress lädt für eine Textdomain mehrere Kataloge und fragt sie der Reihe
 * nach ab. WP_Translation_Controller::locate_translation() nimmt die *erste*
 * Datei, die den String kennt — spätere füllen nur noch Lücken. Gemessen an
 * WordPress 7.0.2, nicht aus der Dokumentation abgeleitet.
 *
 * Daraus folgt: Wer zuerst lädt, gewinnt. Dieses Plugin lädt auf
 * `plugins_loaded` mit Priorität 1, also bevor FluentCommunity seine erste
 * Übersetzung anfordert und bevor das Sprachpaket von wordpress.org zum Zug
 * kommt.
 *
 * Das löst zwei Probleme auf einmal:
 *
 * 1. Die eigene Übersetzung ist gegen Sprachpaket-Updates immun. Bisher
 *    sickerte deren Terminologie über Lückenfüller ein — „Raum“ statt „Forum“.
 * 2. Die Kataloge sind portabel. Sie liegen nicht mehr verstreut in
 *    wp-content/languages/loco/, sondern hier im Plugin und wandern mit ihm
 *    von Installation zu Installation.
 *
 * Das Sprachpaket bleibt aktiv und füllt weiterhin Lücken. Das ist gewollt:
 * Es deckt rund 950 Strings ab, die sonst englisch blieben.
 *
 * Bis 1.5.3 hielt das Plugin außerdem drei fremde Textdomains englisch
 * (fluent-crm, fluentcampaign-pro, easy-code-manager, Filter
 * `pcl_fluent_de/blocked_domains`). Seit 1.6.0 kümmert es sich nur noch um
 * seine eigenen Übersetzungen. Wer eine Domain englisch halten will, braucht
 * dafür einen eigenen Filter auf `override_load_textdomain`; ein Beispiel
 * steht im README.
 */

/**
 * Die Textdomains, für die dieses Plugin Kataloge mitbringt.
 *
 * Bewusst nicht per glob() über den languages-Ordner ermittelt: Eine
 * versehentlich dort abgelegte Datei soll keine fremde Domain kapern.
 */
function pcl_fluent_de_domains() {
    return apply_filters('pcl_fluent_de/domains', array(
        'fluent-community',
        'fluent-community-pro',
        'fluent-messaging',
        'fluent-player',
        'fluent-player-pro',
    ));
}

/**
 * Lädt die eigenen Kataloge, bevor irgendjemand anders sie anfordert.
 */
function pcl_fluent_de_load() {
    $locale = determine_locale();
    $dir    = plugin_dir_path(__FILE__) . 'languages/';

    foreach (pcl_fluent_de_domains() as $domain) {
        // Kein Katalog für ein Plugin laden, das gar nicht da ist. Das Paket
        // bringt alle fünf Kataloge mit, installiert ist selten alles — fehlt
        // etwa fluent-player-pro, würde dessen .l10n.php sonst bei jedem
        // Aufruf für nichts gelesen.
        //
        // Die Ordnernamen entsprechen bei allen fünf genau der Textdomain.
        // Sollte das je auseinanderlaufen, hilft der Filter pcl_fluent_de/domains
        // nicht weiter — dann muss diese Prüfung angepasst werden. Deshalb steht
        // sie hier und nicht versteckt in einer Hilfsfunktion.
        if (!is_dir(WP_PLUGIN_DIR . '/' . $domain)) {
            continue;
        }

        $eigen = $dir . $domain . '-' . $locale . '.mo';

        if (!is_readable($eigen)) {
            continue;
        }

        // load_textdomain() statt load_plugin_textdomain(): Letzteres würde
        // zuerst in WP_LANG_DIR nachsehen und damit genau die Reihenfolge
        // herstellen, die wir vermeiden wollen.
        load_textdomain($domain, $eigen, $locale);

        // Und direkt danach das Sprachpaket von wordpress.org — falls vorhanden.
        //
        // Ohne diese Zeile gingen rund 950 Strings verloren. Grund:
        // _load_textdomain_just_in_time() überspringt jede Domain, die bereits
        // geladen ist. Wer früh lädt, verhindert damit ungewollt, dass
        // WordPress das Sprachpaket überhaupt noch anfasst. Gemessen am
        // 31.07.2026 — vorher füllte das Paket die Lücken, danach standen
        // „Target Lessons" und „Public only" wieder englisch da.
        //
        // Die Reihenfolge macht die Musik: Der Controller nimmt die erste
        // Datei, die den String kennt. Unsere steht davor und gewinnt, das
        // Paket kommt nur dort zum Zug, wo wir nichts haben. Damit ist die
        // eigene Terminologie sicher und die Abdeckung bleibt hoch.
        $paket = WP_LANG_DIR . '/plugins/' . $domain . '-' . $locale . '.mo';

        if (is_readable($paket)) {
            load_textdomain($domain, $paket, $locale);
        }
    }
}
add_action('plugins_loaded', 'pcl_fluent_de_load', 1);

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

    $locale  = determine_locale();
    $dir     = plugin_dir_path(__FILE__) . 'languages/';
    $geladen = array();

    foreach (pcl_fluent_de_domains() as $domain) {
        if (is_readable($dir . $domain . '-' . $locale . '.mo')) {
            $geladen[] = $domain;
        }
    }

    $links[] = $geladen
        ? sprintf(
            /* translators: 1: locale, 2: comma separated list of text domains */
            esc_html__('Aktiv für %1$s: %2$s', 'pcl-fluent-de'),
            esc_html($locale),
            esc_html(implode(', ', $geladen))
        )
        : sprintf(
            /* translators: %s: locale */
            esc_html__('Keine Kataloge für %s gefunden', 'pcl-fluent-de'),
            esc_html($locale)
        );

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

/* -------------------------------------------------------------------------
 * Block-Editor: zwei Stellen, die der Katalog nicht erreicht
 * ---------------------------------------------------------------------- */

/**
 * Die Reiterüberschrift im Block-Editor.
 *
 * FluentCommunity registriert für seinen Editor eigene Beitragstypen und gibt
 * ihnen die Beschriftung als festen Text mit, ohne `__()`:
 *
 *   'fcom-dummy'      => ['label' => 'Lesson', …]       (FluentBlockEditorHandler)
 *   'fcom-lockscreen' => ['label' => 'Lockscreen', …]
 *   'fcom-page'       => ['label' => 'Space Page', …]   (Pro, SpacePagesModule)
 *
 * Der Editor zeigt diese Beschriftung als Überschrift des ersten Reiters der
 * Seitenleiste. Kein Katalog kommt dort heran.
 *
 * Ersetzt wird nur, solange der Wert noch der bekannte englische Text ist.
 * Reicht der Hersteller die Beschriftung eines Tages durch `__()`, steht dort
 * etwas anderes, und dieser Filter hält sich heraus.
 *
 * Wo der Katalog das Wort schon kennt, kommt es von dort. „Foren-Seite“ gibt es
 * im Katalog nicht, weil der Hersteller den Begriff nirgends übersetzbar
 * anbietet – deshalb steht er hier, und nur für `de_DE`.
 */
function pcl_fluent_de_editor_post_types($post_types) {
    $ersatz = array(
        'fcom-dummy'      => array('Lesson', __('Lesson', 'fluent-community')),
        'fcom-lockscreen' => array('Lockscreen', __('Lock Screen', 'fluent-community')),
    );

    if (determine_locale() === 'de_DE') {
        $ersatz['fcom-page'] = array('Space Page', 'Foren-Seite');
    }

    foreach ($ersatz as $typ => $paar) {
        list($englisch, $deutsch) = $paar;

        if (!isset($post_types[$typ]['label']) || $post_types[$typ]['label'] !== $englisch) {
            continue;
        }

        // Ohne Übersetzung im Katalog kommt __() mit dem Original zurück;
        // dann bleibt alles, wie es war.
        if ($deutsch === '' || $deutsch === $englisch) {
            continue;
        }

        $post_types[$typ]['label'] = $deutsch;
    }

    return $post_types;
}
// Nach Pro (Priorität 10), das fcom-page erst hinzufügt.
add_filter('fluent_community/block_editor_post_types', 'pcl_fluent_de_editor_post_types', 20);

/**
 * „Enable comments“ in der Seitenleiste einer Foren-Seite.
 *
 * Der Block-Editor liest seine Texte nicht aus dem Katalog, sondern aus einer
 * eigenen Liste (`window.fcomEditorI18n`), die FluentBlockEditorHandler
 * zusammenstellt. Was dort fehlt, zeigt das Bundle englisch – auch wenn der
 * Katalog die Übersetzung längst kennt.
 *
 * In FluentCommunity 2.10.01 fehlt genau ein Schlüssel: `Enable comments` mit
 * kleinem c. Die Liste führt nur `Enable Comments` für die Lektionen. Gemessen
 * gegen alle 40 Texte, die das Bundle anfragt.
 *
 * Die Übersetzung kommt aus dem Katalog; hier wird nur die Lücke geschlossen.
 */
function pcl_fluent_de_editor_i18n($strings) {
    if (!isset($strings['Enable comments'])) {
        $strings['Enable comments'] = __('Enable comments', 'fluent-community');
    }

    return $strings;
}
add_filter('fluent_community/editor_i18n_strings', 'pcl_fluent_de_editor_i18n');

/* -------------------------------------------------------------------------
 * Zustimmungs-Link bei der Registrierung
 * ---------------------------------------------------------------------- */

/**
 * Warum das hier und nicht in der Übersetzung?
 *
 * FluentCommunity baut die Zustimmungs-Checkbox so:
 *
 *   $policyUrl = apply_filters('fluent_community/terms_policy_url', get_privacy_policy_url());
 *   __('I agree to the %1$s terms and conditions %2$s', 'fluent-community');
 *
 * Gemessen in Modules/Auth/AuthHelper.php::getTermsText(). Das Etikett spricht
 * von Nutzungsbedingungen, das Ziel ist die Datenschutzseite aus
 * Einstellungen → Datenschutz. Das ist kein Übersetzungsfehler und lässt sich
 * durch keine Formulierung heilen — der Link zeigt einfach woandershin.
 *
 * Reihenfolge, absteigend:
 *
 *   1. Konstante PCL_FLUENT_TERMS_URL aus der wp-config.php
 *   2. Option aus Einstellungen → PC’L Übersetzungen
 *   3. unverändert, also die WordPress-Datenschutzseite
 *
 * Die Konstante steht oben, damit eine Installation die Adresse festnageln
 * kann, ohne dass ein Klick im Backend sie wieder verstellt.
 */

const PCL_FLUENT_DE_TERMS_OPTION = 'pcl_fluent_de_terms_url';

/**
 * Die konfigurierte AGB-Adresse, oder ein leerer String.
 */
function pcl_fluent_de_terms_url() {
    if (defined('PCL_FLUENT_TERMS_URL') && PCL_FLUENT_TERMS_URL) {
        return (string) PCL_FLUENT_TERMS_URL;
    }

    return (string) get_option(PCL_FLUENT_DE_TERMS_OPTION, '');
}

function pcl_fluent_de_filter_terms_url($url) {
    $eigen = pcl_fluent_de_terms_url();

    return $eigen ? $eigen : $url;
}
add_filter('fluent_community/terms_policy_url', 'pcl_fluent_de_filter_terms_url');

/**
 * Und derselbe Link noch einmal, für den Fall, dass das Feld angefasst wurde.
 *
 * Der Filter oben reicht nämlich nicht. AuthenticationService::getAuthSettings()
 * ruft getTermsText() nur, solange `signup.form.fields.terms` leer ist:
 *
 *   if (!Arr::get($authSettings, 'signup.form.fields.terms')) {
 *       $termsText = AuthHelper::getTermsText();
 *       …
 *   }
 *   return apply_filters('fluent_community/auth/settings', $authSettings);
 *
 * Sobald jemand die Beschriftung im Backend einmal bearbeitet, liegt sie als
 * Freitext in der Datenbank (fcom_meta, Schluessel auth_settings) und laeuft
 * nie wieder durch eine Übersetzung oder einen Filter. Das Feld startet mit
 * einem Platzhalter-Link auf `#` — wer den Text anpasst und den Link vergisst,
 * hat eine Zustimmungs-Checkbox, die auf nichts zeigt. Genau so vorgefunden,
 * nicht ausgedacht.
 *
 * Deshalb hier der zweite Zugriff, auf dem Hook nach dem Zusammenfuehren.
 * Ersetzt wird ausschliesslich der Platzhalter — eine echte Adresse, die
 * jemand bewusst eingetragen hat, bleibt unangetastet.
 */
function pcl_fluent_de_filter_auth_settings($settings) {
    $eigen = pcl_fluent_de_terms_url();

    if (!$eigen || !is_array($settings)) {
        return $settings;
    }

    $feld = isset($settings['signup']['form']['fields']['terms'])
        ? $settings['signup']['form']['fields']['terms']
        : null;

    if (!is_array($feld)) {
        return $settings;
    }

    // Markdown-Fassung: [Nutzungsbedingungen](#)
    if (!empty($feld['label'])) {
        $feld['label'] = str_replace('](#)', '](' . $eigen . ')', $feld['label']);
    }

    // HTML-Fassung: <a href="#"> beziehungsweise <a href='#'>
    if (!empty($feld['inline_label'])) {
        $feld['inline_label'] = str_replace(
            array('href="#"', "href='#'"),
            'href="' . esc_url($eigen) . '"',
            $feld['inline_label']
        );
    }

    $settings['signup']['form']['fields']['terms'] = $feld;

    return $settings;
}
add_filter('fluent_community/auth/settings', 'pcl_fluent_de_filter_auth_settings');

/**
 * Erlaubt eine absolute http(s)-Adresse oder einen seiteninternen Pfad.
 *
 * Ein leerer Wert ist gültig und bedeutet „nichts überschreiben". Alles andere
 * — javascript:, data:, protokollrelative //host — fliegt raus, sonst wäre das
 * Feld ein offenes Scheunentor für jeden mit Zugriff auf die Einstellungen.
 */
function pcl_fluent_de_sanitize_terms_url($wert) {
    $wert = trim((string) $wert);

    if ('' === $wert) {
        return '';
    }

    if (0 === strpos($wert, '/') && 0 !== strpos($wert, '//')) {
        return esc_url_raw($wert, array('http', 'https'));
    }

    $schema = wp_parse_url($wert, PHP_URL_SCHEME);

    if (!in_array($schema, array('http', 'https'), true)) {
        add_settings_error(
            PCL_FLUENT_DE_TERMS_OPTION,
            'pcl_fluent_de_terms_url',
            esc_html__('Die Adresse muss mit http:// oder https:// beginnen oder ein Pfad wie /nutzungsbedingungen sein.', 'pcl-fluent-de')
        );

        return (string) get_option(PCL_FLUENT_DE_TERMS_OPTION, '');
    }

    return esc_url_raw($wert, array('http', 'https'));
}

function pcl_fluent_de_register_settings() {
    register_setting('pcl_fluent_de', PCL_FLUENT_DE_TERMS_OPTION, array(
        'type'              => 'string',
        'default'           => '',
        'sanitize_callback' => 'pcl_fluent_de_sanitize_terms_url',
    ));
}
add_action('admin_init', 'pcl_fluent_de_register_settings');

function pcl_fluent_de_settings_page() {
    add_options_page(
        esc_html__('PC’L Übersetzungen', 'pcl-fluent-de'),
        esc_html__('PC’L Übersetzungen', 'pcl-fluent-de'),
        'manage_options',
        'pcl-fluent-de',
        'pcl_fluent_de_render_settings'
    );
}
add_action('admin_menu', 'pcl_fluent_de_settings_page');

function pcl_fluent_de_render_settings() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $fest = defined('PCL_FLUENT_TERMS_URL') && PCL_FLUENT_TERMS_URL;
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('PC’L Übersetzungen für Fluent-Plugins', 'pcl-fluent-de'); ?></h1>

        <p>
            <?php echo esc_html__('FluentCommunity verlinkt die Zustimmungs-Checkbox bei der Registrierung auf die Datenschutzseite aus Einstellungen → Datenschutz, beschriftet sie aber mit „Allgemeine Geschäftsbedingungen". Hier lässt sich das Ziel auf die tatsächliche AGB-Seite legen.', 'pcl-fluent-de'); ?>
        </p>

        <?php if ($fest) : ?>
            <div class="notice notice-info inline">
                <p>
                    <?php
                    printf(
                        /* translators: %s: the URL set in wp-config.php */
                        esc_html__('Die Adresse steht als Konstante PCL_FLUENT_TERMS_URL in der wp-config.php und lautet %s. Sie hat Vorrang, das Feld unten bleibt wirkungslos.', 'pcl-fluent-de'),
                        '<code>' . esc_html(PCL_FLUENT_TERMS_URL) . '</code>'
                    );
                    ?>
                </p>
            </div>
        <?php endif; ?>

        <form action="options.php" method="post">
            <?php settings_fields('pcl_fluent_de'); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="pcl_fluent_de_terms_url"><?php echo esc_html__('Adresse der Nutzungsbedingungen', 'pcl-fluent-de'); ?></label>
                    </th>
                    <td>
                        <input
                            type="text"
                            class="regular-text code"
                            id="pcl_fluent_de_terms_url"
                            name="<?php echo esc_attr(PCL_FLUENT_DE_TERMS_OPTION); ?>"
                            value="<?php echo esc_attr(get_option(PCL_FLUENT_DE_TERMS_OPTION, '')); ?>"
                            placeholder="/nutzungsbedingungen"
                        />
                        <p class="description">
                            <?php echo esc_html__('Vollständige Adresse oder seiteninterner Pfad. Leer lassen, damit es bei der Datenschutzseite bleibt.', 'pcl-fluent-de'); ?>
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>

        <h2><?php echo esc_html__('Was gerade greift', 'pcl-fluent-de'); ?></h2>
        <p>
            <?php
            $aktuell = apply_filters('fluent_community/terms_policy_url', get_privacy_policy_url());
            if ($aktuell) {
                printf(
                    /* translators: %s: the URL the consent checkbox currently links to */
                    esc_html__('Die Zustimmungs-Checkbox verlinkt auf %s', 'pcl-fluent-de'),
                    '<code>' . esc_html($aktuell) . '</code>'
                );
            } else {
                echo esc_html__('Es ist keine Adresse gesetzt — die Checkbox erscheint ohne Link.', 'pcl-fluent-de');
            }
            ?>
        </p>
    </div>
    <?php
}
