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
 * Seit 2.6.7: 3. Die drei Hinweis-Mails an die Verwaltung. „Anmeldung
 *    erfolgreich“, „Anmeldung gesperrt“ und der Anmeldebericht
 *    (`LoginSecurityHandler::maybeSendSuccessEmail()` und
 *    `maybeSendBlockedEmail()`, `BasicTasksHandler`) bauen Betreff,
 *    Einleitung und Beschriftungen ohne __() zusammen (3.0.5). Erkannt wird
 *    jede an ihrer englischen Einleitung; nur dann werden Betreff, Einleitung,
 *    Vorschauzeile und Beschriftungen ersetzt. Die Anmeldemethode und die
 *    Rolle stehen dort als Bezeichner (`two_factor_email`, `administrator`)
 *    und bekommen ihre Beschriftung aus FluentAuth bzw. WordPress. Die Mails
 *    gehen an die Verwaltung, deshalb anredefrei. Ändert FluentAuth den
 *    Wortlaut, bleibt die Mail englisch, kaputt geht nichts.
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

require_once __DIR__ . '/mail-codes.php';

/**
 * Wraps every digit of the code paragraph in its own span.
 *
 * Only the text between the opening <p …letter-spacing: 7px…> and its </p>
 * is touched, and only if it consists of digits and whitespace. Since 2.6.18
 * one wrapping <span> around the digits is allowed: an own template saved in
 * FluentAuth's editor puts the code into a span with the font stack
 * (measured in a logged 2FA mail, 30.10.2026).
 */
function pcl_fluent_de_auth_code_entschaerfen($html) {
    $muster = '#(<p\b[^>]*' . PCL_FLUENTAUTH_DE_CODE_MERKMAL . '[^>]*>)(\s*(?:<span\b[^>]*>\s*)?)(\d{4,10})(\s*(?:</span>\s*)?)(</p>)#i';

    return preg_replace_callback($muster, function ($m) {
        return $m[1] . $m[2] . pcl_fluent_de_code_ziffern($m[3]) . $m[4] . $m[5];
    }, $html);
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
        $html = pcl_fluent_de_mail_kopf_ergaenzen($html);
    }

    $atts['message'] = $html;
    return $atts;
}
add_filter('wp_mail', 'pcl_fluent_de_auth_mail_korrigieren', 20);

/**
 * Which of the three notification mails this is, by its opening paragraph
 * (3.0.5). Returns 'erfolg', 'gesperrt', 'bericht' or ''.
 */
function pcl_fluent_de_auth_hinweis_art($html) {
    if (false !== strpos($html, 'Hello there, <br />The following user has been logged in to your site. Here is the details:')) {
        return 'erfolg';
    }
    if (false !== strpos($html, 'Hello there, <br />The following user has been blocked from logged in from your site. Here is the details:')) {
        return 'gesperrt';
    }
    if (preg_match('#Hello there, <br />Here is the (daily|weekly|monthly) digest report of your site\'s \(.*?\) login activity\.#', $html)) {
        return 'bericht';
    }
    return '';
}

/** Adjective stem for the digest period, declined by the caller. */
function pcl_fluent_de_auth_periode($periode) {
    $map = array('daily' => 'täglich', 'weekly' => 'wöchentlich', 'monthly' => 'monatlich');
    $periode = strtolower($periode);
    return isset($map[$periode]) ? $map[$periode] : $periode;
}

/** "14, Oct 2026" (PHP date 'd, M Y') as "14.10.2026". */
function pcl_fluent_de_auth_datum($tag, $monat, $jahr) {
    $monate = array('Jan' => 1, 'Feb' => 2, 'Mar' => 3, 'Apr' => 4, 'May' => 5, 'Jun' => 6,
        'Jul' => 7, 'Aug' => 8, 'Sep' => 9, 'Oct' => 10, 'Nov' => 11, 'Dec' => 12);
    if (!isset($monate[$monat])) {
        return $tag . ', ' . $monat . ' ' . $jahr;
    }
    return sprintf('%02d.%02d.%s', (int) $tag, $monate[$monat], $jahr);
}

/** Role slugs ("administrator, editor") as their translated role names. */
function pcl_fluent_de_auth_rollen($slugs) {
    $namen = function_exists('wp_roles') ? wp_roles()->role_names : array();
    $aus   = array();
    foreach (array_map('trim', explode(',', $slugs)) as $slug) {
        $aus[] = isset($namen[$slug]) ? translate_user_role($namen[$slug]) : $slug;
    }
    return esc_html(implode(', ', $aus));
}

