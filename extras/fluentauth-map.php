<?php
/**
 * Seit 1.1.0: Texte der Verwaltung, die FluentAuth nicht übersetzbar macht.
 *
 * Die Vue-App schlägt jeden Text in `window.fluentAuthAdmin.i18n` nach, gefüllt
 * aus `TransStrings::getStrings()`. Vier Schlüssel, die die App in 3.0.1
 * nachschlägt, fehlen dort und damit auch in der POT; kein Katalog erreicht
 * sie (gemessen mit scripts/missing-strings.py am 17.09.2026, gemeldet bei
 * WPManageNinja/fluent-security). Die App bleibt an diesen Stellen englisch.
 *
 * FluentAuth reicht die ganze Map durch den Filter `fluent_security/app_vars`.
 * Hier werden die fehlenden Schlüssel ergänzt – nur unter de_DE und nur, wenn
 * FluentAuth sie nicht schon selbst liefert. Nimmt ein späteres Release sie
 * in die Map auf, gilt dessen Eintrag (und damit der Katalog), und diese
 * Datei tut nichts mehr.
 *
 * Abschaltbar über `pcl_fluentauth_de/map_ergaenzen`.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Die ergänzten Einträge: englischer Schlüssel => deutscher Text (Du-Fassung).
 *
 * Wortwahl wie im Katalog: „Mit Passkey anmelden“ ist die Beschriftung der
 * Schaltfläche, „Nutzungsbedingungen“ steht so im Registrierungsformular, und
 * „ oder “ verbindet die Satzteile „eine Authenticator-App“ und „einen
 * Passkey“ im Akkusativ.
 */
function pcl_fluent_de_auth_map_eintraege() {
    return array(
        ' or ' => ' oder ',
        'Adds a "Sign in with a passkey" button to the login form. Anyone without a passkey signs in as before.' =>
            'Fügt dem Anmeldeformular die Schaltfläche „Mit Passkey anmelden“ hinzu. Wer keinen Passkey hat, meldet sich wie bisher an.',
        'Copy this line into your wp-config.php, above the line that says "That\'s all, stop editing". Save the file, then come back here and switch this on.' =>
            'Kopiere diese Zeile in Deine wp-config.php, oberhalb der Zeile mit „That\'s all, stop editing“. Speichere die Datei, komm dann hierher zurück und schalte die Option ein.',
        'privacy policy and terms and conditions' =>
            'Datenschutzerklärung und Nutzungsbedingungen',
    );
}

function pcl_fluent_de_auth_map_ergaenzen($vars) {
    if (!is_array($vars) || !isset($vars['i18n']) || !is_array($vars['i18n'])) {
        return $vars;
    }

    if ('de_DE' !== determine_locale()) {
        return $vars;
    }

    if (!apply_filters('pcl_fluentauth_de/map_ergaenzen', true)) {
        return $vars;
    }

    foreach (pcl_fluent_de_auth_map_eintraege() as $schluessel => $text) {
        if (!array_key_exists($schluessel, $vars['i18n'])) {
            $vars['i18n'][$schluessel] = $text;
        }
    }

    return $vars;
}
add_filter('fluent_security/app_vars', 'pcl_fluent_de_auth_map_ergaenzen', 20);
