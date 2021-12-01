<?php

/*
 * This file is part of SAC Event Feedback Bundle.
 *
 * (c) Marko Cupic 2021 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/sac-event-feedback
 */

use Markocupic\SacEventFeedback\Contao\Controller\EventFeedbackController;
use Markocupic\SacEventFeedback\Model\EventFeedbackModel;
use Markocupic\SacEventFeedback\Model\EventFeedbackReminderModel;

/**
 * Backend modules
 */
$GLOBALS['BE_MOD']['event_feedback'] = array(
	'event_feedback' => array(
		'tables' => array('tl_event_feedback')
	),
	'event_feedback_reminder' => array(
		'tables' => array('tl_event_feedback_reminder')
	),
);

$GLOBALS['BE_MOD']['sac_be_modules']['sac_calendar_events_tool']['showEventFeedbacks'] = array(EventFeedbackController::class, 'getEventFeedbackAction');
$GLOBALS['BE_MOD']['sac_be_modules']['sac_calendar_events_tool']['showEventFeedbacksAsPdf'] = array(EventFeedbackController::class, 'getEventFeedbackAsPdfAction');

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_event_feedback'] = EventFeedbackModel::class;
$GLOBALS['TL_MODELS']['tl_event_feedback_reminder'] = EventFeedbackReminderModel::class;

/**
 * Notification center
 */
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['sac_event_tool']['event_feedback_reminder'] = array(
	// Field in tl_nc_language
	'email_sender_name'    => array('instructor_name', 'instructor_email', 'admin_email'),
	'email_sender_address' => array('instructor_email', 'instructor_email', 'admin_email'),
	'recipients'           => array('participant_email', 'instructor_email', 'admin_email'),
	'email_replyTo'        => array('instructor_email'),
	'email_subject'        => array('event_name'),
	'email_text'           => array('event_name', 'instructor_name', 'participant_uuid', 'participant_firstname', 'participant_lastname', 'participant_email', 'feedback_url'),
	'email_html'           => array('event_name', 'instructor_name', 'participant_uuid', 'participant_firstname', 'participant_lastname', 'participant_email', 'feedback_url'),
);

if(TL_MODE === 'BE'){
    $GLOBALS['TL_CSS'][] = 'bundles/markocupicsaceventfeedback/css/styles.css';
}
