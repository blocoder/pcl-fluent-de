<?php
/**
 * Seit 2.6.17: Schlüssel, die die Oberfläche von FluentCRM nachschlägt, die
 * aber in deren Map fehlen.
 *
 * Die Vue-App schlägt jeden Text in `window.fcAdmin.trans` nach, gefüllt aus
 * `TransStrings::getStrings()` und durchgereicht über den Filter
 * `fluent_crm/admin_vars` (Schlüssel `trans`). Fehlt ein Schlüssel, zeigt sie
 * das englische Original, egal was im Katalog steht.
 *
 * FluentCRM 3.3.0 hat in `TransStrings.php` „Custom Fields“ in „Custom fields“
 * umbenannt, drei Bundles fragen aber weiter nach „Custom Fields“ (die
 * Seitenleiste der Einstellungen, das Kontaktprofil, die E-Mail-Einstellungen).
 * Mit FluentCRM Pro fällt das nicht auf, weil dessen eigene Map den alten
 * Schlüssel noch führt. Ohne Pro stand „Custom Fields“ englisch da (am
 * 30.10.2026 auf praesentare.com gemessen). Gemeldet an den Hersteller.
 *
 * Ergänzt wird nur, was in der Map fehlt, und mit dem Text des Eintrags, den
 * der Core selbst übersetzt. Liefert Pro oder ein späteres Release den
 * Schlüssel wieder, gilt dessen Eintrag, und diese Datei tut nichts.
 *
 * Abschaltbar über `pcl_fluent_de/fluentcrm_map_ergaenzen`.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Missing key => msgid in the fluent-crm domain that carries the text.
 */
function pcl_fluent_de_crm_map_eintraege() {
    return array(
        'Custom Fields' => 'Custom fields',
    );
}

function pcl_fluent_de_crm_map_ergaenzen($vars) {
    if (!is_array($vars) || !isset($vars['trans']) || !is_array($vars['trans'])) {
        return $vars;
    }

    if (0 !== strpos(determine_locale(), 'de_')) {
        return $vars;
    }

    if (!apply_filters('pcl_fluent_de/fluentcrm_map_ergaenzen', true)) {
        return $vars;
    }

    foreach (pcl_fluent_de_crm_map_eintraege() as $schluessel => $msgid) {
        if (!array_key_exists($schluessel, $vars['trans'])) {
            $vars['trans'][$schluessel] = __($msgid, 'fluent-crm');
        }
    }

    return $vars;
}
// After Pro, which adds its own map at the default priority.
add_filter('fluent_crm/admin_vars', 'pcl_fluent_de_crm_map_ergaenzen', 20);
