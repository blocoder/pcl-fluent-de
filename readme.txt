=== PC'L Übersetzungen für Fluent-Plugins ===
Contributors: blocoder
Tags: fluentcommunity, fluentmessaging, fluentplayer, deutsch, übersetzung
Requires at least: 6.5
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.6.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Eine vollständige deutsche Übersetzung für FluentCommunity, FluentMessaging und FluentPlayer – die vor dem offiziellen Sprachpaket lädt.

== Description ==

5.326 übersetzte Zeichenketten für fünf Plugins, ausgeliefert als eigenes Plugin statt über Loco Translate.

**Warum das nötig ist:** WordPress fragt für eine Textdomain mehrere Kataloge der Reihe nach ab und nimmt die erste Datei, die eine Zeichenkette kennt. Wer zuerst lädt, gewinnt. Dieses Plugin lädt auf `plugins_loaded` mit Priorität 1 – vor dem Sprachpaket von wordpress.org. Damit hält die eigene Terminologie, und das Sprachpaket füllt nur noch Lücken.

**Wie übersetzt wurde:** Du-Form. `Space` heißt Forum, `Member` heißt Mitglied. Ein eigenes Glossar sorgt für Konsistenz, jede Zeichenkette ist von Hand nachgearbeitet.

Das Plugin legt außerdem den Zustimmungs-Link bei der Registrierung auf die echte Seite mit den Nutzungsbedingungen.

Unabhängiges Projekt, keine Verbindung zu WPManageNinja.

== Installation ==

1. Das ZIP aus den GitHub-Releases herunterladen.
2. Im Backend unter Plugins → Installieren → Plugin hochladen einspielen und aktivieren.

Die Seite muss auf `de_DE` stehen. Das Sprachpaket von wordpress.org muss nicht deinstalliert werden.

Ab Version 1.5.0 meldet sich jedes weitere Update von selbst.

== Frequently Asked Questions ==

= Muss ich das offizielle Sprachpaket entfernen? =

Nein. Es bleibt aktiv und deckt rund 950 Zeichenketten ab, die sonst englisch blieben. Es kommt nur dort zum Zug, wo dieses Plugin nichts hat.

= Wird das Paket auf Echtheit geprüft? =

Nein. WordPress bringt dafür einen Rahmen mit, wendet ihn aber nur auf Downloads von wordpress.org an. Was das Paket schützt, ist HTTPS und GitHub.

= Warum erscheinen einzelne Texte englisch? =

Ein Katalog gehört zu einer Plugin-Version. Ändert der Hersteller einen englischen Text, ist das für gettext ein neuer Schlüssel, und der alte fällt aus dem Katalog. Am besten erst die Fluent-Plugins aktualisieren, dann dieses.

= Gibt es eine Sie-Fassung? =

Nein, nur `de_DE` in der Du-Form.

== Changelog ==

= 1.6.0 =
* Das Plugin hält keine fremden Textdomains mehr auf Englisch. Bis 1.5.3 unterband es die deutschen Übersetzungen von FluentCRM, FluentCRM Pro und FluentSnippets; die Funktion `pcl_fluent_de_blocked_domains()` und der Filter `pcl_fluent_de/blocked_domains` entfallen.
* Wer FluentCRM oder FluentSnippets weiter englisch haben will, braucht dafür jetzt einen eigenen Filter (Beispiel im README). Sonst zeigt FluentCRM nach diesem Update die deutsche Fassung, die es selbst mitbringt.

= 1.5.3 =
* Block-Editor: Die Reiterüberschrift heißt jetzt „Foren-Seite“, „Lektion“ und „Sperrbildschirm“ statt „Space Page“, „Lesson“ und „Lockscreen“. FluentCommunity gibt sie als festen englischen Text aus.
* Block-Editor: „Kommentare aktivieren“ in der Seitenleiste einer Foren-Seite war englisch, weil der Schlüssel in der Textliste des Editors fehlt.
* Die Ansicht „Unified“ für Foren-Seiten heißt jetzt „Lektion“.

= 1.5.2 =
* Angepasst an FluentCommunity und FluentCommunity Pro 2.10.01: 136 neue Zeichenketten, vor allem die neuen Seiten innerhalb eines Forums.
* Auf FluentCommunity 2.9.1 erscheinen drei Zeichenketten englisch, die der Hersteller in 2.10.01 ersetzt hat. Erst FluentCommunity aktualisieren, dann dieses Plugin.

= 1.5.1 =
* In der Plugin-Liste steht jetzt „Einstellungen“ neben „Deaktivieren“.

= 1.5.0 =
* Das Plugin meldet Updates jetzt selbst und holt sie von GitHub.
* Übersetzungen unverändert.

= 1.4.3 =
* Erste öffentliche Fassung, angepasst an FluentCommunity 2.9.1.
