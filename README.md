# PC’L Übersetzungen für Fluent-Plugins

Eine vollständige deutsche Übersetzung für **FluentCommunity**,
**FluentCommunity Pro**, **FluentMessaging**, **FluentPlayer** und
**FluentPlayer Pro** – ausgeliefert als WordPress-Plugin, das seine Kataloge
vor allen anderen lädt.

> Unabhängiges Projekt. Keine Verbindung zu WPManageNinja, den Herstellern der
> Fluent-Plugins.

---

## Warum es das gibt

Ich betreibe seit 2025 eine Community auf FluentCommunity. Die deutsche
Übersetzung von wordpress.org war da, aber sie stand mir ständig im Weg.

Ein *Space* hieß bei ihr **„Raum“**. Das ist nicht falsch übersetzt, es ist nur
falsch für das, was meine Mitglieder vor sich haben: Sie diskutieren in Foren,
nicht in Räumen. Also habe ich angefangen, einzelne Zeichenketten in Loco
Translate zu ändern – und bin zweimal in dieselbe Falle gelaufen.

**Erstens sickerte die fremde Terminologie zurück.** WordPress fragt für eine
Textdomain mehrere Kataloge der Reihe nach ab. Was ich nicht selbst übersetzt
hatte, holte es aus dem Sprachpaket – und damit standen „Forum“ und „Raum“
nebeneinander auf derselben Seite. Nach jedem Update ein Stück mehr.

**Zweitens waren meine Änderungen nicht mitzunehmen.** Loco legt seine Dateien
in `wp-content/languages/loco/` ab. Auf einer zweiten Installation fängt man
von vorn an.

Beides löst sich an derselben Stelle: **Wer zuerst lädt, gewinnt.** Dieses
Plugin lädt seine Kataloge auf `plugins_loaded` mit Priorität 1 – bevor
FluentCommunity die erste Übersetzung anfordert und bevor das Sprachpaket zum
Zug kommt. Die eigene Fassung steht damit vorn, das Sprachpaket füllt nur noch
Lücken, und die Kataloge liegen im Plugin und wandern mit ihm.

Aus einer Handvoll geänderter Zeichenketten sind über 5.000 geworden.

---

## Was drin ist

| Katalog | übersetzt |
|---|---:|
| `fluent-community` | 3.065 |
| `fluent-community-pro` | 409 |
| `fluent-messaging` | 366 |
| `fluent-player` | 763 |
| `fluent-player-pro` | 590 |

**5.193 Zeichenketten. Acht sind offen** – und die bleiben es: Es sind
Plugin-Kopfzeilen (Produktname, Adresse, Autorenname), die man nicht
übersetzt. Jede trägt einen Übersetzerkommentar, warum sie leer steht.

Nur `de_DE`. Eine Sie-Fassung gibt es nicht.

---

## Wie übersetzt wurde

**Sie überschreibt die offizielle Übersetzung.** Das ist der Zweck, nicht ein
Nebeneffekt. Das Sprachpaket von wordpress.org bleibt installiert und aktiv –
es deckt rund 950 Zeichenketten ab, die sonst englisch blieben. Es kommt nur
noch dort zum Zug, wo dieses Plugin nichts hat.

**Du-Form**, durchgehend, mit großem „Du“. Eine Community duzt sich.

**`Space` heißt Forum**, `Spaces` heißt Foren, `Space Group` heißt
Foren-Gruppe. Das ist die Entscheidung, an der alles angefangen hat.

**Ein eigenes Glossar sorgt für Konsistenz.** `Member` ist immer Mitglied, nie
Nutzer. `Student` ist Teilnehmer oder Teilnehmende, Plural bevorzugt.
`Reports` als Substantiv sind Berichte, als Verb bleibt es „melden“.
Fachbegriffe wie `Layer`, `Preset` und `Playlist` bleiben stehen – eine
Eindeutschung verwirrt dort mehr, als sie hilft.