/** Login media key ("two_factor_email") as FluentAuth's own, translated label. */
function pcl_fluent_de_auth_methode($media) {
    $helper = '\FluentAuth\App\Helpers\Helper';
    if (class_exists($helper) && method_exists($helper, 'getLoginMediaLabel')) {
        return esc_html(call_user_func(array($helper, 'getLoginMediaLabel'), $media));
    }
    return $media;
}

function pcl_fluent_de_auth_hinweis_uebersetzen($atts) {
    if (!is_array($atts) || empty($atts['message']) || !is_string($atts['message'])) {
        return $atts;
    }

    if (0 !== strpos(determine_locale(), 'de_')) {
        return $atts;
    }

    $html = $atts['message'];
    $art  = pcl_fluent_de_auth_hinweis_art($html);

    if (!$art || !apply_filters('pcl_fluentauth_de/mail_korrigieren', true, $atts)) {
        return $atts;
    }

    // Opening paragraph.
    $html = str_replace(
        array(
            'Hello there, <br />The following user has been logged in to your site. Here is the details:',
            'Hello there, <br />The following user has been blocked from logged in from your site. Here is the details:',
        ),
        array(
            'Hallo, <br />auf der Website hat sich folgendes Konto angemeldet:',
            'Hallo, <br />auf der Website wurde folgende Anmeldung gesperrt:',
        ),
        $html
    );
    $html = preg_replace_callback(
        '#Hello there, <br />Here is the (daily|weekly|monthly) digest report of your site\'s \((.*?)\) login activity\.#',
        function ($m) {
            return 'Hallo, <br />hier ist der ' . pcl_fluent_de_auth_periode($m[1])
                . 'e Bericht über die Anmeldungen auf der Website ' . $m[2] . '.';
        },
        $html
    );

    // Preheader. FluentAuth 3.0.5 prints it only when empty, so this is for later.
    $html = preg_replace_callback(
        '#(<span class="preheader"[^>]*>)(Login success at |Blocked from login |Auth Report for )#',
        function ($m) {
            $neu = array(
                'Login success at '   => 'Anmeldung erfolgreich auf ',
                'Blocked from login ' => 'Anmeldung gesperrt auf ',
                'Auth Report for '    => 'Anmeldebericht für ',
            );
            return $m[1] . $neu[$m[2]];
        },
        $html
    );

    // Values that arrive as identifiers. Before the labels, which they match on.
    $html = preg_replace_callback('#(<b>Media:</b> )([a-z0-9_]+)(</li>)#', function ($m) {
        return $m[1] . pcl_fluent_de_auth_methode($m[2]) . $m[3];
    }, $html);
    $html = preg_replace_callback('#(<b>User Role:</b> )([^<]*)(</li>)#', function ($m) {
        return $m[1] . pcl_fluent_de_auth_rollen($m[2]) . $m[3];
    }, $html);

    // Labels; wording as in the fluent-security catalogue.
    $beschriftungen = array(
        'Site URL'         => 'Website-URL',
        'Username'         => 'Benutzername',
        'User Role'        => 'Benutzerrolle',
        'Media'            => 'Anmeldemethode',
        'Email'            => 'E-Mail',
        'Name'             => 'Name',
        'Login IP Address' => 'IP-Adresse',
        'Browser'          => 'Browser',
    );
    foreach ($beschriftungen as $en => $de) {
        $html = str_replace('<b>' . $en . ':</b>', '<b>' . $de . ':</b>', $html);
    }

    $atts['message'] = $html;

    // Subject, only for a mail recognised above.
    if (!empty($atts['subject']) && is_string($atts['subject'])) {
        $betreff = $atts['subject'];
        if ('erfolg' === $art && preg_match('#^\[(.*)\] Login success for (.*)$#s', $betreff, $m)) {
            $betreff = '[' . $m[1] . '] Anmeldung erfolgreich: ' . $m[2];
        } elseif ('gesperrt' === $art && preg_match('#^\[(.*)\] Blocked from login - (.*)$#s', $betreff, $m)) {
            $betreff = '[' . $m[1] . '] Anmeldung gesperrt: ' . $m[2];
        } elseif ('bericht' === $art && preg_match('#^(Daily|Weekly|Monthly) report for (.*) - (\d{1,2}), ([A-Za-z]{3}) (\d{4})$#s', $betreff, $m)) {
            $betreff = ucfirst(pcl_fluent_de_auth_periode($m[1])) . 'er Anmeldebericht für ' . $m[2]
                . ' (' . pcl_fluent_de_auth_datum($m[3], $m[4], $m[5]) . ')';
        }
        $atts['subject'] = $betreff;
    }

    return $atts;
}
add_filter('wp_mail', 'pcl_fluent_de_auth_hinweis_uebersetzen', 20);
