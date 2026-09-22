# PC’L Übersetzungen für Fluent-Plugins

Eine vollständige deutsche Übersetzung für **FluentCommunity**,
**FluentCommunity Pro**, **FluentMessaging**, **FluentPlayer**,
**FluentPlayer Pro**, **FluentAuth**, **FluentSMTP** und **FluentSnippets** –
ausgeliefert als **ein** WordPress-Plugin, das seine Kataloge vor allen anderen
lädt. Jede der acht Übersetzungen lässt sich einzeln abschalten.

> Unabhängiges Projekt. Keine Verbindung zu WPManageNinja, den Herstellern der
> Fluent-Plugins.

> [!IMPORTANT]
> **Nur das ZIP aus den [Releases](https://github.com/blocoder/pcl-fluent-de/releases/latest) installieren.**
> Das Quellcode-Archiv der Repo-Startseite („Code → Download ZIP“) enthält
> allein die `.po`-Dateien. Die daraus gebauten Kataloge (`.mo`, `.l10n.php`)
> stecken im Release-Archiv – ohne sie übersetzt das Plugin nichts, und in der
> Plugin-Liste steht „Keine Kataloge gefunden“.

---

## Warum eine eigene deutsche Übersetzung?

Ich betreibe seit 2025 eine Community auf FluentCommunity. Die deutsche
Übersetzung von wordpress.org war da, aber sie gefiel mir nicht.

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

Die Lösung: **Wer zuerst lädt, gewinnt.** Dieses Plugin lädt seine Kataloge
auf `plugins_loaded` mit Priorität 1 – bevor FluentCommunity die erste
Übersetzung anfordert und bevor das Sprachpaket zum Zug kommt. Die eigene
Fassung steht damit vorn, das Sprachpaket füllt nur noch Lücken, und die
Kataloge liegen im Plugin und wandern mit ihm.

Aus einer Handvoll geänderter Zeichenketten sind 8.660 geworden, und aus einem
Plugin für FluentCommunity eines für acht.

---

## Was drin ist

Stand: 22.09.2026, abgeglichen mit diesen Plugin-Versionen.

| Katalog | Plugin | Version | übersetzt | offen |
|---|---|---|---:|---:|
| `fluent-community` | FluentCommunity | 2.11.0 | 3.249 | – |
| `fluent-community-pro` | FluentCommunity Pro | 2.11.0 | 470 | – |
| `fluent-messaging` | FluentMessaging | 2.9.0 | 366 | 3 |
| `fluent-player` | FluentPlayer | 1.4.0 | 763 | 2 |
| `fluent-player-pro` | FluentPlayer Pro | 1.4.0 | 590 | 3 |
| `fluent-security` | FluentAuth | 3.0.3 | 1.938 | 34 |
| `fluent-smtp` | FluentSMTP | 2.4.0 | 802 | 25 |
| `easy-code-manager` | FluentSnippets | 10.56 | 482 | 3 |

**8.660 Zeichenketten. 70 sind offen:** Eigennamen (`Facebook`, `Mailgun`,
`Amazon SES`) und Plugin-Kopfzeilen – Produktname, Adresse, Autorenname –, die
man nicht übersetzt. Jede trägt einen Übersetzerkommentar, warum sie leer
steht.

**FluentSMTP und FluentSnippets gibt es zusätzlich in der Sie-Form**, mit
denselben Zahlen. Für die übrigen sechs Kataloge existiert nur die Du-Fassung.
Steht die Seite auf „Deutsch (Sie)“, laden die sechs seit 2.4.0 in der
Du-Form, statt englisch zu bleiben.

Ein Katalog gehört zu einer Plugin-Version: Ändert der Hersteller den
englischen Text, ist das für gettext ein neuer Schlüssel, und der alte fällt
aus dem Katalog. Auf einer älteren Plugin-Version können einzelne
Zeichenketten deshalb englisch erscheinen. Am besten erst die Fluent-Plugins
aktualisieren, dann dieses hier.

---

## Wie übersetzt wurde

**Meine Übersetzung überschreibt die offizielle Version.** Das ist Absicht.
Das Sprachpaket von wordpress.org bleibt installiert und aktiv – es deckt rund
950 Zeichenketten ab, die sonst englisch blieben. Es kommt nur noch dort zum
Zug, wo dieses Plugin nichts hat.

**Du-Form**, durchgehend, mit großem „Du“. Für FluentSMTP und FluentSnippets
gibt es daneben eine Sie-Fassung, die sich je Plugin erzwingen lässt.

**`Space` heißt Forum**, `Spaces` heißt Foren, `Space Group` heißt
Foren-Gruppe.

**Ein eigenes Glossar sorgt für Konsistenz.** `Member` ist immer Mitglied, nie
Nutzer. `Student` ist Teilnehmer oder Teilnehmende, Plural bevorzugt.
`Reports` als Substantiv sind Berichte, als Verb bleibt es „melden“.
Fachbegriffe wie `Layer`, `Preset` und `Playlist` bleiben stehen – eine
Eindeutschung verwirrt dort mehr, als sie hilft.

Ein Sonderfall, der zeigt, wie fein das wird: **`User` heißt Mitglied oder
Benutzer, je nach Ebene.** Geht es um ein WordPress-Konto – Rollen, Login,
Benutzerverwaltung –, heißt es Benutzer, wie im WordPress-Kern. Geht es um die
Person in der Community, heißt es Mitglied.

**Jede Zeichenkette ist von Hand nachgearbeitet.** Kein maschineller
Durchlauf. Wo das Original unklar ist, steht auf Deutsch, was gemeint ist –
und wenn eine Zeile im Original aus Bruchstücken zusammengesetzt ist, die sich
nicht sauber übersetzen lassen, ist das im Katalog kommentiert und beim
Hersteller gemeldet.

Dazu die Hausregeln: typografische Anführungszeichen `„…“`, ein echtes
Auslassungszeichen `…` statt drei Punkten, generische Formen statt
Genderschreibweisen.

---

## Die Einstellungsseite

Unter *Einstellungen → PC’L Übersetzungen* steht eine Zeile je Übersetzung.
Jede kennt **drei Zustände**:

| Zustand | Was passiert |
|---|---|
| **Deutsche Übersetzung aktiv** | Unser Katalog gilt. Vorgabe, sobald das Plugin installiert ist. |
| **Fremde Übersetzung (WordPress, Plugin)** | Unser Katalog bleibt weg, was WordPress sonst findet, greift weiter. |
| **Keine Übersetzung zulassen** | Das Plugin bleibt englisch, auch gegenüber dem Sprachpaket. |

Die mittlere und die rechte Spalte sind nicht dasselbe: Bei „Fremde
Übersetzung“ springt das Sprachpaket von wordpress.org ein, bei „Keine
Übersetzung zulassen“ niemand.

Ist ein Plugin nicht installiert, steht seine Zeile grau da – die Einstellung
bleibt erhalten und greift wieder, sobald das Plugin da ist.

**Anrede**, wo beide Fassungen vorliegen (FluentSMTP, FluentSnippets): „wie die
Seite“, „immer Du“ oder „immer Sie“. Eine Seite in der Du-Form kann damit für
ein einzelnes Plugin siezen. Fehlt die gewünschte Fassung, gilt wieder die
Anrede der Seite, statt dass der Text englisch wird.

---

## Was das Plugin außerdem tut

**Es setzt den Zustimmungs-Link bei der Registrierung richtig.**
FluentCommunity beschriftet die Checkbox mit „Nutzungsbedingungen“, verlinkt
aber die Datenschutzseite aus *Einstellungen → Datenschutz*. Das ist kein
Übersetzungsfehler und durch keine Formulierung zu heilen – der Link zeigt
woandershin. Unter *Einstellungen → PC’L Übersetzungen* lässt sich die
richtige Adresse eintragen; wer es festnageln will, setzt
`PCL_FLUENT_TERMS_URL` in der `wp-config.php`.

**Und was ein Katalog nicht kann, bringt es mit:** deutsche Datumsangaben in
der FluentSMTP-Verwaltung, relative Zeitangaben in FluentSnippets und die
Fußzeile der Anmeldecode-Mails von FluentAuth. Geladen wird das nur, wenn das
zugehörige Plugin da und die Übersetzung eingeschaltet ist.

Welche Domains das Plugin überhaupt kennt, steht in einer Registry und lässt
sich über einen Filter anpassen:

```php
add_filter( 'pcl_fluent_de/registry', function ( $registry ) { … } );
```

> [!NOTE]
> Der frühere Filter `pcl_fluent_de/domains` ist mit 2.0.0 entfallen.

## Fremde Übersetzungen englisch halten

Bis 1.5.3 hielt das Plugin drei fremde Textdomains auf Englisch
(`fluent-crm`, `fluentcampaign-pro`, `easy-code-manager`). **Seit 1.6.0 tut es
das nicht mehr** – es kümmert sich nur um seine eigenen Übersetzungen. Der
Filter `pcl_fluent_de/blocked_domains` entfällt.

Für die acht eigenen Domains gibt es dafür den Zustand **„Keine Übersetzung
zulassen“** auf der Einstellungsseite. Wer ein *fremdes* Plugin englisch haben
will, legt das selbst fest, etwa als Snippet:

```php
add_filter( 'override_load_textdomain', function ( $override, $domain ) {
	// Priorität 1: Ein späterer Callback (Loco Translate) lädt sonst schon selbst.
	return in_array( $domain, array( 'fluent-crm', 'fluentcampaign-pro' ), true ) ? true : $override;
}, 1, 2 );
```

Läuft das Snippet erst nach `plugins_loaded` an (FluentSnippets: Priorität 9),
kann eine Domain bis dahin schon geladen sein; dann einmal
`unload_textdomain( $domain )` hinterher.

---

## Installation

1. Das ZIP aus [Releases](https://github.com/blocoder/pcl-fluent-de/releases)
   herunterladen.
2. Im Backend unter *Plugins → Installieren → Plugin hochladen* einspielen und
   aktivieren.

Das ZIP steht unter *Releases* am rechten Rand der Repo-Startseite. Der grüne
Knopf *Code → Download ZIP* daneben liefert den Quellcode ohne die gebauten
Kataloge und damit ein Plugin, das nichts übersetzt.

Die Seite muss auf Deutsch stehen. `de_DE` ist der Normalfall; auf
`de_DE_formal`, `de_AT` oder `de_CH` greifen die Kataloge ebenfalls – dort, wo
es keine eigene Fassung für die Sprachvariante gibt, in der Du-Form. In der
Plugin-Liste steht danach, welche Kataloge tatsächlich greifen und unter
welcher Sprache – das erspart die Suche, wenn eine Datei fehlt oder die
Sprache nicht passt.

Ein bereits aktives `pcl-fluentauth-de`, `pcl-fluentsmtp-de` oder
`pcl-fluentsnippets-de` braucht niemand vorher abzuschalten: Solange eines
davon läuft, hält sich dieses Plugin für dessen Übersetzung heraus. Danach
können die drei deaktiviert und gelöscht werden.

Es ist nicht nötig, das Sprachpaket von wordpress.org zu deinstallieren. Loco
Translate darf ebenfalls stehen bleiben – eine dort gepflegte Fassung behält
ihren Vorrang.

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
