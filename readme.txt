=== PC'L Übersetzungen für Fluent-Plugins ===
Contributors: blocoder
Tags: fluentcommunity, fluentcrm, fluentauth, fluentsmtp, fluentsnippets, deutsch
Requires at least: 6.5
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 2.5.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Deutsche Übersetzungen für neun Fluent-Plugins – die vor dem offiziellen Sprachpaket laden. Jede einzeln abschaltbar.

== Description ==

13.444 übersetzte Zeichenketten für neun Plugins, ausgeliefert als ein Plugin statt über Loco Translate. Für FluentCRM, FluentSMTP und FluentSnippets zusätzlich in der Sie-Anrede.

**Warum das nötig ist:** WordPress fragt für eine Textdomain mehrere Kataloge der Reihe nach ab und nimmt die erste Datei, die eine Zeichenkette kennt. Wer zuerst lädt, gewinnt. Dieses Plugin lädt auf `plugins_loaded` mit Priorität 1 – vor dem Sprachpaket von wordpress.org. Damit hält die eigene Terminologie, und das Sprachpaket füllt nur noch Lücken.

**Wie übersetzt wurde:** Du-Form. `Space` heißt Forum, `Member` heißt Mitglied. Ein eigenes Glossar sorgt für Konsistenz, jede Zeichenkette ist von Hand nachgearbeitet.

Das Plugin legt außerdem den Zustimmungs-Link bei der Registrierung auf die echte Seite mit den Nutzungsbedingungen.

Unabhängiges Projekt, keine Verbindung zu WPManageNinja.

== Installation ==

1. Das ZIP aus den GitHub-Releases herunterladen – aus dem Bereich „Releases“, nicht über „Code → Download ZIP“. Im Quellcode-Archiv fehlen die gebauten Kataloge.
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

Für FluentCRM, FluentSMTP und FluentSnippets ja. Steht die Seite auf `de_DE_formal`, wird sie geladen – und seit 2.3.0 lässt sie sich auch auf einer Du-Seite für diese Plugins erzwingen (Spalte „Anrede“ in den Einstellungen). Die übrigen sechs Kataloge gibt es nur in der Du-Form; seit 2.4.0 laden sie dort in der Du-Fassung, statt englisch zu bleiben.

= Kann ich einzelne Übersetzungen abschalten? =

Ja, jede für sich. Drei Zustände je Plugin: „Deutsche Übersetzung aktiv“ (Vorgabe, schaltet sich ein, sobald das Plugin da ist), „Fremde Übersetzung“ (unsere Fassung weg, das Sprachpaket von wordpress.org greift weiter) und „Keine Übersetzung zulassen“ (das Plugin bleibt englisch).

== Changelog ==

= 2.5.0 =
* Neu: FluentCRM. 4.784 Zeichenketten für die kostenlose Fassung, in Du und in Sie. Für FluentCRM Pro bleibt es beim eigenen Plugin.
* Der Katalog wird erst gelesen, wenn der erste Text daraus gebraucht wird. Auf einer Seite ohne FluentCRM kostet er nichts.
* Einheitliche Namen für die Farbschemata: „Ozeanblau“ in einem Wort wie „Himmelblau“, „Smaragd“ statt „Smaragd-Essenz“, und der Zusatz der dunklen Fassungen durchgehend klein – „(dunkel)“.

= 2.4.0 =
* Neu: Fehlt der Katalog zur Sprache der Seite, wird jetzt die Du-Fassung geladen, statt dass der Text englisch bleibt. Das betrifft vor allem Seiten auf „Deutsch (Sie)“ – dort waren bisher alle Übersetzungen englisch, die es nur in der Du-Form gibt.
* Dieselbe Reihenfolge gilt überall: erzwungene Anrede, sonst die Sprache der Seite, sonst die Du-Fassung.
* Der Rückfall greift auch dort, wo Loco Translate mitläuft. Eine Fassung, die in Loco gepflegt ist, behält weiterhin Vorrang – eingegriffen wird nur, wo gar keine Datei liegt.

