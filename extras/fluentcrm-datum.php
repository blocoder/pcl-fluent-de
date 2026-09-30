<?php
/**
 * Deutsche Datumsangaben in der FluentCRM-Verwaltung.
 *
 * Übernommen am 30.09.2026 aus `pcl-fluentcrm-de` 1.3.0 (dort seit dessen
 * 1.1.0, 17.09.2026). Der Umzug hierher war Entscheidung PC’L: Die Datei
 * wirkt nur auf die **kostenlose** FluentCRM-Oberfläche, und deren Übersetzung
 * liefert seit dem 24.09.2026 dieses Plugin aus. Im alten Plugin lag sie
 * zuletzt falsch – auf dem Testsystem ist es inaktiv, und damit fehlten die
 * deutschen Datumsangaben dort ganz.
 *
 * Diagramme, Tooltips, Datumswähler und relative Zeiten formatiert FluentCRM
 * im Browser mit dayjs, und zwar mit der Instanz, die `boot.js` als
 * `window.dayjs` ablegt – ohne Sprache. Daher „Friday“, „Sep 10“ und
 * „2 days ago“, auch wenn der Katalog vollständig deutsch ist. Die
 * Achsenbeschriftungen des Kontaktwachstums kommen als `Y-m-d` vom Server;
 * übersetzbar ist daran nichts, nur die Formatierung.
 *
 * Was das Skript tut:
 *
 * 1. Es fängt die Zuweisung `window.dayjs = …` ab (die Bundles laden als
 *    Module und laufen nach diesem Skript) und registriert eine deutsche
 *    Locale samt relativer Zeiten („vor 2 Tagen“, „in einer Stunde“).
 * 2. Es biegt die fest eingebauten englischen Formate auf deutsche
 *    Reihenfolge um. Ohne das stünde „Sep. 10“ da, ein halbdeutsches Datum,
 *    das man falsch liest. Die Liste stammt aus den Bundles von FluentCRM und
 *    Pro; sie ist gegen 3.2.5 nachgemessen und unverändert gegenüber 3.2.0.
 *    Ein Format, das nicht darin steht, bleibt unverändert und liefert dann
 *    deutsche Namen in englischer Reihenfolge.
 *
 * Nicht erreichbar: der Dezimalpunkt an Diagrammachsen (ECharts) und die
 * Zeitraumauswahl „30 Days“ (Literal im Dashboard-Bundle).
 *
 * Nur bei deutscher Locale und nur auf den Seiten `?page=fluentcrm…`.
 * Abschaltbar über den Filter `pcl_fluentcrm_de/dayjs_deutsch` – der Name
 * bleibt, wie schon bei FluentSMTP und FluentSnippets: Nur die Funktionen
 * heißen anders, damit ein Snippet, das sich daran hängt, weiter greift.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Liefert das alte Plugin diese Ausgabe noch selbst?
 *
 * `pcl-fluentcrm-de` gibt die freie Domain ab, bleibt aber für
 * `fluentcampaign-pro` aktiv – deshalb steht es bewusst **nicht** als
 * `vorgaenger` in der Registry (ein Eintrag dort würde die Domain dauerhaft
 * blockieren). Für diese beiden Dateien braucht es trotzdem eine Weiche:
 * Solange die alte Fassung mitläuft, muss diese hier schweigen.
 *
 * Zwei dayjs-Umstellungen nacheinander wären nicht bloß Verschwendung. Beide
 * legen `window.dayjs` über `Object.defineProperty` neu an; die zweite
 * überschriebe den Getter der ersten, und der Selbstschutz `dayjs.__pclDe`
 * greift erst danach. Der Flag-Name ist deshalb in beiden Fassungen gleich
 * geblieben – er ist das zweite Netz, nicht das erste.
 *
 * Geprüft wird die Funktion, nicht der Plugin-Ordner: Sie existiert genau
 * dann, wenn das alte Plugin aktiv ist und seine Datei eingebunden hat.
 *
 * Beide `fluentcrm-*`-Dateien brauchen die Weiche, und keine soll auf die
 * Ladereihenfolge der anderen angewiesen sein. Deshalb definiert sie, wer
 * zuerst geladen wird, und die zweite lässt sie stehen.
 *
 * @return bool
 */
