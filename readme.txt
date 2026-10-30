=== PC'L Übersetzungen für Fluent-Plugins ===
Contributors: blocoder
Tags: fluentcommunity, fluentcrm, fluentauth, fluentsmtp, fluentsnippets, deutsch
Requires at least: 6.5
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 2.6.18
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

= 2.6.18 =
* Codes in Mails werden auf dem Handy nicht mehr als Telefonnummer verlinkt: jetzt auch der Verifizierungscode der Registrierung in FluentCommunity und der 2FA-Code von FluentAuth in einer eigenen Mailvorlage. Jede Ziffer steht in einem eigenen Element, ein kopierter Code bleibt genau die Ziffern.
* Kataloge unverändert; setzt wie 2.6.14 **FluentCRM 3.3.0** voraus.

= 2.6.17 =
* FluentCRM: „Benutzerdefinierte Felder“ in den Einstellungen, im Kontaktprofil und in den E-Mail-Einstellungen ist auf Seiten ohne FluentCRM Pro wieder deutsch. FluentCRM 3.3.0 hat den Schlüssel dafür umbenannt, die Oberfläche fragt aber den alten ab; mit Pro fiel das nicht auf. Das Plugin ergänzt den fehlenden Schlüssel, solange FluentCRM ihn nicht selbst liefert.
* Setzt wie 2.6.14 **FluentCRM 3.3.0** voraus.

= 2.6.16 =
* FluentCRM: Der Bereich für SMS und WhatsApp heißt jetzt „Messaging“ statt „Nachrichten“, auch in Zusammensetzungen („Messaging-Modul aktivieren“, „Einstellungen > Messaging“). „Nachrichten“ war irreführend, denn E-Mails sind auch Nachrichten. Wo es um einzelne Nachrichten geht, etwa „WhatsApp-Nachrichten“ oder den Posteingang, bleibt es dabei.
* Setzt wie 2.6.14 **FluentCRM 3.3.0** voraus.

= 2.6.15 =
* Behoben: Nach einem Sprachwechsel innerhalb eines Seitenaufrufs (`switch_to_locale()`, etwa wenn WordPress eine Mail in der Sprache des Empfängers verschickt) standen FluentCRM und die anderen spät geladenen Plugins englisch da, wenn die neue Sprache eine andere deutsche Variante war, zum Beispiel „Deutsch (Sie)“. Der Ordner mit den Übersetzungen gilt jetzt für jede deutsche Sprache, nicht nur für die beim Start.
* FluentCRM, Sie-Fassung: drei Sätze mit falschem Pronomen korrigiert („Sie haben sich … abgemeldet“, „Warum melden Sie sich ab?“, „Danke, dass Sie sich für FluentCRM entschieden haben“).
* Setzt wie 2.6.14 **FluentCRM 3.3.0** voraus.

= 2.6.14 =
* FluentCRM 3.3.0 nachgezogen: 114 neue Einträge, vor allem der neue Integrationskatalog, die Bestellansicht für FluentCart im Kontaktprofil, Filter für benutzerdefinierte Unternehmensfelder, Tracking-Stufen je Kampagne und die Zuordnung von WordPress-Benutzern nur über bestätigte E-Mail-Adressen. Der Katalog ist wieder vollständig: 4.346 von 4.346 Einträgen, in Du und in Sie.
* **Wichtig beim Einspielen:** Diese Fassung setzt **FluentCRM 3.3.0** voraus. 39 Texte, die FluentCRM 3.2.6 noch abfragt, kennt der Katalog nicht mehr; auf einer Installation mit 3.2.6 stünden sie wieder englisch da. Erst FluentCRM aktualisieren, dann dieses Plugin.

= 2.6.13 =
* Die Update-Prüfung wird nur noch dort geladen, wo Updates angezeigt, geprüft oder eingespielt werden: im Backend (auch über admin-ajax), im WP-Cron und in WP-CLI. Im Frontend und in der REST-API hatte sie nichts zu tun und kostete trotzdem bei jedem Aufruf. Updates über die Plugin-Seite, die automatischen Hintergrund-Updates und `wp plugin update` funktionieren wie bisher. In der Werkzeugleiste, die angemeldete Administratoren im Frontend sehen, zählt ein verfügbares Update dieses Plugins nicht mehr mit; im Backend schon.
* An den Übersetzungen ändert sich nichts.

