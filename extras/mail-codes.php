<?php
/**
 * Seit 2.6.18: gemeinsame Bausteine gegen Codes, die Mail-Apps als
 * Telefonnummer verlinken.
 *
 * Mail-Apps auf Mobilgeräten machen aus einer sechsstelligen Zahl einen
 * Anruf-Link. Zwei Mittel helfen zusammen:
 *
 * 1. Jede Ziffer bekommt ein eigenes <span>. Das bricht die Erkennung als
 *    zusammenhängende Nummer, ohne unsichtbare Zeichen einzufügen – ein
 *    kopierter Code bleibt genau die Ziffern.
 * 2. `format-detection: telephone=no` und die Apple-Regel für erkannte Daten
 *    im <head>, soweit die Mail einen hat.
 *
 * Genutzt von `fluentauth-mail.php` (Anmeldecode, 2FA-Code) und
 * `fluentcommunity.php` (Verifizierungscode bei der Registrierung). Beide
 * binden diese Datei mit require_once ein; die Registry lädt sie nicht selbst.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * The digits of a code, each in its own span.
 */
function pcl_fluent_de_code_ziffern($code) {
    $ziffern = '';
    foreach (str_split((string) $code) as $ziffer) {
        $ziffern .= '<span style="color:inherit;text-decoration:none;">' . $ziffer . '</span>';
    }

    return $ziffern;
}

/**
 * Adds the "no phone numbers" hints to the <head>, once.
 */
function pcl_fluent_de_mail_kopf_ergaenzen($html) {
    if (false !== stripos($html, 'format-detection') || false === stripos($html, '</head>')) {
        return $html;
    }

    $zusatz = '<meta name="format-detection" content="telephone=no, date=no, address=no, email=no">'
        . '<style>a[x-apple-data-detectors]{color:inherit!important;text-decoration:none!important;'
        . 'font-size:inherit!important;font-family:inherit!important;font-weight:inherit!important;'
        . 'line-height:inherit!important;}</style>';

    return preg_replace('#</head>#i', $zusatz . '</head>', $html, 1);
}
