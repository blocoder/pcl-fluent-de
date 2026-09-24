# Änderungen

Die Versionsnummer steigt bei **jeder** Katalogänderung, auch wenn sich am
Plugin-Code nichts tut. Ein Archiv, dessen Name nichts über seinen Inhalt
sagt, ist beim Weitergeben wertlos.

Frühere Fassungen liefen nicht öffentlich; diese Liste beginnt mit dem ersten
veröffentlichten Stand.

## 2.5.0

**Neu: FluentCRM.** 4.784 Zeichenketten für die kostenlose Fassung, in Du und
in Sie. Der Katalog wird erst gelesen, wenn der erste Text daraus gebraucht
wird – auf einer Seite ohne FluentCRM kostet er nichts.

Für **FluentCRM Pro** bleibt es beim eigenen Plugin. Die Trennung verläuft
entlang der Textdomain: `fluent-crm` hier, `fluentcampaign-pro` dort. Ein paar
allgemeine Beschriftungen, die Pro aus der freien Domain holt – Status, Datum,
Gesamt – sind damit mit übersetzt.

**Einheitliche Namen für die Farbschemata.** „Ozeanblau“ steht jetzt in einem
Wort, wie „Himmelblau“ daneben; aus „Smaragd-Essenz“ wurde „Smaragd“; und der
Zusatz der dunklen Fassungen ist durchgehend klein – „(dunkel)“ statt
teils „(Dunkel)“.

## 2.4.0

**Fehlt der Katalog zur Sprache der Seite, gilt jetzt die Du-Fassung.** Bisher
blieb die Übersetzung in diesem Fall ganz aus, und der Text stand englisch da.
Betroffen waren vor allem Seiten auf „Deutsch (Sie)“: Sechs der acht
Übersetzungen gibt es nur in der Du-Form, und dort griff keine davon.

Die Reihenfolge ist überall dieselbe – in der Plugin-Liste, beim Laden im
Voraus und beim Laden auf Abruf: die erzwungene Anrede, sonst die Sprache der
Seite, sonst die Du-Fassung. Auf einer nicht-deutschen Seite ändert sich
nichts; dort wäre ein deutscher Katalog falsch.

Der Rückfall greift auch dort, wo **Loco Translate** mitläuft. Eine Fassung,
die in Loco gepflegt ist, behält ihren Vorrang – eingegriffen wird nur an
Stellen, an denen gar keine Datei liegt.

## 2.3.1

**Behoben: kritischer Fehler auf der Plugin-Seite.** Die Zeile, die anzeigt,
welche Kataloge gerade greifen, rief eine Funktion aus der 1.x-Reihe auf, die
es seit 2.0.0 nicht mehr gibt. Wer im Backend „Plugins“ öffnete, bekam an
dieser Stelle statt des Hinweises eine Fehlermeldung.
Betroffen war allein diese Übersicht – die Übersetzungen
selbst, das Portal und die Website haben durchgehend funktioniert, und das
Plugin blieb aktiv.

Der Hinweis zählt jetzt die tatsächlich wirksamen Übersetzungen auf und
berücksichtigt dabei die erzwungene Anrede: Steht ein Plugin auf „immer Sie“,
nennt die Zeile den Katalog, der wirklich gelesen wird.

## 2.3.0

**Anrede je Plugin erzwingbar.** Wo beide Fassungen vorliegen – zurzeit
FluentSMTP und FluentSnippets –, lässt sich in den Einstellungen wählen: „wie
die Seite“ (Vorgabe), „immer Du“ oder „immer Sie“. Eine Seite in der Du-Form
kann damit für ein einzelnes Plugin die Sie-Fassung zeigen, ohne dass sonst
etwas umschaltet. Fehlt die gewünschte Fassung, gilt wieder die Anrede der
Seite, statt dass der Text englisch wird.

## 2.2.1

**Klarere Beschriftungen** in der Auswahl: „Deutsche Übersetzung aktiv“,
„Fremde Übersetzung (WordPress, Plugin)“ und „Keine Übersetzung zulassen“.

## 2.2.0

**Kataloge auf Abruf.** Die Kataloge für FluentAuth, FluentSMTP und
FluentSnippets werden erst gelesen, wenn der erste Text daraus gebraucht wird.
Auf einer Seite, die keinen davon anzeigt, bleiben sie ungelesen – das sind
339 KB, die bisher jeder Aufruf mitgeschleppt hat.

Die übrigen fünf laden weiterhin voraus. Bei ihnen füllt das Sprachpaket von
wordpress.org Lücken auf, und das verlangt die Reihenfolge „eigener Katalog
zuerst“. Beim Laden auf Abruf liest WordPress genau eine Datei.

## 2.1.0

**Eine Einstellungsseite mit einer Zeile je Übersetzung**, unter
Einstellungen → PC’L Übersetzungen. Ist ein Plugin nicht installiert, steht
seine Zeile grau da – die Einstellung bleibt erhalten und greift wieder,
sobald das Plugin da ist.

## 2.0.0

