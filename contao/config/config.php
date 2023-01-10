<?php

declare(strict_types=1);

/*
 * This file is part of SAC Event Feedback.
 *
 * (c) Marko Cupic 2023 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/sac-event-feedback
 */

use Markocupic\SacEventFeedback\Contao\Controller\EventFeedbackController;
use Markocupic\SacEventFeedback\Model\EventFeedbackModel;
use Markocupic\SacEventFeedback\Model\EventFeedbackReminderModel;

/*
 * Backend modules
 */
$GLOBALS['BE_MOD']['event_feedback'] = [
    'event_feedback'          => [
        'tables' => ['tl_event_feedback'],
    ],
    'event_feedback_reminder' => [
        'tables' => ['tl_event_feedback_reminder'],
    ],
];

$GLOBALS['BE_MOD']['sac_be_modules']['sac_calendar_events_tool']['showEventFeedbacks'] = [EventFeedbackController::class, 'getEventFeedbackAction'];
$GLOBALS['BE_MOD']['sac_be_modules']['sac_calendar_events_tool']['showEventFeedbacksAsPdf'] = [EventFeedbackController::class, 'getEventFeedbackAsPdfAction'];

/*
 * Models
 */
$GLOBALS['TL_MODELS']['tl_event_feedback'] = EventFeedbackModel::class;
$GLOBALS['TL_MODELS']['tl_event_feedback_reminder'] = EventFeedbackReminderModel::class;

/*
 * Notification center
 */
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['sac_event_tool']['event_feedback_reminder'] = [
    // Field in tl_nc_language
    'email_sender_name'    => ['instructor_name', 'instructor_email', 'admin_email'],
    'email_sender_address' => ['instructor_email', 'instructor_email', 'admin_email'],
    'recipients'           => ['participant_email', 'instructor_email', 'admin_email'],
    'email_replyTo'        => ['instructor_email'],
    'email_subject'        => ['event_name'],
    'email_text'           => ['event_name', 'instructor_name', 'participant_uuid', 'participant_firstname', 'participant_lastname', 'participant_email', 'feedback_url'],
    'email_html'           => ['event_name', 'instructor_name', 'participant_uuid', 'participant_firstname', 'participant_lastname', 'participant_email', 'feedback_url'],
];
