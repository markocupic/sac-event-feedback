![Alt text](docs/logo.png?raw=true "logo")

# SAC Pilatus - Digitale Angebotsauswertung
## Ziel
Angebote werden digital auf der Webseite ausgewertet.
  Dies erlaubt den LeiterInnen und der Sektion das Angebot fortlaufend zu verbessern
  und neue Wünsche aufzunehmen.

## Vorgehen
Teilnehmer kriegen nach einer Aktivität eine Aufforderung zum Ausfüllen einer Auswertung 
  auf unserer Webseite.
Der Tourenleiter kann die gesammelten Auswertungen anonymisiert im Backend (bei der Teilnehmerliste betrachten)

## Anforderungen

1. Pro Event-Container (tl_calendar) kann ein Formular für die Qualitätssicherung definiert werden.
2. Pro Anlass kann definiert werden, ob eine online Auswertung stattfinden soll oder nicht. Wenn ein Formular definiert ist, wird standardmässig online Auswertung gesetzt und kann danach abgewählt werden.
3. Falls online Auswertung
   - Wenn die Teilnahme an einem Anlass bestätigt wurde, kriegen die Teilnehmenden innerhalb 24 h eine E-Mail mit einem Link zur Angebots-Auswertung
   - Wird die Auswertung in den ersten 2 Wochen nicht beantwortet, sendet das System automatisch eine Erinnerung
   - Nach weiteren 2 Wochen wird ein weiterer Reminder versendet
   - Die Auswertung kann bis 2 Monate nach dem Anlass durch die Teilnehmenden ausgefüllt werden
4. TourenleiterInnen können auf der Tour die Auswertung summarisch herunterladen. Rückschlüsse auf die antwortende Person sind nicht möglich
   - Der/die TourenleiterIn sieht bei Fragen mit Zahlen wie viele TN, welches Feld angeklickt haben
   - Bei Textfragen werden alle Antworten untereinander dargestellt
5. Die Auswertungsformularfragen müssen nicht archiviert werden und können ggf. nicht mehr den Antworten zugeordnet werden.
6. Antworten können nicht bearbeiten werden
7. Auswertungen werden nach 2 Jahren gelöscht
      
## Folgende Elemente sind nicht im Umsetzungsumfang (out of scope)
-	Statistisches Suchen in der Datenbank Z.B. Bester Kurs! Oder alle Kurse mit Note 2, etc.
