<?php
/**
 * Deutsche Datumsangaben in der FluentSMTP-Verwaltung.
 *
 * Seit 1.1.0 (17.09.2026, Entscheidung PC’L). Das E-Mail-Protokoll, die
 * letzten Aktivitäten und die Achse der Versandstatistik formatiert
 * FluentSMTP im Browser mit dayjs – ohne Sprache. Daher „16 Sep 2026 6:17 PM“
 * und „Sep 11“, auch wenn der Katalog vollständig deutsch ist.
 *
 * Anders als FluentCRM legt FluentSMTP dayjs nicht auf `window`. Es gibt zwei
 * Instanzen, beide in den Bundles eingeschlossen: eine in `boot.js` (für
 * `$dateFormat`) und eine im App-Bundle (Diagrammachse, Element Plus). Der Weg
 * aus `pcl-fluentcrm-de` (Zuweisung an `window.dayjs` abfangen) greift hier
 * nicht.
 *
 * Was das Skript stattdessen tut:
 *
 * 1. Jede dayjs-Instanz setzt beim Aufbau `F.Ls = L` (ihre Locale-Tabelle).
 *    Ein Setter für `Ls` auf `Object.prototype` bekommt dabei die Instanz zu
 *    fassen, legt die Eigenschaft ganz normal als eigene an und registriert
 *    eine deutsche Locale samt relativer Zeiten. Für jedes andere Objekt, das
 *    `Ls` setzt, verhält sich der Setter wie eine gewöhnliche Zuweisung.
 * 2. Es biegt die fest eingebauten englischen Formate auf deutsche
 *    Reihenfolge um (Liste aus den Bundles von FluentSMTP 2.4.0). Ein Format,
 *    das nicht darin steht, bleibt unverändert.
 *
 * Anders als in `pcl-fluentcrm-de` wird `locale('en')` **nicht** umgelenkt.
 * Hier trifft das Skript auch die dayjs-Instanz von Element Plus, und die
 * bildet die Schlüssel der Wochentage im Datumswähler bewusst über
 * `date.locale("en").localeData().weekdaysShort()`. Mit Umlenkung standen
 * dort rohe Schlüssel wie „el.datepicker.weeks.mo.“ (gemessen auf dem
 * Testsystem, 17.09.2026).
 *
 * 3. Seit 1.2.0 (Wunsch PC’L): eine deutsche Sprachtabelle für Element Plus.
 *    FluentSMTP startet Element Plus ohne Sprache
 *    (`provideGlobalConfig({zIndex: 1e5}, app, true)`), die Bibliothek fällt
 *    dann auf ihre englische Tabelle zurück: „Sun Mon …“, „October“,
 *    „Total 27“, „10/page“. Die Sprache liest Element Plus aus genau diesem
 *    Einstellungsobjekt, und zwar erst, wenn eine Komponente sie braucht. Das
 *    Skript fängt `window.FluentMail.app = …` ab (steht unmittelbar vor
 *    `mount()`), sucht in `app._context.provides` das Objekt mit `zIndex`
 *    und hängt `locale` an. Mit `name: 'de'` formatiert Element Plus seine
 *    Werte über die deutsche dayjs-Locale; die Woche beginnt am Montag.
 *    Die Tabelle trägt alle Schlüssel der englischen aus FluentSMTP 2.4.0
 *    (Element Plus zeigt bei einem fehlenden Schlüssel dessen Pfad an). Nach
 *    einem Update von FluentSMTP `scripts/ep-locale-check.py` laufen lassen.
 *    Bleibt: die Reihenfolge „2026 September“ im Kopf des Wählers, die setzt
 *    Element Plus fest zusammen.
 * 4. Seit 1.2.0: Der Trenner im Datumsbereich ist „→“ wie in
 *    `pcl-fluentcrm-de` 1.1.2 (Entscheidung PC’L). Der Text („bis“) bleibt im
 *    Katalog und wird per CSS ausgeblendet.
 *
 * Nur bei deutscher Locale und nur auf `?page=fluent-mail`. Abschaltbar über
 * den Filter `pcl_fluentsmtp_de/dayjs_deutsch`.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Das Skript als Zeichenkette. Bewusst hier und nicht als .js-Datei: Es ist
 * klein, gehört zu genau dieser Seite, und `package.sh` wie `push-plugin.sh`
 * übertragen die PHP-Dateien des Plugins ohnehin.
 */
