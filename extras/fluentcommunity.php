<?php
/**
 * Was der Katalog bei FluentCommunity nicht erreicht.
 *
 * Drei Dinge, die sich nicht übersetzen lassen, weil sie nicht durch
 * `__()` laufen: die Beschriftungen der Block-Editor-Beitragstypen, die
 * Textliste des Editor-Bundles und der Zustimmungs-Link bei der
 * Registrierung. Dazu die Einstellungsseite für dessen Adresse.
 *
 * Diese Datei wird nur eingebunden, wenn FluentCommunity installiert und die
 * Übersetzung dafür eingeschaltet ist — siehe `extras` in der Registry.
 * Bis 1.7.1 stand alles davon in der Hauptdatei und lief auf jeder
 * Installation mit, auch ohne FluentCommunity.
 */

if (!defined('ABSPATH')) {
    exit;
}

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

/**
 * Das Feld für die AGB-Adresse, eingehängt in die Einstellungsseite.
 *
 * Bis 2.0.0 baute diese Datei die ganze Seite — Menüpunkt, Formular,
 * Speichern-Knopf. Das war der falsche Ort: Ohne FluentCommunity wurde sie
 * nicht geladen, und damit hatte das Plugin auf einer solchen Installation
 * gar keine Einstellungen. Seit 2.1.0 gehört die Seite dem Plugin
 * (einstellungen.php), und hier steht nur noch, was FluentCommunity angeht.
 */
function pcl_fluent_de_terms_feld() {
    $fest = defined('PCL_FLUENT_TERMS_URL') && PCL_FLUENT_TERMS_URL;
    ?>
    <h2><?php echo esc_html__('Zustimmungs-Link bei der Registrierung', 'pcl-fluent-de'); ?></h2>

    <p class="description" style="max-width:52em">
        <?php echo esc_html__('FluentCommunity verlinkt die Zustimmungs-Checkbox auf die Datenschutzseite aus Einstellungen → Datenschutz, beschriftet sie aber mit „Allgemeine Geschäftsbedingungen“. Hier lässt sich das Ziel auf die tatsächliche AGB-Seite legen.', 'pcl-fluent-de'); ?>
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
                <?php
                $aktuell = apply_filters('fluent_community/terms_policy_url', get_privacy_policy_url());
                echo '<p class="description">';
                if ($aktuell) {
                    printf(
                        /* translators: %s: the URL the consent checkbox currently links to */
                        esc_html__('Aktuell verlinkt die Checkbox auf %s', 'pcl-fluent-de'),
                        '<code>' . esc_html($aktuell) . '</code>'
                    );
                } else {
                    echo esc_html__('Es ist keine Adresse gesetzt — die Checkbox erscheint ohne Link.', 'pcl-fluent-de');
                }
                echo '</p>';
                ?>
            </td>
        </tr>
    </table>
    <?php
}
add_action('pcl_fluent_de/einstellungen_felder', 'pcl_fluent_de_terms_feld');
