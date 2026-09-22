<?php
/**
 * Seit 1.2.0: zwei Korrekturen an den Mails, die FluentAuth verschickt.
 *
 * 1. Fußzeile. Die Vorlage `app/Views/magic_login/footer.php` (Anmeldecode und
 *    Magic Login in der Standardfassung) schreibt „This email has been sent
 *    from: <Adresse>“ ohne __(); kein Katalog erreicht den Satz. Unter de_DE
 *    wird er hier ersetzt.
 *
 * 2. Der Anmeldecode als Telefonnummer. Mail-Apps auf Mobilgeräten machen aus
 *    der sechsstelligen Zahl einen Anruf-Link. FluentAuth setzt den Code in
 *    beiden Fassungen (Standard und „Eigene Fassung“ mit der Vorgabe aus
 *    SystemEmailService) in einen Absatz mit `letter-spacing: 7px`. Dort
 *    bekommt jede Ziffer ein eigenes <span>, das die Erkennung als
 *    zusammenhängende Nummer bricht, ohne unsichtbare Zeichen einzufügen – ein
 *    kopierter Code bleibt also genau die sechs Ziffern. Dazu kommen
 *    `format-detection: telephone=no` und die Apple-Regel für erkannte
 *    Daten in den <head>, soweit die Mail einen hat.
 *
 * Getroffen werden nur Mails, die eines der beiden FluentAuth-Merkmale tragen
 * (die Fußzeile der Standardvorlage oder den Code-Absatz). Alles andere, was
 * die Website verschickt, geht unverändert durch. Priorität 20, damit
 * FluentSMTP und andere Filter vorher fertig sind.
 *
 * Abschaltbar über `pcl_fluentauth_de/mail_korrigieren`.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Style fragment FluentAuth puts on the code paragraph (3.0.1), as a regex
 * part. The CSS inliner of the "Eigene Fassung" path may drop the blank.
 */
const PCL_FLUENTAUTH_DE_CODE_MERKMAL = 'letter-spacing:\s*7px';

/** Footer text of app/Views/magic_login/footer.php (3.0.1). */
const PCL_FLUENTAUTH_DE_FUSS_MERKMAL = 'This email has been sent from: ';

/**
 * Wraps every digit of the code paragraph in its own span.
 *
 * Only the text between the opening <p …letter-spacing: 7px…> and its </p>
 * is touched, and only if it consists of digits and whitespace.
 */
function pcl_fluent_de_auth_code_entschaerfen($html) {
    $muster = '#(<p\b[^>]*' . PCL_FLUENTAUTH_DE_CODE_MERKMAL . '[^>]*>)(\s*)(\d{4,10})(\s*)(</p>)#i';

    return preg_replace_callback($muster, function ($m) {
        $ziffern = '';
        foreach (str_split($m[3]) as $ziffer) {
            $ziffern .= '<span style="color:inherit;text-decoration:none;">' . $ziffer . '</span>';
        }
        return $m[1] . $m[2] . $ziffern . $m[4] . $m[5];
    }, $html);
}

/**
 * Adds the "no phone numbers" hints to the <head>, once.
 */
function pcl_fluent_de_auth_head_ergaenzen($html) {
    if (false !== stripos($html, 'format-detection') || false === stripos($html, '</head>')) {
        return $html;
    }

    $zusatz = '<meta name="format-detection" content="telephone=no, date=no, address=no, email=no">'
        . '<style>a[x-apple-data-detectors]{color:inherit!important;text-decoration:none!important;'
        . 'font-size:inherit!important;font-family:inherit!important;font-weight:inherit!important;'
        . 'line-height:inherit!important;}</style>';

    return preg_replace('#</head>#i', $zusatz . '</head>', $html, 1);
}

function pcl_fluent_de_auth_mail_korrigieren($atts) {
    if (!is_array($atts) || empty($atts['message']) || !is_string($atts['message'])) {
        return $atts;
    }

    $html = $atts['message'];
    $fuss = false !== strpos($html, PCL_FLUENTAUTH_DE_FUSS_MERKMAL);
    $code = (bool) preg_match('#' . PCL_FLUENTAUTH_DE_CODE_MERKMAL . '#i', $html);

    if (!$fuss && !$code) {
        return $atts;
    }

    if (!apply_filters('pcl_fluentauth_de/mail_korrigieren', true, $atts)) {
        return $atts;
    }

    // The footer text is only replaced where the German catalogue is in use.
    if ($fuss && 'de_DE' === determine_locale()) {
        $html = str_replace(PCL_FLUENTAUTH_DE_FUSS_MERKMAL, 'Diese E-Mail wurde gesendet von: ', $html);
    }

    // The phone number fix does not depend on the language.
    if ($code) {
        $html = pcl_fluent_de_auth_code_entschaerfen($html);
        $html = pcl_fluent_de_auth_head_ergaenzen($html);
    }

    $atts['message'] = $html;
    return $atts;
}
add_filter('wp_mail', 'pcl_fluent_de_auth_mail_korrigieren', 20);
