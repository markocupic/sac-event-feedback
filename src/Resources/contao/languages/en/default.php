<?php

declare(strict_types=1);

/*
 * This file is part of SAC Event Feedback Bundle.
 * 
 * (c) Marko Cupic 2021 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/sac-event-feedback
 */

// Errors
$GLOBALS['TL_LANG']['ERR']['sacEvFb']['invalidEventFeedbackConfiguration'] = 'Invalid configuration detected. Please check for a valid form, page, notification and configuration in the events parent calendar.';
$GLOBALS['TL_LANG']['ERR']['sacEvFb']['invalidUuidForLoggedInUser'] = 'Die mit der url übermittelte UUID konnte nicht deinem Benutzerkonto zugeordnet werden. Bitte nimm mit dem Administrator Kontakt auf, sollte der Fehler länger bestehen.';
$GLOBALS['TL_LANG']['ERR']['sacEvFb']['eventMatchingUuidNotFound'] = 'Die mit der url übermittelte UUID konnte keinem Event zugeordnet werden. Bitte nimm mit dem Administrator Kontakt auf, sollte der Fehler länger bestehen.';
$GLOBALS['TL_LANG']['ERR']['sacEvFb']['calendarMatchingUuidNotFound'] = 'Die mit der url übermittelte UUID konnte keinem Kalender zugeordnet werden. Bitte nimm mit dem Administrator Kontakt auf, sollte der Fehler länger bestehen.';
$GLOBALS['TL_LANG']['ERR']['sacEvFb']['formMatchingUuidNotFound'] = 'Die mit der url übermittelte UUID konnte keinem Auswertungsformular zugeordnet werden. Bitte nimm mit dem Administrator Kontakt auf, sollte der Fehler länger bestehen.';
$GLOBALS['TL_LANG']['ERR']['sacEvFb']['formAllreadyFilledOut'] = 'Zu diesem Event wurde das Auswertungsformular von dir bereits einmal ausgefüllt. Bitte nimm mit dem Administrator Kontakt auf, sollte der Fehler länger bestehen.';
$GLOBALS['TL_LANG']['ERR']['sacEvFb']['formMatchingUuidNotFound'] = 'Die mit der url übermittelte UUID konnte keinem Auswertungsformular zugeordnet werden. Bitte nimm mit dem Administrator Kontakt auf, sollte der Fehler länger bestehen.';
$GLOBALS['TL_LANG']['ERR']['sacEvFb']['formMatchingUuidNotFound'] = 'Die mit der url übermittelte UUID konnte keinem Auswertungsformular zugeordnet werden. Bitte nimm mit dem Administrator Kontakt auf, sollte der Fehler länger bestehen.';
$GLOBALS['TL_LANG']['ERR']['sacEvFb']['tokenExpired'] = 'Das Sicherheitstoken ist abgelaufen. Für diesen Event kann kein Feedback mehr abgegeben werden.';
$GLOBALS['TL_LANG']['ERR']['sacEvFb']['invalidToken'] = 'Ungültiges Sicherheitstoken.';

// Miscelaneous
$GLOBALS['TL_LANG']['MSC']['sacEvFb']['salutationMale'] = 'Lieber';
$GLOBALS['TL_LANG']['MSC']['sacEvFb']['salutationFemale'] = 'Liebe';
$GLOBALS['TL_LANG']['MSC']['sacEvFb']['checkoutMsg'] = '%s %s{{br}}Besten Dank für dein Feedback. Deine Rückmeldung hilft uns, unsere Angebote stetig zu verbessern und diese den Bedürfnissen der Teilnehmer anzupassen.{{br}}{{br}}Weiterhin unvergessliche und unfallfreie Touren und hoffentlich bis bald{{br}}{{br}}SAC Touren- und Kursadministration';