function pcl_fluent_de_smtp_dayjs_script() {
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

    // English order as found in the FluentSMTP 2.4.0 bundles -> German order.
    // The localizedFormat plugin wraps format() outside of us and has already
    // replaced LT with the German "HH:mm" when the string arrives here; both
    // spellings are listed.
    var FORMATE = {
        'DD MMM YYYY LT': 'DD. MMM YYYY, HH:mm',
        'DD MMM YYYY HH:mm': 'DD. MMM YYYY, HH:mm',
        'DD MMM YYYY h:mm A': 'DD. MMM YYYY, HH:mm',
        'MMM D': 'D. MMM',
        'DD-MM-YYYY': 'DD.MM.YYYY'
    };

    function umstellen(vorlage) {
        if (typeof vorlage === 'string' && Object.prototype.hasOwnProperty.call(FORMATE, vorlage)) {
            return FORMATE[vorlage];
        }
        return vorlage;
    }

    function einrichten(dayjs) {
        if (typeof dayjs !== 'function' || typeof dayjs.locale !== 'function'
            || typeof dayjs.extend !== 'function' || dayjs.__pclDe) {
            return;
        }
        dayjs.__pclDe = true;
        try {
            dayjs.locale(DE);
            dayjs.extend(function (option, Klasse) {
                var original = Klasse.prototype.format;
                Klasse.prototype.format = function (vorlage) {
                    return original.call(this, umstellen(vorlage));
                };
            });
        } catch (fehler) {
            if (window.console) {
                window.console.warn('pcl-fluentsmtp-de: dayjs nicht umgestellt', fehler);
            }
        }
    }

    // dayjs builds its factory as: F.extend = …, F.locale = …, …, F.Ls = L.
    // The accessor below sees that last assignment, stores the value as an
    // own property of the target (so later reads never reach us again) and
    // then switches the instance to German.
    try {
        Object.defineProperty(Object.prototype, 'Ls', {
            configurable: true,
            enumerable: false,
            get: function () { return undefined; },
            set: function (wert) {
                Object.defineProperty(this, 'Ls', {
                    configurable: true, enumerable: true, writable: true, value: wert
                });
                if (typeof this === 'function' && typeof this.isDayjs === 'function') {
                    einrichten(this);
                }
            }
        });
    } catch (fehler) {
        // Leave the page alone if the accessor cannot be installed.
    }

    // German Element Plus locale. Keys mirror the English table bundled with
    // FluentSMTP 2.4.0; a missing key would show up as its raw path.
    // scripts/ep-locale-check.py compares both tables - keep this a plain
    // literal (no variables) so the script can read it.
    var EP_DE = {
        name: 'de',
        el: {
            breadcrumb: { label: 'Pfadnavigation' },
            colorpicker: {
                confirm: 'OK', clear: 'Leeren', defaultLabel: 'Farbauswahl',
                description: 'Aktuelle Farbe: {color}. Mit Enter eine neue Farbe wählen.',
                alphaLabel: 'Deckkraft wählen', alphaDescription: 'Deckkraft {alpha}, aktuelle Farbe: {color}',
                hueLabel: 'Farbton wählen', hueDescription: 'Farbton {hue}, aktuelle Farbe: {color}',
                svLabel: 'Sättigung und Helligkeit wählen',
                svDescription: 'Sättigung {saturation}, Helligkeit {brightness}, aktuelle Farbe: {color}',
                predefineDescription: '{value} als Farbe wählen'
            },
            datepicker: {
                now: 'Jetzt', today: 'Heute', cancel: 'Abbrechen', clear: 'Leeren', confirm: 'OK',
                dateTablePrompt: 'Mit den Pfeiltasten und Enter den Tag wählen',
                monthTablePrompt: 'Mit den Pfeiltasten und Enter den Monat wählen',
                quarterTablePrompt: 'Mit den Pfeiltasten und Enter das Quartal wählen',
                yearTablePrompt: 'Mit den Pfeiltasten und Enter das Jahr wählen',
                selectedDate: 'Gewähltes Datum', selectDate: 'Datum wählen', selectTime: 'Uhrzeit wählen',
                startDate: 'Startdatum', startTime: 'Startzeit', endDate: 'Enddatum', endTime: 'Endzeit',
                prevYear: 'Vorheriges Jahr', nextYear: 'Nächstes Jahr',
                prevMonth: 'Vorheriger Monat', nextMonth: 'Nächster Monat',
                year: '',
                month1: 'Januar', month2: 'Februar', month3: 'März', month4: 'April',
                month5: 'Mai', month6: 'Juni', month7: 'Juli', month8: 'August',
                month9: 'September', month10: 'Oktober', month11: 'November', month12: 'Dezember',
                weeks: { sun: 'So', mon: 'Mo', tue: 'Di', wed: 'Mi', thu: 'Do', fri: 'Fr', sat: 'Sa' },
                weeksFull: {
                    sun: 'Sonntag', mon: 'Montag', tue: 'Dienstag', wed: 'Mittwoch',
                    thu: 'Donnerstag', fri: 'Freitag', sat: 'Samstag'
                },
                months: {
                    jan: 'Jan.', feb: 'Feb.', mar: 'März', apr: 'Apr.', may: 'Mai', jun: 'Juni',
                    jul: 'Juli', aug: 'Aug.', sep: 'Sep.', oct: 'Okt.', nov: 'Nov.', dec: 'Dez.'
                }
            },
            input: { characters: '{count} / {max} Zeichen' },
            inputNumber: { decrease: 'Wert verringern', increase: 'Wert erhöhen' },
            select: { loading: 'Wird geladen', noMatch: 'Keine Treffer', noData: 'Keine Daten', placeholder: 'Bitte wählen' },
            mention: { loading: 'Wird geladen' },
            dropdown: { toggleDropdown: 'Menü ein- oder ausblenden' },
            cascader: { noMatch: 'Keine Treffer', loading: 'Wird geladen', placeholder: 'Bitte wählen', noData: 'Keine Daten' },
            pagination: {
                goto: 'Gehe zu', pagesize: '/Seite', total: 'Gesamt: {total}', pageClassifier: '',
                page: 'Seite', prev: 'Vorherige Seite', next: 'Nächste Seite', currentPage: 'Seite {pager}',
                prevPages: '{pager} Seiten zurück', nextPages: '{pager} Seiten vor',
                deprecationWarning: 'Deprecated usages detected, please refer to the el-pagination documentation for more details'
            },
            dialog: { close: 'Dialog schließen' },
            drawer: { close: 'Dialog schließen' },
            messagebox: { title: 'Hinweis', confirm: 'OK', cancel: 'Abbrechen', error: 'Ungültige Eingabe', close: 'Dialog schließen' },
            upload: { deleteTip: 'Zum Entfernen Entf drücken', delete: 'Löschen', preview: 'Vorschau', continue: 'Fortfahren' },
            slider: { defaultLabel: 'Schieberegler zwischen {min} und {max}', defaultRangeStartLabel: 'Startwert wählen', defaultRangeEndLabel: 'Endwert wählen' },
            table: {
                emptyText: 'Keine Daten', confirmFilter: 'Anwenden', resetFilter: 'Zurücksetzen',
                clearFilter: 'Alle', sumText: 'Summe', selectAllLabel: 'Alle Zeilen auswählen',
                selectRowLabel: 'Diese Zeile auswählen', expandRowLabel: 'Zeile aufklappen',
                collapseRowLabel: 'Zeile zuklappen', sortLabel: 'Nach {column} sortieren', filterLabel: 'Nach {column} filtern'
            },
            tag: { close: 'Tag entfernen' },
            tour: { next: 'Weiter', previous: 'Zurück', finish: 'Fertig', close: 'Dialog schließen' },
            tree: { emptyText: 'Keine Daten' },
            transfer: {
                noMatch: 'Keine Treffer', noData: 'Keine Daten', titles: ['Liste 1', 'Liste 2'],
                filterPlaceholder: 'Suchbegriff eingeben', noCheckedFormat: '{total} Einträge',
                hasCheckedFormat: '{checked}/{total} ausgewählt'
            },
            image: { error: 'Fehler' },
            pageHeader: { title: 'Zurück' },
            popconfirm: { confirmButtonText: 'Ja', cancelButtonText: 'Nein' },
            carousel: { leftArrow: 'Karussell nach links', rightArrow: 'Karussell nach rechts', indicator: 'Karussell zu Position {index}' },
            inputOTP: { groupLabel: 'Einmalcode-Eingabe', defaultLabel: 'Bitte Zeichen {index} des Einmalcodes eingeben' }
        }
    };

    // FluentSMTP calls provideGlobalConfig({zIndex: 1e5}, app, true) and then
    // assigns window.FluentMail.app right before mount(). The provided config
    // is that plain object, and Element Plus reads its `locale` lazily, so
    // adding it here (before any component renders) is enough. The locale
    // computed itself is skipped: reading it now would cache `undefined`.
    function spracheSetzen(app) {
        var provides = app && app._context && app._context.provides;
        if (!provides || !Object.getOwnPropertySymbols) {
            return;
        }
        Object.getOwnPropertySymbols(provides).forEach(function (schluessel) {
            if (String(schluessel.description || '').indexOf('locale') !== -1) {
                return;
            }
            var wert = provides[schluessel];
            if (!wert || wert.__v_isRef !== true) {
                return;
            }
            var objekt = wert.value;
            if (objekt && typeof objekt === 'object' && 'zIndex' in objekt && !objekt.locale) {
                objekt.locale = EP_DE;
            }
        });
    }

    function appAbfangen(fluentMail) {
        if (!fluentMail || typeof fluentMail !== 'object' || fluentMail.__pclDe) {
            return;
        }
        var app = fluentMail.app;
        try {
            Object.defineProperty(fluentMail, '__pclDe', { value: true });
            Object.defineProperty(fluentMail, 'app', {
                configurable: true,
                enumerable: true,
                get: function () { return app; },
                set: function (wert) {
                    app = wert;
                    try {
                        spracheSetzen(wert);
                    } catch (fehler) {
                        if (window.console) {
                            window.console.warn('pcl-fluentsmtp-de: Element-Plus-Sprache nicht gesetzt', fehler);
                        }
                    }
                }
            });
        } catch (fehler) {
            // Leave FluentMail alone if the property cannot be redefined.
        }
    }

    // boot.js assigns window.FluentMail = new …; catch that assignment.
    try {
        var fluentMail = window.FluentMail;
        appAbfangen(fluentMail);
        Object.defineProperty(window, 'FluentMail', {
            configurable: true,
            enumerable: true,
            get: function () { return fluentMail; },
            set: function (wert) { fluentMail = wert; appAbfangen(wert); }
        });
    } catch (fehler) {
        // Nothing to do.
    }

    // Fallback: boot.js already ran (another plugin printed it earlier).
    // Its instance is reachable through the app mixin.
    try {
        if (window.FluentMail && typeof window.FluentMail.appMixin === 'function') {
            einrichten((window.FluentMail.appMixin().methods || {}).dayjs);
        }
    } catch (fehler) {
        // Nothing to do.
    }
})();
JS;
}

