<?php
/**
 * Die Einstellungsseite: eine Zeile je Übersetzung.
 *
 * Bis 2.0.0 gab es hier nur ein Feld, und es stand in der FluentCommunity-
 * Zusatzdatei — auf einer Installation ohne FluentCommunity hatte das Plugin
 * damit gar keine Einstellungsseite. Seit 2.1.0 gehört die Seite dem Plugin,
 * und was ein einzelnes Plugin beisteuert, hängt sich über
 * `pcl_fluent_de/einstellungen_felder` ein.
 *
 * Beide Optionen liegen in derselben Settings-Gruppe `pcl_fluent_de`. Deshalb
 * genügt ein Formular und ein Speichern-Knopf, auch wenn die Felder aus
 * verschiedenen Dateien kommen.
 */

if (!defined('ABSPATH')) {
    exit;
}

const PCL_FLUENT_DE_ZUSTAND_OPTION = 'pcl_fluent_de_zustand';
const PCL_FLUENT_DE_ANREDE_OPTION  = 'pcl_fluent_de_anrede';

/**
 * Die beiden Optionen für die Settings API anmelden.
 */
function pcl_fluent_de_zustand_registrieren() {
    register_setting('pcl_fluent_de', PCL_FLUENT_DE_ZUSTAND_OPTION, array(
        'type'              => 'array',
        'sanitize_callback' => 'pcl_fluent_de_zustand_saeubern',
        'default'           => array(),
    ));

    register_setting('pcl_fluent_de', PCL_FLUENT_DE_ANREDE_OPTION, array(
        'type'              => 'array',
        'sanitize_callback' => 'pcl_fluent_de_anrede_saeubern',
        'default'           => array(),
    ));
}
add_action('admin_init', 'pcl_fluent_de_zustand_registrieren');

/**
 * Nur Anreden, die es für die Domain wirklich gibt.
 *
 * Eine Auswahl, die der Katalog nicht hergibt, würde beim Laden ins Leere
 * zeigen. Der leere Wert („wie die Seite“) wird nicht gespeichert — dieselbe
 * Regel wie bei `auto`: Die Option enthält nur, was abweicht.
 */
function pcl_fluent_de_anrede_saeubern($eingabe) {
    $sauber = array();

    foreach ((array) $eingabe as $domain => $locale) {
        $eintrag = pcl_fluent_de_eintrag($domain);

        if (!$eintrag || !$locale) {
            continue;
        }

        if (!in_array($locale, $eintrag['anreden'], true)) {
            continue;
        }

        $sauber[$domain] = $locale;
    }

    return $sauber;
}

/**
 * Nur bekannte Domains, nur gültige Zustände, und `auto` fliegt raus.
 *
 * Das Entfernen von `auto` ist kein Schönheitsgriff: Die Option wird auf jedem
 * Request gelesen, und sie soll nur enthalten, was vom Normalfall abweicht.
 * Eine Installation, auf der nichts umgestellt wurde, hat am Ende ein leeres
 * Array — und das löscht `update_option` nicht, aber es kostet auch nichts.
 */
function pcl_fluent_de_zustand_saeubern($eingabe) {
    $sauber   = array();
    $gueltig  = pcl_fluent_de_zustaende();
    $registry = pcl_fluent_de_registry();

    foreach ((array) $eingabe as $domain => $zustand) {
        if (!isset($registry[$domain])) {
            continue;
        }

        if (!in_array($zustand, $gueltig, true) || $zustand === 'auto') {
            continue;
        }

        $sauber[$domain] = $zustand;
    }

    return $sauber;
}

/**
 * Der Menüpunkt. Unter Einstellungen, wie bisher.
 */
function pcl_fluent_de_menue() {
    add_options_page(
        esc_html__('PC’L Übersetzungen', 'pcl-fluent-de'),
        esc_html__('PC’L Übersetzungen', 'pcl-fluent-de'),
        'manage_options',
        'pcl-fluent-de',
        'pcl_fluent_de_seite'
    );
}
add_action('admin_menu', 'pcl_fluent_de_menue');

/**
 * Was in der Spalte „Status“ steht.
 *
 * Drei Fälle, die der Mensch auseinanderhalten können muss: Das Plugin fehlt,
 * ein Vorgänger bedient die Domain noch, oder es läuft.
 */
function pcl_fluent_de_status_text($domain) {
    if (!pcl_fluent_de_plugin_da($domain)) {
        return array('nicht installiert', 'dashicons-minus', '#8c8f94');
    }

    if (pcl_fluent_de_vorgaenger_aktiv($domain)) {
        return array('vom Vorgänger bedient', 'dashicons-warning', '#996800');
    }

    if (in_array($domain, pcl_fluent_de_aktive_domains(), true)) {
        return array('deutsch', 'dashicons-yes-alt', '#00794a');
    }

    if (pcl_fluent_de_zustand($domain) === 'englisch') {
        return array('keine Übersetzung', 'dashicons-translation', '#646970');
    }

    return array('fremde Übersetzung', 'dashicons-admin-site-alt3', '#646970');
}