**Vier Plugins werden eins.** Dieses Plugin bringt jetzt auch die
Übersetzungen für FluentAuth, FluentSMTP und FluentSnippets mit; die
bisherigen Einzel-Plugins `pcl-fluentauth-de`, `pcl-fluentsmtp-de` und
`pcl-fluentsnippets-de` werden beim Aktivieren deaktiviert und können
gelöscht werden. Acht Textdomains, 8.660 Zeichenketten in der Du-Fassung.

Jede Übersetzung schaltet sich selbst ein, sobald ihr Plugin installiert ist,
und lässt sich einzeln abschalten oder ganz auf Englisch stellen. Was ein
Katalog nicht kann – Datumsformate, die Fußzeile der Anmeldecode-Mails,
relative Zeitangaben – wird nur noch geladen, wenn das zugehörige Plugin da
ist.

Solange eines der drei Vorgänger-Plugins noch aktiv ist, hält sich dieses für
dessen Textdomain heraus. Die Umstellung ist damit unabhängig von der
Reihenfolge, in der aktiviert wird.

Außerdem: Beim Sprachwechsel werden die Kataloge jetzt auch für
FluentCommunity neu geladen. Das fehlte bis 1.7.1 und betraf E-Mails an
Empfänger mit abweichender Sprache.

## 1.7.1

**Ein Hinweis, wenn die gebauten Kataloge fehlen.** Wer das Plugin aus dem
Quellcode-Archiv von GitHub installiert („Code → Download ZIP“) statt aus den
Releases, bekommt nur die `.po`-Dateien und damit ein Plugin, das nichts
übersetzt. Von außen war das nicht zu erkennen – die Plugin-Liste meldete
lediglich „Keine Kataloge gefunden“, was nach einem Problem mit der Sprache
aussieht. Jetzt benennt das Plugin die Ursache, in der Plugin-Liste und als
Hinweis in der Verwaltung, samt Link auf das richtige Archiv.

An den Katalogen ändert sich nichts.

## 1.7.0

**Angepasst an FluentCommunity und FluentCommunity Pro 2.11.0.** 115 neue
Zeichenketten, 99 davon in `fluent-community`, 16 in `fluent-community-pro`.

Das Update bringt zwei neue Bereiche. Der Fragen-Editor der Kurse kennt jetzt
Lückentext, Zuordnung, Bildzuordnung, Reihenfolge und Wahr/Falsch, dazu die
Wahl zwischen „Alle auf einmal“ und „Nacheinander“. Und jedes Forum bekommt
ein eigenes Menü aus Hauptmenü und weiteren Links.

Zur Terminologie: Die beiden Spalten einer Zuordnungsfrage heißen **Vorgabe**
und **Zuordnung**, die Leerstelle im Lückentext heißt **Lücke**. `Primary
Menu` ist das **Hauptmenü** (die Tab-Leiste oben im Forum), `Secondary Links`
sind die **weiteren Links** in der Unterleiste darunter.

**Erst FluentCommunity aktualisieren, dann dieses Plugin.** Auf 2.10.01
erscheinen drei Zeichenketten rund um den Forenchat englisch, weil der
Hersteller sie in 2.11.0 ersetzt hat und sie damit aus dem Katalog gefallen
sind.

## 1.6.1

Im CRM-Profil, das FluentCommunity Pro in der Seitenleiste eines
Mitgliedsprofils zeigt, heißt die Überschrift „Tags“ jetzt „Tags“ statt
„Schlagwörter“ – dieselbe Bezeichnung wie in FluentCRM. Sonst unverändert.

## 1.6.0

**Keine Sperren mehr für fremde Plugins.** Bis 1.5.3 unterband das Plugin die
deutschen Übersetzungen von FluentCRM, FluentCRM Pro und FluentSnippets. Das
entfällt, samt der Funktion `pcl_fluent_de_blocked_domains()` und dem Filter
`pcl_fluent_de/blocked_domains`. Das Plugin kümmert sich nur noch um
FluentCommunity, FluentCommunity Pro, FluentMessaging und FluentPlayer.

**Folge nach dem Update:** FluentCRM zeigt die deutsche Fassung, die es selbst
mitbringt, sofern keine andere Übersetzung früher lädt. Wer es englisch haben
will, findet im README ein Snippet dafür. Die Kataloge sind unverändert.

## 1.5.3

**Block-Editor:** Zwei Stellen waren englisch, die keine Übersetzungsdatei
erreicht – dieses Plugin korrigiert sie jetzt direkt.

- Die Überschrift des ersten Reiters heißt „Foren-Seite“, „Lektion“ und
  „Sperrbildschirm“ statt „Space Page“, „Lesson“ und „Lockscreen“.
  FluentCommunity gibt diese Beschriftungen als festen englischen Text aus.
- „Kommentare aktivieren“ in der Seitenleiste einer Foren-Seite. Der Editor
  liest seine Texte aus einer eigenen Liste, und dort fehlte genau dieser
  Eintrag.

Beide Korrekturen greifen nur, solange FluentCommunity den englischen Text
liefert, und halten sich heraus, sobald der Hersteller sie selbst übersetzbar
macht.