/**
 * Das Skript unmittelbar vor `boot.js` von FluentSMTP einhängen.
 *
 * FluentSMTP reiht `boot.js` auf `admin_enqueue_scripts` (Priorität 10) im
 * Kopf ein; WordPress gibt es auf `admin_print_scripts` aus, also vor
 * `admin_head`. Ein Skript in `admin_head` käme zu spät – gemessen auf dem Testsystem am
 * 17.09.2026: Die Diagrammachse (App-Bundle, im Fuß) wurde deutsch, das
 * Protokoll (`boot.js`) nicht. `wp_add_inline_script(…, 'before')` setzt den
 * Setter vor beide Bundles.
 */
function pcl_fluent_de_smtp_dayjs_einhaengen() {
    if (!pcl_fluent_de_smtp_datum_aktiv()) {
        return;
    }
    if (!wp_script_is('fluent_mail_admin_app_boot', 'enqueued')) {
        return;
    }
    wp_add_inline_script('fluent_mail_admin_app_boot', pcl_fluent_de_smtp_dayjs_script(), 'before');
}
add_action('admin_enqueue_scripts', 'pcl_fluent_de_smtp_dayjs_einhaengen', 20);

/**
 * Deutsche Locale, FluentSMTP-Seite, Filter nicht abgeschaltet.
 */