if (!function_exists('pcl_fluent_de_crm_vorgaenger_liefert')) {
    function pcl_fluent_de_crm_vorgaenger_liefert() {
        return function_exists('pcl_fluentcrm_de_dayjs_ausgeben');
    }
}

/**
 * Das Skript als Zeichenkette.
 */
function pcl_fluent_de_crm_dayjs_script() {
    return <<<'JS'
(function () {
    'use strict';

    // Relative times need the dative after "vor" and "in": "vor 2 Tagen".
    var woerter = {
        s: ['ein paar Sekunden', 'ein paar Sekunden'],
        m: ['eine Minute', 'einer Minute'],
        mm: ['%d Minuten', '%d Minuten'],
        h: ['eine Stunde', 'einer Stunde'],
        hh: ['%d Stunden', '%d Stunden'],
        d: ['ein Tag', 'einem Tag'],
        dd: ['%d Tage', '%d Tagen'],
        M: ['ein Monat', 'einem Monat'],
        MM: ['%d Monate', '%d Monaten'],
        y: ['ein Jahr', 'einem Jahr'],
        yy: ['%d Jahre', '%d Jahren']
    };
    function relativ(zahl, ohneZusatz, schluessel) {
        return woerter[schluessel][ohneZusatz ? 0 : 1].replace('%d', zahl);
    }
    var rt = { future: 'in %s', past: 'vor %s' };
    Object.keys(woerter).forEach(function (k) { rt[k] = relativ; });

    var DE = {
        name: 'de',
        weekdays: 'Sonntag_Montag_Dienstag_Mittwoch_Donnerstag_Freitag_Samstag'.split('_'),
        weekdaysShort: 'So._Mo._Di._Mi._Do._Fr._Sa.'.split('_'),
        weekdaysMin: 'So_Mo_Di_Mi_Do_Fr_Sa'.split('_'),
        months: 'Januar_Februar_März_April_Mai_Juni_Juli_August_September_Oktober_November_Dezember'.split('_'),
        monthsShort: 'Jan._Feb._März_Apr._Mai_Juni_Juli_Aug._Sep._Okt._Nov._Dez.'.split('_'),
        weekStart: 1,
        yearStart: 4,
        ordinal: function (n) { return n + '.'; },
        formats: {
            LTS: 'HH:mm:ss',
            LT: 'HH:mm',
            L: 'DD.MM.YYYY',
            LL: 'D. MMMM YYYY',
            LLL: 'D. MMMM YYYY HH:mm',
            LLLL: 'dddd, D. MMMM YYYY HH:mm'
        },
        relativeTime: rt
    };

    // English order as found in the FluentCRM bundles -> German order.
    var FORMATE = {
        'MMM DD': 'DD. MMM',
        'MMM D': 'D. MMM',
        'MMM DD, YYYY': 'DD. MMM YYYY',
        'MMM D, YYYY': 'D. MMM YYYY',
        'D MMM': 'D. MMM',
        'D MMM, YYYY': 'D. MMM YYYY',
        'D MMM, YY': 'D. MMM YY',
        'h:mm A': 'HH:mm',
        'DD/MM/YYYY': 'DD.MM.YYYY',
        'DD-MM-YYYY HH:mm': 'DD.MM.YYYY HH:mm'
    };

    // "MMMM Do, YYYY [at] h:mm A" (campaign times). The advancedFormat plugin
    // wraps format() outside of us and has already replaced "Do" with the bare
    // ordinal ("10.") when we see the string - measured on dev, 17.09.2026.
    // The pattern accepts "Do" as well as any replacement and brackets it, so
    // digits and dots stay literal.
    var MUSTER = [
        [/^MMMM (Do|\[[^\]]*\]|[^,\[\]]+), YYYY \[at\] h:mm A$/, function (alles, tag) {
            var kopf = tag === 'Do' ? 'Do' : (tag.charAt(0) === '[' ? tag : '[' + tag + ']');
            return kopf + ' MMMM YYYY [um] HH:mm';
        }]
    ];

    function umstellen(vorlage) {
        if (typeof vorlage !== 'string') {
            return vorlage;
        }
        if (Object.prototype.hasOwnProperty.call(FORMATE, vorlage)) {
            return FORMATE[vorlage];
        }
        for (var i = 0; i < MUSTER.length; i++) {
            if (MUSTER[i][0].test(vorlage)) {
                return vorlage.replace(MUSTER[i][0], MUSTER[i][1]);
            }
        }
        return vorlage;
    }

    function einrichten(dayjs) {
        if (!dayjs || typeof dayjs.locale !== 'function' || dayjs.__pclDe) {
            return dayjs;
        }
        try {
            dayjs.locale(DE);
            dayjs.extend(function (option, Klasse) {
                var original = Klasse.prototype.format;
                Klasse.prototype.format = function (vorlage) {
                    return original.call(this, umstellen(vorlage));
                };
                // Element Plus asks for its own UI language on every value
                // (dayjs(x).locale("en")), which brought back "Aug" without
                // a dot in the range pickers - measured on kommunarden,
                // 17.09.2026. On these pages English is never the intent.
                var originalLocale = Klasse.prototype.locale;
                Klasse.prototype.locale = function (vorgabe, objekt) {
                    if (vorgabe === 'en' && !objekt) {
                        vorgabe = 'de';
                    }
                    return originalLocale.call(this, vorgabe, objekt);
                };
            });
            // Same flag name as in pcl-fluentcrm-de on purpose: whichever runs
            // first, the other one leaves the instance alone.
            dayjs.__pclDe = true;
        } catch (fehler) {
            if (window.console) {
                window.console.warn('pcl-fluent-de: dayjs nicht umgestellt', fehler);
            }
        }
        return dayjs;
    }

    var aktuell = einrichten(window.dayjs);
    try {
        Object.defineProperty(window, 'dayjs', {
            configurable: true,
            enumerable: true,
            get: function () { return aktuell; },
            set: function (wert) { aktuell = einrichten(wert); }
        });
    } catch (fehler) {
        // Leave the page alone if the property cannot be redefined.
    }
})();
JS;
}