Außerdem heißt die Ansicht „Unified“ für Foren-Seiten jetzt **„Lektion“** – sie
ist ausdrücklich einer Kurslektion nachgebildet.

## 1.5.2

**Angepasst an FluentCommunity und FluentCommunity Pro 2.10.01.** 136 neue
Zeichenketten, vor allem für die neuen **Seiten innerhalb eines Forums**:
Sichtbarkeit, Slug, Menüeintrag, SEO-Beschreibung und die vier Ansichten
(Durchgehend, Standard, Klassisch, Volle Breite). Dazu die vorgerenderten
Foren- und Kursseiten, das Aktivieren von FluentNotify und neue Beschriftungen
für Bildschirmleser.

**Reihenfolge beachten:** Auf FluentCommunity 2.9.1 erscheinen mit dieser
Fassung drei Zeichenketten englisch, die der Hersteller in 2.10.01 ersetzt hat
(`Edit Article`, `New Article` und der Hinweis auf blockierte
Benachrichtigungen). Erst FluentCommunity aktualisieren, dann dieses Plugin.

## 1.5.1

In der Plugin-Liste steht jetzt **Einstellungen** neben *Deaktivieren* – der
kurze Weg zu der Seite, auf der die Adresse der Nutzungsbedingungen liegt.

## 1.5.0

**Das Plugin meldet Updates jetzt selbst.** Es fragt bei GitHub nach dem
neuesten Release, und WordPress zeigt das Update unter *Plugins* an wie bei
jedem anderen – Einspielen mit einem Klick, automatische Updates lassen sich
im Backend einschalten.

Dafür liegt `plugin-update-checker` 5.7 von Jānis Elsts bei (MIT). Die
Übersetzungen selbst sind unverändert.

Ein Hinweis zur Ehrlichkeit: **Es findet keine Signatur- oder
Prüfsummenkontrolle statt.** WordPress bringt dafür einen Rahmen mit, wendet
ihn aber nur auf Downloads von wordpress.org an. Was das Paket schützt, ist
HTTPS und GitHub – nicht mehr und nicht weniger als bei jedem anderen Plugin,
das von GitHub aktualisiert wird.

**Diese Fassung muss noch von Hand eingespielt werden.** Ab 1.5.0 meldet sich
jede weitere von selbst.

## 1.4.3

Erste öffentliche Fassung.

**Angepasst an FluentCommunity 2.9.1.** Das Update ersetzt die tote
Push-Oberfläche durch einen vierstufigen Einrichtungspfad im Admin: zehn
Zeichenketten neu, sieben entfallen. Die anderen vier Kataloge sind
unverändert.

Enthält außerdem die Arbeit der Fassungen davor:

- **1.4.2** – Die Benachrichtigungs-Tabelle liest sich jetzt als ein Satz. Die
  Spaltenüberschrift stellt die Frage („Benachrichtige mich, wenn …“), jede
  Zeile setzt sie fort („… jemand meinen Beitrag kommentiert“).
- **1.4.1** – FluentMessaging 2.9.0 und der Nachlauf zweier
  FluentPlayer-Updates, zusammen 120 Zeichenketten. Seitdem ist jeder der fünf
  Kataloge lückenlos. Neu unterschieden: `Block User` ist „Blockieren“ (die
  Sache zwischen zwei Mitgliedern), `Block from Chat` ist „Vom Chat
  ausschließen“ (die Moderationsmaßnahme).
- **1.4.0** – FluentCommunity 2.9.0 mit den Push-Benachrichtigungen: 56 neue
  Zeichenketten. Aus vierzehn E-Mail-Sätzen in der Ich-Form wurde eine Tabelle
  mit zwei Kanal-Spalten.
- **1.3.7 bis 1.3.9** – Fünf Korrekturen nach demselben Muster: eine
  Beschriftung, die mehreren Feldern dient, war für genau eines davon
  übersetzt und stand an allen anderen falsch. Betroffen waren `Short Bio`,
  `Type your message here!`, `Delete Course`, `Username` und
  `No space or special characters`.
- **1.3.1 bis 1.3.6** – FluentCommunity 2.8.0 und 2.8.1 mit dem bislang
  größten Durchgang: 154 neue Zeichenketten, fünf Themen, darunter das
  PWA-Modul, nicht gelistete Beiträge und die neu geschriebene Lizenzmaske.
- **1.3.0** – Ein Katalog wird nur noch geladen, wenn das zugehörige Plugin
  überhaupt installiert ist, und drei Textdomains bleiben auf Wunsch englisch.
- **1.0 bis 1.2, August 2026** – Der Umzug: Die Kataloge verlassen den
  Loco-Ordner und ziehen ins Plugin, geladen auf `plugins_loaded` mit
  Priorität 1. Damit hört auf, dass die Terminologie des Sprachpakets über
  Lückenfüller zurücksickert. Dazu der Filter, der den Zustimmungs-Link bei
  der Registrierung auf die richtige Seite legt.