function pcl_fluent_de_smtp_datum_aktiv() {
    if (0 !== strpos(determine_locale(), 'de_')) {
        return false;
    }
    // Read-only routing check, no state change - a nonce adds nothing here.
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $seite = isset($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : '';
    if ('fluent-mail' !== $seite) {
        return false;
    }
    return (bool) apply_filters('pcl_fluentsmtp_de/dayjs_deutsch', true);
}

/**
 * Trenner in den Datumsbereichs-Wählern: „→“ mit Abstand, nicht fett.
 *
 * Seit 1.2.0, übernommen aus `pcl-fluentcrm-de` 1.1.2. FluentSMTP setzt den
 * Trenner als Text `to` („bis“) in einen Kasten, der für ein einzelnes
 * Zeichen gebaut ist. Der Text wird ausgeblendet und durch den Pfeil ersetzt.
 */
function pcl_fluent_de_smtp_trenner_css() {
    return <<<'CSS'
.el-date-editor .el-range-separator {
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
.el-range-editor--small .el-range-separator::after {
    font-size: 12px;
}
CSS;
}

function pcl_fluent_de_smtp_trenner_ausgeben() {
    if (!pcl_fluent_de_smtp_datum_aktiv()) {
        return;
    }
    echo '<style id="pcl-fluentsmtp-de-trenner">' . pcl_fluent_de_smtp_trenner_css() . "</style>\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- static CSS
}
add_action('admin_head', 'pcl_fluent_de_smtp_trenner_ausgeben', 20);
