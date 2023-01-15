![Alt text](docs/logo.png?raw=true "logo")

# SAC Pilatus - Digitale Angebotsauswertung


## Ziel
Diese Erweiterung für das Contao CMS ermöglicht es Events online auf der Webseite auzuwerten.
 Dies erlaubt den LeiterInnen und der Sektion das Angebot fortlaufend zu verbessern
 und Touren und Kurse den Bedürfnissen der Teilnehmer anzupassen.


## Vorgehen
Teilnehmer kriegen nach einer Aktivität eine Aufforderung per E-Mail zum Ausfüllen einer Auswertung
  auf unserer Webseite.
Der Tourenleiter kann die gesammelten Auswertungen anonymisiert im Backend (bei der Teilnehmerliste lesen)


## Anforderungen an das Plugin

1. Pro Event-Container (tl_calendar) kann ein Formular für die Qualitätssicherung definiert werden.
2. Ist dies der Fall, kann jeder Tourenleiter pro Anlass definieren, ob eine Online-Auswertung durchgeführt wrden soll oder nicht.
3. Falls die Online-Auswertung aktiviert wurde:
   - Wenn die Teilnahme an einem Anlass bestätigt wurde (blauer Haken), kriegen die Teilnehmenden innerhalb weniger Minuten eine E-Mail mit einem Link zur Angebots-Auswertung
   - Wird die Auswertung durch den/die TeilnehmerIn in den ersten 2 Wochen nicht durchgeführt, sendet das System automatisch einen Reminder
   - Nach weiteren 2 Wochen wird ein weiterer Reminder versendet
   - Die Auswertung kann bis 2 Monate nach dem Anlass durch die Teilnehmenden ausgefüllt werden
4. TourenleiterInnen können auf der Tour die Auswertung summarisch herunterladen. Rückschlüsse auf die antwortende Person sind nicht möglich
   - Der/die TourenleiterIn sieht bei Fragen mit Zahlen wie viele TN, welches Feld angeklickt haben
   - Bei Textfragen werden alle Antworten untereinander aufgelistet
5. Die Auswertungsformularfragen müssen nicht archiviert werden und können ggf. nicht mehr den Antworten zugeordnet werden.
6. Antworten können nicht bearbeiten werden
7. Auswertungen werden nach 2 Jahren gelöscht

## Folgende Elemente sind nicht im Umsetzungsumfang (out of scope)
-	Statistisches Suchen in der Datenbank Z.B. Bester Kurs! Oder alle Kurse mit Note 2, etc.


# Installation
`composer require markocupic/sac-event-feedback`

**Abhängigkeiten:**
- contao/contao-core-bundle
- rbdwllr/reallysimplejwt
- markocupic/sac-event-tool-bundle
- markocupic/cloudconvert-bundle
- markocupic/sac-event-tool-bundle
- markocupic/cloudconvert-bundle
- terminal42/contao-mp_forms
- juststeveking/uri-builder

# Inbetriebnahme
- Erstellung eines Formulars im Contao Backend.
 In den Formulareinstellungen muss die Option (Checkbox) "SAC Event Auswertungsformular" aktiviert sein.
 Erstellung der Formularfelder. Möglich sind Select und Textarea-Felder.
- Erstellung eines Frontend-Moduls vom Typ "Event Feedback Formular"
- Erstellung einer Benachrichtigung "Aufforderung Online Tour-/Kurs-Auswertung"
- Erstellung einer Seite mit Artikel und darin das Modul "Event Feedback Formular" einbauen
- Danach pro Kalender-Container (tl_calendar) die Event-Auswertung konfigurieren.


# Funktion
Sobald der Leiter die Teilnahme eines Gastes bestätigt, wird ein Set von "Feedback Remindern" in der Datenbank abgelegt.
Wie viele Reminder und in welchem Abstand diese versendet werden sollen, kann via Konfiguration festgelegt werden.
Die Reminder werden dann minütlich (einstellbar) via Cronjob versendet.


# Konfiguration

Mit dem Bundle wird folgende Default-Konfiguration mitgeliefert:

```
markocupic_sac_event_feedback:
  secret: ''
  configs:
    # Default configuration
    default:
      name: 'default'
      # Allow feedbacks up to 10 days after the event end date
      feedback_expiration_time: 60 #days
      # Send reminders: 1 day after event end
      # Send reminders: 14 days after event end
      # Send reminders: 28 days after event end
      send_reminder_after_days: [1,14,28] #days
```

In config/config.yml muss ein secret abgelegt werden, welches aus mindestens
 12 Zeichen, Gross- und Kleinbuchstaben, Zahlen und einem Sonderzeichen bestehen muss.

```
markocupic_sac_event_feedback:
    # The secret should contain a number, an upper and a lowercase letter,
    # and a special character *&!@%^#$. It should be at least 12 characters in
    secret: '&fsdrefsR24ssfUTedsd%'
```

Die Konfiguration (config key) kann zudem angepasst werden
 oder es können weitere Konfigurationen erstellt werden,
 welche dann im Event Container ausgewählt werden können.