Ein Sonderfall, der zeigt, wie fein das wird: **`User` heißt Mitglied oder
Benutzer, je nach Ebene.** Geht es um ein WordPress-Konto – Rollen, Login,
Benutzerverwaltung –, heißt es Benutzer, wie im WordPress-Kern. Geht es um die
Person in der Community, heißt es Mitglied. Das kann kein Suchen-und-Ersetzen
entscheiden, das geht nur Zeichenkette für Zeichenkette.

**Jede Zeichenkette ist von Hand nachgearbeitet.** Kein maschineller
Durchlauf. Wo das Original unklar ist, steht auf Deutsch, was gemeint ist –
und wenn eine Zeile im Original aus Bruchstücken zusammengesetzt ist, die sich
nicht sauber übersetzen lassen, ist das im Katalog kommentiert und beim
Hersteller gemeldet.

Dazu die Hausregeln: typografische Anführungszeichen `„…“`, ein echtes
Auslassungszeichen `…` statt drei Punkten, generische Formen statt
Genderschreibweisen.

---

## Was das Plugin außerdem tut

**Es setzt den Zustimmungs-Link bei der Registrierung richtig.**
FluentCommunity beschriftet die Checkbox mit „Nutzungsbedingungen“, verlinkt
aber die Datenschutzseite aus *Einstellungen → Datenschutz*. Das ist kein
Übersetzungsfehler und durch keine Formulierung zu heilen – der Link zeigt
woandershin. Unter *Einstellungen → PC’L Übersetzungen* lässt sich die
richtige Adresse eintragen; wer es festnageln will, setzt
`PCL_FLUENT_TERMS_URL` in der `wp-config.php`.

**Es hält drei Textdomains auf Englisch.** Für `fluent-crm`,
`fluentcampaign-pro` und `easy-code-manager` ist die deutsche Fassung nach
meinem Urteil schlechter als das Original. Statt Dateien wegzuräumen, die ein
Update wiederbringt, wird das Laden unterbunden.

Beides lässt sich über Filter anpassen:

```php
add_filter( 'pcl_fluent_de/domains', function ( $domains ) { … } );
add_filter( 'pcl_fluent_de/blocked_domains', function ( $domains ) { … } );
```

---

## Installation

1. Das ZIP aus [Releases](https://github.com/blocoder/pcl-fluent-de/releases)
   herunterladen.
2. Im Backend unter *Plugins → Installieren → Plugin hochladen* einspielen und
   aktivieren.

Die Seite muss auf `de_DE` stehen. In der Plugin-Liste steht danach, welche
Kataloge tatsächlich greifen – das erspart die Suche, wenn eine Datei fehlt
oder die Sprache nicht passt.

Es ist nicht nötig, das Sprachpaket von wordpress.org zu deinstallieren. Im
Gegenteil: Es soll bleiben.

**Voraussetzungen:** WordPress 6.5+, PHP 7.4+.

---

## Mitmachen

Ein Wort, das nicht passt? Eine Zeichenkette, die im Zusammenhang falsch
klingt? [Ein Issue](https://github.com/blocoder/pcl-fluent-de/issues) mit dem
englischen Original und der Stelle, an der es auftaucht, hilft am meisten.

Die `.po`-Dateien liegen in `languages/` und lassen sich direkt bearbeiten –
auch mit Loco Translate im Backend, dafür ist die `loco.xml` da. Die
kompilierten `.mo`- und `.l10n.php`-Dateien stehen nur im Release-Archiv,
nicht im Repo: Sie sind Erzeugnisse.

---

## Lizenz

`GPL-2.0-or-later`, siehe [LICENSE](LICENSE).

Die Kataloge enthalten die Quellzeichenketten der übersetzten Plugins und sind
damit abgeleitete Werke GPL-lizenzierter Software. Nutzung, Änderung und
Weitergabe sind erlaubt, kommerziell eingeschlossen.

Namensnennung freut mich, ist aber keine Bedingung.
