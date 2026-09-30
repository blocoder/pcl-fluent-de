<?php
/**
 * Texte, die am Katalog vorbeilaufen: das CRM-Profil in FluentCommunity und
 * das Suchfenster der Werkzeugleiste.
 *
 * Übernommen am 30.09.2026 aus `pcl-fluentcrm-de` 1.3.0 (dort seit dessen
 * 1.2.0, 17.09.2026), zusammen mit `fluentcrm-datum.php` und aus demselben
 * Grund: Beide wirken nur auf die kostenlose FluentCRM-Oberfläche.
 *
 * 1. CRM-Profil in der Seitenleiste eines FluentCommunity-Profils.
 *    FluentCommunity Pro baut das Widget
 *    (`Integrations/FluentCRM/ContactAdvancedFilter::pushCrmProfile()`, Filter
 *    `fluent_community/activity/after_contents_user`, Priorität 10). Zwei
 *    Stellen gibt es dort ohne gettext aus: den Status auf dem Knopf
 *    (`ucfirst($profile->status)`, also „Unsubscribed“) und die drei Zähler
 *    (`ucfirst()` der Schlüssel aus `Subscriber::stats()`, „Emails“, „Opens“,
 *    „Clicks“). Dieser Filter läuft danach und ersetzt beides im fertigen
 *    HTML – den Status mit den Beschriftungen, die FluentCRM selbst dafür
 *    übersetzt (`fluentcrm_subscriber_statuses(true)`), die Zähler mit den
 *    vorhandenen Katalogeinträgen. Auf einer englischen Seite liefern beide
 *    den Originaltext, es ändert sich nichts. In FluentCommunity Pro 2.11.0
 *    unverändert (nachgemessen am 30.09.2026).
 *
 * 2. Suchfenster „Kontakte suchen“ in der Werkzeugleiste.
 *    Das Bundle schlägt seine Texte in `fcrm_adminbar_search_vars.trans` nach.
 *    Drei davon fehlen dort: „Search in CRM“ und „Best Matches“ gar nicht,
 *    „Quick links“ steht nur als „Quick Links“ (großes L) in der Map. Die
 *    fehlenden Schlüssel werden vor dem Bundle nachgetragen; ein Schlüssel,
 *    den FluentCRM selbst liefert, bleibt unangetastet. Die beiden ersten gibt
 *    es in keinem Katalog, sie stehen deshalb hier und gelten nur bei
 *    deutscher Locale – anredefrei, also für Du und Sie gleich. Sonst gilt
 *    weiter: Lücken der großen Übersetzungs-Map werden gemeldet, nicht
 *    ergänzt (Entscheidung vom 17.09.2026); dieses Fenster ist die bewusste
 *    Ausnahme. In 3.2.5 liegt das Bundle unter `assets/admin/adminbar-search.js`
 *    statt unter `admin/js/`; der Skript-Handle ist derselbe geblieben.
 *
 * Abschaltbar über den Filter `pcl_fluentcrm_de/ausgaben_deutsch` – der Name
 * bleibt wie gehabt, nur die Funktionen heißen anders.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Liefert das alte Plugin diese Ausgaben noch selbst?
 *
 * Dieselbe Weiche wie in `fluentcrm-datum.php`, samt Begründung dort. Sie
 * steht in beiden Dateien unter `function_exists`, damit keine von beiden auf
 * die Ladereihenfolge der anderen angewiesen ist.
 *
 * @return bool
 */
if (!function_exists('pcl_fluent_de_crm_vorgaenger_liefert')) {
    function pcl_fluent_de_crm_vorgaenger_liefert() {
        return function_exists('pcl_fluentcrm_de_dayjs_ausgeben');
    }
}

/**
 * Status und Zähler im CRM-Profil übersetzen.
 *
 * @param string $content Bisheriger Inhalt der Seitenleiste.
 * @return string
 */
function pcl_fluent_de_crm_profil($content) {
    if (pcl_fluent_de_crm_vorgaenger_liefert()) {
        return $content;
    }
    if (!is_string($content) || false === strpos($content, 'fc_stats')) {
        return $content;
    }
    if (!function_exists('fluentcrm_subscriber_statuses') || !apply_filters('pcl_fluentcrm_de/ausgaben_deutsch', true)) {
        return $content;
    }

    // Status button: the link to the contact carries the raw status as text.
    $status = array();
    foreach ((array) fluentcrm_subscriber_statuses(true) as $eintrag) {
        if (isset($eintrag['slug'], $eintrag['title'])) {
            $status[ucfirst($eintrag['slug'])] = $eintrag['title'];
        }
    }
    $content = preg_replace_callback(
        '~(<a\b[^>]*href="[^"]*subscribers/\d+"[^>]*>)(\s*)([A-Za-z]+)(\s*</a>)~',
        function ($m) use ($status) {
            if (!isset($status[$m[3]])) {
                return $m[0];
            }
            return $m[1] . $m[2] . esc_html($status[$m[3]]) . $m[4];
        },
        $content,
        1
    );

    // Counters: "<span>Emails: 0</span>" and so on.
    $zaehler = array(
        'Emails' => __('Emails', 'fluent-crm'),
        'Opens'  => __('Opens', 'fluent-crm'),
        'Clicks' => __('Clicks', 'fluent-crm'),
    );
    foreach ($zaehler as $englisch => $deutsch) {
        $content = str_replace('<span>' . $englisch . ': ', '<span>' . esc_html($deutsch) . ': ', $content);
    }

    return $content;
}
add_filter('fluent_community/activity/after_contents_user', 'pcl_fluent_de_crm_profil', 20);

/**
 * Fehlende Texte des Suchfensters vor dem Bundle nachtragen.
 */
function pcl_fluent_de_crm_suchfenster() {
    if (pcl_fluent_de_crm_vorgaenger_liefert()) {
        return;
    }
    if (!wp_script_is('fluentcrm_adminbar_search', 'enqueued')) {
        return;
    }
    if (0 !== strpos(determine_locale(), 'de_') || !apply_filters('pcl_fluentcrm_de/ausgaben_deutsch', true)) {
        return;
    }
    $texte = array(
        'Search in CRM' => 'Im CRM suchen',
        'Best Matches'  => 'Beste Treffer',
        'Quick links'   => __('Quick Links', 'fluent-crm'),
    );
    $js = '(function(){var v=window.fcrm_adminbar_search_vars;if(!v){return;}'
        . 'v.trans=v.trans||{};var t=' . wp_json_encode($texte) . ';'
        . 'Object.keys(t).forEach(function(k){if(!v.trans[k]){v.trans[k]=t[k];}});})();';
    wp_add_inline_script('fluentcrm_adminbar_search', $js, 'before');
}
// FluentCRM enqueues the bundle on admin_bar_menu with priority 999.
add_action('admin_bar_menu', 'pcl_fluent_de_crm_suchfenster', 1000);
