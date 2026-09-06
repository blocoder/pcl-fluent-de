# Änderungen

Die Versionsnummer steigt bei **jeder** Katalogänderung, auch wenn sich am
Plugin-Code nichts tut. Ein Archiv, dessen Name nichts über seinen Inhalt
sagt, ist beim Weitergeben wertlos.

Frühere Fassungen liefen nicht öffentlich; diese Liste beginnt mit dem ersten
veröffentlichten Stand.

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
