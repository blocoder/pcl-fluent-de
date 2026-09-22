<?php
/**
 * German relative times in the FluentSnippets admin ("vor 4 Tagen").
 *
 * Since 1.1.0 (17.09.2026, decision PC'L). FluentSnippets formats the
 * "Updated" column and the "Updated At:" line of the grouped view with
 * `dayjs.utc(…).local().fromNow()`. Its dayjs lives inside the webpack bundle
 * and has no locale, so the text is "4 days ago" whatever the catalogue says.
 * Unlike FluentCRM (`window.dayjs`, see pcl-fluentcrm-de/datum-de.php) there is
 * no global handle: the English words are attached to dayjs' private `en`
 * locale, and the helper method is bound into every Vue component on creation.
 *
 * So this script works on the rendered text instead, and deliberately narrow:
 *
 * - only text nodes whose whole content is one of dayjs' fixed English
 *   phrases ("a few seconds ago", "in an hour", "4 days ago", …),
 * - only in the two places that show them: inside `.fsnip_row_when`, or right
 *   after the screen-reader label `.fsnip_sr_only` ("Aktualisiert am:"),
 * - only inside `#fluent_snippets_app`, only on `?page=fluent-snippets`, only
 *   for a German locale.
 *
 * A MutationObserver catches every re-render. Vue compares virtual nodes, not
 * the DOM, so a German text stays until the value itself changes; then Vue
 * writes English again and the observer replaces it once more.
 *
 * Reported upstream (issue 57 at WPManageNinja/easy-code-manager). Can be
 * switched off via `pcl_fluentsnippets_de/relative_zeiten`.
 */

if (!defined('ABSPATH')) {
    exit;
}

function pcl_fluent_de_snippets_relativ_script() {
    return <<<'JS'
(function () {
    'use strict';

    var wurzel = document.getElementById('fluent_snippets_app');
    if (!wurzel || !window.MutationObserver) {
        return;
    }

    // dayjs relativeTime (en), after "vor"/"in" always in the dative.
    var EINZELN = {
        'a few seconds': 'ein paar Sekunden',
        'a minute': 'einer Minute',
        'an hour': 'einer Stunde',
        'a day': 'einem Tag',
        'a month': 'einem Monat',
        'a year': 'einem Jahr'
    };
    var MEHRERE = {
        minutes: 'Minuten',
        hours: 'Stunden',
        days: 'Tagen',
        months: 'Monaten',
        years: 'Jahren'
    };
    var MUSTER = /^(in )?(a few seconds|a minute|an hour|a day|a month|a year|(\d+) (minutes|hours|days|months|years))( ago)?$/;

    function deutsch(text) {
        var t = text.trim();
        var m = MUSTER.exec(t);
        // Exactly one of "in …" (future) and "… ago" (past).
        if (!m || !!m[1] === !!m[5]) {
            return null;
        }
        var dauer = m[3] ? m[3] + ' ' + MEHRERE[m[4]] : EINZELN[m[2]];
        var neu = (m[1] ? 'in ' : 'vor ') + dauer;
        return text.replace(t, neu);
    }

    function zustaendig(knoten) {
        var eltern = knoten.parentElement;
        if (!eltern) {
            return false;
        }
        if (eltern.classList.contains('fsnip_row_when')) {
            return true;
        }
        var davor = knoten.previousSibling;
        while (davor && davor.nodeType === 3 && !davor.nodeValue.trim()) {
            davor = davor.previousSibling;
        }
        return !!(davor && davor.nodeType === 1 && davor.classList.contains('fsnip_sr_only'));
    }

    function pruefen(knoten) {
        if (knoten.nodeType !== 3 || !zustaendig(knoten)) {
            return;
        }
        var neu = deutsch(knoten.nodeValue);
        if (neu !== null && neu !== knoten.nodeValue) {
            knoten.nodeValue = neu;
        }
    }

    function durchgehen(start) {
        if (start.nodeType === 3) {
            pruefen(start);
            return;
        }
        if (start.nodeType !== 1) {
            return;
        }
        var laeufer = document.createTreeWalker(start, NodeFilter.SHOW_TEXT);
        while (laeufer.nextNode()) {
            pruefen(laeufer.currentNode);
        }
    }

    new MutationObserver(function (aenderungen) {
        aenderungen.forEach(function (a) {
            if (a.type === 'characterData') {
                pruefen(a.target);
            } else {
                a.addedNodes.forEach(durchgehen);
            }
        });
    }).observe(wurzel, { childList: true, subtree: true, characterData: true });

    durchgehen(wurzel);
})();
JS;
}

/**
 * Print the script at the end of the FluentSnippets admin page.
 *
 * admin_footer runs after the page markup, so `#fluent_snippets_app` exists.
 * Whether the app bundle has run yet does not matter: the observer sees
 * whatever it renders later, and the first pass covers what is already there.
 */
function pcl_fluent_de_snippets_relativ_ausgeben() {
    if (0 !== strpos(determine_locale(), 'de_')) {
        return;
    }
    // Read-only routing check, no state change - a nonce adds nothing here.
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $seite = isset($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : '';
    if ('fluent-snippets' !== $seite) {
        return;
    }
    if (!apply_filters('pcl_fluentsnippets_de/relative_zeiten', true)) {
        return;
    }
    wp_print_inline_script_tag(pcl_fluent_de_snippets_relativ_script(), array('id' => 'pcl-fluentsnippets-de-relativ'));
}
add_action('admin_footer', 'pcl_fluent_de_snippets_relativ_ausgeben', 1);