= 2.3.1 =
* Behoben: Die Plugin-Seite im Backend lief in einen kritischen Fehler. Der Hinweis, welche Kataloge greifen, rief eine Funktion aus 1.x auf, die es seit 2.0.0 nicht mehr gibt. Betroffen war allein die Übersicht unter „Plugins“ – Übersetzung, Portal und Frontend haben durchgehend funktioniert.
* Der Hinweis zählt jetzt die tatsächlich wirksamen Übersetzungen auf und berücksichtigt dabei die erzwungene Anrede.

= 2.3.0 =
* Anrede je Plugin erzwingbar: „wie die Seite“ (Vorgabe), „immer Du“ oder „immer Sie“. Eine Seite in der Du-Form kann so für ein einzelnes Plugin die Sie-Fassung zeigen.
* Wählbar nur dort, wo beide Fassungen vorliegen – zurzeit FluentSMTP und FluentSnippets. Fehlt die gewünschte Fassung, gilt wieder die Anrede der Seite, statt dass der Text englisch wird.

= 2.2.1 =
* Klarere Beschriftungen in der Auswahl: „Deutsche Übersetzung aktiv“, „Fremde Übersetzung (WordPress, Plugin)“ und „Keine Übersetzung zulassen“. Die Statusspalte spricht jetzt dieselbe Sprache.

= 2.2.0 =
* Die Kataloge für FluentAuth, FluentSMTP und FluentSnippets werden erst gelesen, wenn der erste Text daraus gebraucht wird. Auf einer Seite, die keinen davon anzeigt, bleiben sie ungelesen – das sind 339 KB, die bisher jeder Aufruf mitgeschleppt hat.
* Die übrigen fünf Kataloge laden weiterhin voraus. Bei ihnen füllt das Sprachpaket von wordpress.org Lücken auf, und das verlangt eine feste Reihenfolge.

= 2.1.0 =
* Einstellungsseite: eine Zeile je Übersetzung mit den drei Zuständen, dazu Status und Anrede. Unter Einstellungen → PC’L Übersetzungen.
* Ist ein Plugin nicht installiert, steht seine Zeile grau da – die Einstellung bleibt aber erhalten und greift wieder, sobald das Plugin da ist.
* Die Einstellungsseite gehört jetzt dem Plugin statt der FluentCommunity-Zusatzdatei. Ohne FluentCommunity gab es bis 2.0.0 gar keine.

= 2.0.0 =
* Zusammenführung: Dieses Plugin bringt jetzt auch die Übersetzungen für FluentAuth, FluentSMTP und FluentSnippets mit. Die drei bisherigen Einzel-Plugins werden beim Aktivieren deaktiviert und können gelöscht werden.
* Jede Übersetzung schaltet sich selbst ein, sobald das zugehörige Plugin installiert ist, und lässt sich einzeln abschalten oder ganz auf Englisch stellen.
* Was ein Katalog nicht kann – Datumsformate, die Fußzeile der Anmeldecode-Mails, relative Zeitangaben – wird nur noch geladen, wenn das zugehörige Plugin da ist.
* Beim Sprachwechsel werden die Kataloge jetzt auch für FluentCommunity neu geladen. Das fehlte bis 1.7.1 und betraf E-Mails an Empfänger mit abweichender Sprache.

= 1.7.1 =
* Hinweis in der Verwaltung und in der Plugin-Liste, wenn das Plugin aus dem Quellcode-Archiv statt aus den Releases installiert wurde und die gebauten Kataloge deshalb fehlen.

= 1.7.0 =
* Angepasst an FluentCommunity und FluentCommunity Pro 2.11.0: 115 neue Zeichenketten. Das neue Quiz-Modul (Fragetypen, Lückentext, Zuordnung, Reihenfolge, Quiz-Aufbau) und das Menü je Forum (Hauptmenü, weitere Links, Platzierung).
* Auf FluentCommunity 2.10.01 erscheinen drei Zeichenketten des Forenchats englisch, die der Hersteller in 2.11.0 ersetzt hat. Erst FluentCommunity aktualisieren, dann dieses Plugin.

= 1.6.1 =
* CRM-Profil in der Seitenleiste eines Mitgliedsprofils: Die Überschrift „Tags“ heißt wieder „Tags“ statt „Schlagwörter“, so wie in FluentCRM selbst.

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
