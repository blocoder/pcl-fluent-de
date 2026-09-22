<?php
/**
 * Der Weg von vier Plugins zu einem.
 *
 * Bis 1.7.1 gab es neben diesem Plugin drei Geschwister, die nach demselben
 * Muster gebaut waren und je eine Domain bedienten: pcl-fluentauth-de,
 * pcl-fluentsmtp-de und pcl-fluentsnippets-de. Seit 2.0.0 sind ihre Kataloge,
 * ihre Zusatzlogik und ihre Domains hier.
 *
 * Bleiben sie aktiv, laden zwei Plugins denselben Katalog für dieselbe Domain.
 * Kaputt geht dabei nichts — wer zuerst lädt, gewinnt, und beide bringen
 * dieselbe Übersetzung mit. Verschwendet wird trotzdem: der doppelte
 * Speicher, der doppelte Update-Prüfer und vier GitHub-Abfragen statt einer.
 *
 * Deshalb legt dieses Plugin sie still, sobald es aktiviert wird. Deaktivieren
 * statt löschen: Was mit den Ordnern geschieht, entscheidet der Mensch.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Die Vorgänger, mit ihrem Pfad relativ zum Plugin-Ordner.
 */
function pcl_fluent_de_vorgaenger() {
    return array(
        'pcl-fluentauth-de/pcl-fluentauth-de.php'           => 'PC’L Übersetzungen für FluentAuth',
        'pcl-fluentsmtp-de/pcl-fluentsmtp-de.php'           => 'PC’L Übersetzungen für FluentSMTP',
        'pcl-fluentsnippets-de/pcl-fluentsnippets-de.php'   => 'PC’L Übersetzungen für FluentSnippets',
    );
}

/**
 * Welche Vorgänger sind gerade aktiv?
 */
function pcl_fluent_de_aktive_vorgaenger() {
    if (!function_exists('is_plugin_active')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $aktiv = array();

    foreach (pcl_fluent_de_vorgaenger() as $datei => $titel) {
        if (is_plugin_active($datei)) {
            $aktiv[$datei] = $titel;
        }
    }

    return $aktiv;
}

/**
 * Beim Aktivieren die Vorgänger stilllegen.
 *
 * `deactivate_plugins()` mit $silent = false, damit deren eigene
 * Deaktivierungs-Routinen laufen. Keines der drei hat eine, aber das kann sich
 * ändern, und ein stilles Abschalten wäre dann ein Fehler, den niemand sieht.
 *
 * Was abgeschaltet wurde, landet in einer Option und wird einmal im Backend
 * gemeldet. Ohne diese Meldung stünde in der Plugin-Liste plötzlich „inaktiv“,
 * ohne dass jemand es war.
 */
function pcl_fluent_de_aktivierung() {
    $abgeschaltet = pcl_fluent_de_aktive_vorgaenger();

    if (!$abgeschaltet) {
        return;
    }

    deactivate_plugins(array_keys($abgeschaltet), false);
    update_option('pcl_fluent_de_migration_hinweis', array_values($abgeschaltet), false);
}
register_activation_hook(
    plugin_dir_path(__FILE__) . 'pcl-fluent-de.php',
    'pcl_fluent_de_aktivierung'
);

/**
 * Der Hinweis nach der Zusammenführung, einmal.
 */
function pcl_fluent_de_migration_hinweis() {
    if (!current_user_can('activate_plugins')) {
        return;
    }

    $titel = (array) get_option('pcl_fluent_de_migration_hinweis', array());

    if (!$titel) {
        return;
    }

    echo '<div class="notice notice-info is-dismissible"><p>';
    echo esc_html__('PC’L Übersetzungen für Fluent-Plugins bringt seit 2.0.0 alle Kataloge selbst mit. Diese Plugins wurden deshalb deaktiviert:', 'pcl-fluent-de');
    echo ' <strong>' . esc_html(implode(', ', $titel)) . '</strong>. ';
    echo esc_html__('Ihre Ordner liegen unverändert da und können gelöscht werden.', 'pcl-fluent-de');
    echo '</p></div>';

    delete_option('pcl_fluent_de_migration_hinweis');
}
add_action('admin_notices', 'pcl_fluent_de_migration_hinweis');

/**
 * Und falls doch einer wieder aktiv wird: dauerhafter Hinweis.
 *
 * Der Aktivierungs-Hook feuert nur einmal. Wer einen Vorgänger später von Hand
 * einschaltet — oder ihn über ein Backup zurückbekommt —, hätte sonst wieder
 * zwei Plugins auf derselben Domain, ohne Hinweis darauf.
 */
function pcl_fluent_de_doppelt_hinweis() {
    if (!current_user_can('activate_plugins')) {
        return;
    }

    $aktiv = pcl_fluent_de_aktive_vorgaenger();

    if (!$aktiv) {
        return;
    }

    echo '<div class="notice notice-warning"><p>';
    echo esc_html(
        sprintf(
            /* translators: %s: comma-separated list of plugin names */
            __('Doppelte Übersetzung: %s ist aktiv und bedient dieselben Textdomains wie PC’L Übersetzungen für Fluent-Plugins. Das kostet Speicher, ohne etwas zu verbessern — das ältere Plugin kann deaktiviert werden.', 'pcl-fluent-de'),
            implode(', ', $aktiv)
        )
    );
    echo '</p></div>';
}
add_action('admin_notices', 'pcl_fluent_de_doppelt_hinweis');