= 2.6.12 =
* FluentAuth 3.1.0 nachgezogen: 126 neue Einträge, vor allem der neue Bot-Schutz mit Cloudflare Turnstile, die Warnung bei veralteten PHP-Versionen und die Seite für den Anmeldelink.
* FluentSnippets 10.58 nachgezogen: 39 neue Einträge, vor allem zum Anlegen und Bearbeiten von Snippets über die REST-API.
* **Wichtig beim Einspielen:** Diese Fassung setzt **FluentAuth 3.1.0** und **FluentSnippets 10.58** voraus. Mit den Vorversionen stünden 65 Texte wieder englisch da. Erst die beiden Plugins aktualisieren, dann dieses.

= 2.6.11 =
* Foren-Seiten: Die Ansicht „Unified“ heißt jetzt „Ganze Fläche“ statt „Lektion“, die Ansicht „Full Width“ „Ohne Kachel“ statt „Volle Breite“. Die breiteste Ansicht ist „Ganze Fläche“; „Ohne Kachel“ nimmt nur die Kachel weg und bleibt in der normalen Spalte.

= 2.6.10 =
* Analysen: „Contributing members“ heißt jetzt „Aktive Mitglieder“ statt „Beteiligte Mitglieder“, die Spalten in den Foren-Tabellen „Aktive“ und „Anteil aktiv“.

= 2.6.9 =
* Analysen: Der erste Kommentar unter einem Beitrag heißt jetzt „Kommentar“ statt „Antwort“, etwa „Beiträge mit Kommentar“ und „Zeit bis zum ersten Kommentar“, so wie der Feed-Filter „Ohne Kommentar“.
* Die Feed-Sortierung „Most Replied“ heißt jetzt „Meistkommentiert“.

= 2.6.8 =
* FluentCommunity 2.12.0, FluentCommunity Pro 2.12.0 und FluentMessaging 2.12.0 nachgezogen: 243 neue Einträge, vor allem der neue Bereich „Insights“, der hier „Analysen“ heißt.
* „Card“ heißt jetzt durchgehend „Kachel“ statt „Karte“, Meldungen über Beiträge und Kommentare heißen „Meldung“ statt „Bericht“, und gezählte Reaktionen heißen „Likes“.
* **Wichtig beim Einspielen:** Diese Fassung setzt **FluentCommunity 2.12.0** voraus. Auf einer Installation mit 2.11.0 stünden 38 Texte wieder englisch da, darunter die alten Analysen. Erst FluentCommunity aktualisieren, dann dieses Plugin.

= 2.6.7 =
* FluentAuth: Die drei Hinweis-Mails an die Verwaltung („Anmeldung erfolgreich“, „Anmeldung gesperrt“ und der Anmeldebericht) sind jetzt deutsch: Betreff, Einleitung und Beschriftungen. FluentAuth setzt diese Texte ohne Übersetzungsfunktion zusammen, deshalb erreicht sie kein Katalog; sie werden beim Versand ersetzt.
* Anmeldemethode und Benutzerrolle stehen dort nicht mehr als Bezeichner („two_factor_email“, „administrator“), sondern lesbar („Code per E-Mail“, „Administrator“).

= 2.6.6 =
* „Clone“ heißt jetzt auch „Duplizieren“. FluentCRM benutzt zwei englische Wörter für dieselbe Sache – „Duplicate“ im Listenmenü von Kampagnen, Vorlagen, Sequenzen und Segmenten, „Clone“ im Blockmenü des Automations-Editors. Im Deutschen steht dafür ein Wort.
* Die Bestätigungen dazu sagen einheitlich „dupliziert“, und der Titel einer Kopie heißt „[Duplikat] …“ statt „[Kopie] …“. Bei Automationen bleibt „[Kopie] …“, weil der Hersteller dort ein anderes Wort verwendet.

= 2.6.5 =
* FluentCRM 3.2.6 nachgezogen. Diese Fassung hat die englischen Texte flächendeckend umformuliert: 1.576 Zeichenketten sind neu, 2.215 entfallen. Der Katalog ist vollständig: 4.271 von 4.271 Einträgen, in Du und in Sie.
* **Wichtig beim Einspielen:** Diese Fassung setzt **FluentCRM 3.2.6** voraus. Auf einer Installation mit 3.2.5 stünden rund 2.190 Texte wieder englisch da, weil ein Katalog immer über den Wortlaut nachschlägt. Erst FluentCRM aktualisieren, dann dieses Plugin.
* FluentSMTP: „Nutzer“ heißt jetzt „Anwender“, wo die Anwender des Plugins gemeint sind und nicht Konten oder Mitglieder.

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