/**
 * Trenner in allen Datumsbereichs-Wählern: „→“ mit Abstand, nicht fett.
 *
 * FluentCRM setzt den Trenner auf vier Arten: fest „-“ (Dashboard), die Texte
 * `To` und `to` („bis“) und „→“ (Pro). Sein CSS gibt ihm einen 16 bzw. 20
 * Pixel breiten, fetten Kasten ohne Abstand – gebaut für ein einzelnes
 * Zeichen. „bis“ quoll darüber hinaus („2026bis17.“) und stand fett da. Der
 * eigentliche Text wird hier ausgeblendet und durch den Pfeil ersetzt; damit
 * ist `To` im Katalog frei für „An“, das dieselbe msgid als
 * Empfänger-Beschriftung braucht.
 */
function pcl_fluent_de_crm_trenner_css() {
    return <<<'CSS'
.el-date-editor .el-range-separator,
.fc_range_picker .el-range-editor--mini .el-range-separator,
.fluentcrm-templates-action-buttons .el-date-editor .el-range-separator {
    flex: 0 0 auto !important;
    width: auto !important;
    min-width: 0 !important;
    /* px, not em: font-size is 0 here, so em would collapse to nothing. */
    padding: 0 5px !important;
    font-size: 0 !important;
    font-weight: 400 !important;
}
.el-date-editor .el-range-separator::after {
    content: "→";
    font-size: 14px;
    font-weight: 400;
}
.el-range-editor--small .el-range-separator::after,
.el-range-editor--mini .el-range-separator::after {
    font-size: 12px;
}
CSS;
}

/**
 * Das Skript früh im Kopf ausgeben, vor den Modul-Bundles von FluentCRM.
 */
function pcl_fluent_de_crm_dayjs_ausgeben() {
    if (pcl_fluent_de_crm_vorgaenger_liefert()) {
        return;
    }
    if (0 !== strpos(determine_locale(), 'de_')) {
        return;
    }
    // Read-only routing check, no state change - a nonce adds nothing here.
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $seite = isset($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : '';
    if (0 !== strpos($seite, 'fluentcrm')) {
        return;
    }
    if (!apply_filters('pcl_fluentcrm_de/dayjs_deutsch', true)) {
        return;
    }
    wp_print_inline_script_tag(pcl_fluent_de_crm_dayjs_script(), array('id' => 'pcl-fluent-de-crm-dayjs'));
    echo '<style id="pcl-fluent-de-crm-trenner">' . pcl_fluent_de_crm_trenner_css() . "</style>\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- static CSS
}
add_action('admin_head', 'pcl_fluent_de_crm_dayjs_ausgeben', 1);