function pcl_fluent_de_seite() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $zustaende = array(
        'auto'     => __('Deutsche Übersetzung aktiv', 'pcl-fluent-de'),
        'aus'      => __('Fremde Übersetzung (WordPress, Plugin)', 'pcl-fluent-de'),
        'englisch' => __('Keine Übersetzung zulassen', 'pcl-fluent-de'),
    );
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('PC’L Übersetzungen für Fluent-Plugins', 'pcl-fluent-de'); ?></h1>

        <form action="options.php" method="post">
            <?php settings_fields('pcl_fluent_de'); ?>

            <h2><?php echo esc_html__('Übersetzungen', 'pcl-fluent-de'); ?></h2>

            <p class="description" style="max-width:52em">
                <?php echo esc_html__('Jede Übersetzung schaltet sich selbst ein, sobald ihr Plugin installiert ist. „Fremde Übersetzung“ nimmt die eigene Fassung weg — dann greift, was WordPress oder das Plugin selbst mitbringt, falls es etwas gibt. „Keine Übersetzung zulassen“ hält jede deutsche Fassung fern, das Plugin bleibt englisch.', 'pcl-fluent-de'); ?>
            </p>

            <table class="widefat striped" style="max-width:62em;margin-top:1em">
                <thead>
                    <tr>
                        <th scope="col"><?php echo esc_html__('Plugin', 'pcl-fluent-de'); ?></th>
                        <th scope="col"><?php echo esc_html__('Übersetzung', 'pcl-fluent-de'); ?></th>
                        <th scope="col"><?php echo esc_html__('Anrede', 'pcl-fluent-de'); ?></th>
                        <th scope="col"><?php echo esc_html__('Status', 'pcl-fluent-de'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach (pcl_fluent_de_registry() as $domain => $_) :
                    $eintrag = pcl_fluent_de_eintrag($domain);
                    $da      = pcl_fluent_de_plugin_da($domain);
                    $zustand = pcl_fluent_de_zustand($domain);
                    $feld    = PCL_FLUENT_DE_ZUSTAND_OPTION . '[' . $domain . ']';
                    list($status, $icon, $farbe) = pcl_fluent_de_status_text($domain);
                    ?>
                    <tr>
                        <td>
                            <strong><?php echo esc_html($eintrag['titel']); ?></strong><br />
                            <code style="font-size:11px"><?php echo esc_html($domain); ?></code>
                        </td>
                        <td>
                            <?php if ($da) : ?>
                                <select name="<?php echo esc_attr($feld); ?>">
                                    <?php foreach ($zustaende as $wert => $beschriftung) : ?>
                                        <option value="<?php echo esc_attr($wert); ?>" <?php selected($zustand, $wert); ?>>
                                            <?php echo esc_html($beschriftung); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else : ?>
                                <select disabled>
                                    <option><?php echo esc_html($zustaende[$zustand]); ?></option>
                                </select>
                                <?php
                                // Der gespeicherte Zustand geht sonst verloren: Ein
                                // deaktiviertes Feld sendet nichts, und was nicht
                                // gesendet wird, fehlt beim Speichern. Wer das Plugin
                                // später installiert, soll seine Einstellung
                                // wiederfinden.
                                if ($zustand !== 'auto') :
                                    ?>
                                    <input type="hidden" name="<?php echo esc_attr($feld); ?>" value="<?php echo esc_attr($zustand); ?>" />
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $anredefeld = PCL_FLUENT_DE_ANREDE_OPTION . '[' . $domain . ']';
                            $anrede     = pcl_fluent_de_anrede($domain);

                            if (!pcl_fluent_de_hat_anredewahl($domain)) {
                                // Nur eine Fassung: nichts zu wählen, nur zu zeigen.
                                echo esc_html(
                                    in_array('de_DE_formal', $eintrag['anreden'], true)
                                        ? __('nur Sie', 'pcl-fluent-de')
                                        : __('nur Du', 'pcl-fluent-de')
                                );
                            } elseif (!$da) {
                                echo esc_html__('Du, Sie', 'pcl-fluent-de');
                                if ($anrede) {
                                    ?>
                                    <input type="hidden" name="<?php echo esc_attr($anredefeld); ?>" value="<?php echo esc_attr($anrede); ?>" />
                                    <?php
                                }
                            } else {
                                ?>
                                <select name="<?php echo esc_attr($anredefeld); ?>">
                                    <option value="" <?php selected($anrede, ''); ?>>
                                        <?php echo esc_html__('wie die Seite', 'pcl-fluent-de'); ?>
                                    </option>
                                    <option value="de_DE" <?php selected($anrede, 'de_DE'); ?>>
                                        <?php echo esc_html__('immer Du', 'pcl-fluent-de'); ?>
                                    </option>
                                    <option value="de_DE_formal" <?php selected($anrede, 'de_DE_formal'); ?>>
                                        <?php echo esc_html__('immer Sie', 'pcl-fluent-de'); ?>
                                    </option>
                                </select>
                                <?php
                            }
                            ?>
                        </td>
                        <td style="color:<?php echo esc_attr($farbe); ?>">
                            <span class="dashicons <?php echo esc_attr($icon); ?>" style="vertical-align:text-bottom"></span>
                            <?php echo esc_html($status); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <?php
            /**
             * Felder, die ein einzelnes Plugin beisteuert.
             *
             * Läuft innerhalb des Formulars, damit die Felder mit demselben
             * Knopf gespeichert werden. Wer sich einhängt, muss seine Option
             * in der Gruppe `pcl_fluent_de` registriert haben.
             */
            do_action('pcl_fluent_de/einstellungen_felder');
            ?>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
