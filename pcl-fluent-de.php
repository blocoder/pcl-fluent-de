<?php
/**
 * Plugin Name:       PC’L Übersetzungen für Fluent-Plugins
 * Plugin URI:        https://github.com/blocoder/pcl-fluent-de
 * Description:       Liefert die deutschen Übersetzungen für FluentCommunity, FluentCommunity Pro, FluentMessaging und FluentPlayer aus. Lädt sie vor allen anderen Katalogen, damit die eigene Fassung gewinnt und mitgelieferte Sprachpakete nur noch Lücken füllen. Legt außerdem den Zustimmungs-Link bei der Registrierung auf die echte AGB-Seite.
 * Version:           1.4.3
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
 * Textdomains, die englisch bleiben sollen.
 *
 * Nicht jede Übersetzung ist ein Gewinn. Für diese Plugins ist die deutsche
 * Fassung — ob mitgeliefert oder als Sprachpaket von wordpress.org — schlechter
 * als das Original, und ein Sprachpaket kann jederzeit unbemerkt dazukommen.
 * Hier wird das Laden unterbunden, statt hinterher Dateien wegzuräumen.
 */
function pcl_fluent_de_blocked_domains() {
    return apply_filters('pcl_fluent_de/blocked_domains', array(
        'fluent-crm',
        'fluentcampaign-pro',
        'easy-code-manager',
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
 * Blockierte Domains gar nicht erst laden lassen.
 *
 * load_textdomain() fragt zuerst diesen Filter. Wer true zurückgibt, sagt
 * damit „ist erledigt" — die Datei wird nicht gelesen, und __() liefert das
 * englische Original. Das greift auch für _load_textdomain_just_in_time(),
 * weil das denselben Weg nimmt.
 *
 * Priorität 1, also vor Loco Translate. Das ist nicht kosmetisch: Loco hängt
 * sich in denselben Filter und lädt seine Datei im Callback. Ein spätes true
 * käme zu spät — geladen ist geladen.
 *
 * Ob ein frühes true reicht, hängt daran, ob der spätere Callback den
 * hereingereichten Wert respektiert. Gemessen an Loco Translate 2.8.7: Er tut
 * es. Mit Priorität 1 bleibt eine blockierte Domain englisch, auch wenn im
 * Loco-Ordner noch ein Katalog dafür liegt. Verlassen sollte man sich darauf
 * trotzdem nicht auf Dauer — nach einem Loco-Update gehört es nachgeprüft:
 *
 *     wp eval "var_dump(is_textdomain_loaded('fluent-crm'));"
 */
function pcl_fluent_de_block_textdomain($override, $domain) {
    if (in_array($domain, pcl_fluent_de_blocked_domains(), true)) {
        return true;
    }

    return $override;
}
add_filter('override_load_textdomain', 'pcl_fluent_de_block_textdomain', 1, 2);

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
