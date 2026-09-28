<?php

declare(strict_types=1);

/*
 * This file is part of SAC Event Feedback.
 *
 * (c) Marko Cupic <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/sac-event-feedback
 */

use Markocupic\SacEventFeedback\NotificationType\EventFeedbackReminderNotificationType;

$type = EventFeedbackReminderNotificationType::NAME;

// Event
$GLOBALS['TL_LANG']['nc_tokens'][$type]['event_title'] = 'Titel des Events.';
$GLOBALS['TL_LANG']['nc_tokens'][$type]['feedback_url'] = 'Absoluter Link zum Online-Feedbackformular, inkl. persönlichem Zugangstoken des Teilnehmers.';

// Instructor
$GLOBALS['TL_LANG']['nc_tokens'][$type]['instructor_name'] = 'Name des Hauptleiters.';
$GLOBALS['TL_LANG']['nc_tokens'][$type]['instructor_email'] = 'E-Mail-Adresse des Hauptleiters. Leer, wenn kein Hauptleiter hinterlegt ist.';

// Participant
$GLOBALS['TL_LANG']['nc_tokens'][$type]['participant_firstname'] = 'Vorname des Teilnehmers.';
$GLOBALS['TL_LANG']['nc_tokens'][$type]['participant_lastname'] = 'Nachname des Teilnehmers.';
$GLOBALS['TL_LANG']['nc_tokens'][$type]['participant_email'] = 'E-Mail-Adresse des Teilnehmers.';
$GLOBALS['TL_LANG']['nc_tokens'][$type]['participant_uuid'] = 'UUID der Event-Anmeldung.';

// Admin
$GLOBALS['TL_LANG']['nc_tokens'][$type]['admin_email'] = 'E-Mail-Adresse des Systemadministrators (Contao-Einstellung "E-Mail-Adresse des Systemadministrators").';
