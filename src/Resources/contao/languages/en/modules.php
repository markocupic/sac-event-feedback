<?php

declare(strict_types=1);

/*
 * This file is part of SAC Event Feedback.
 *
 * (c) Marko Cupic 2022 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/sac-event-feedback
 */

use Markocupic\SacEventFeedback\Controller\FrontendModule\EventFeedbackFormController;

/*
 * Backend modules
 */
$GLOBALS['TL_LANG']['MOD']['event_feedback'] = ['Event Feedback', 'Event Feedback'];
$GLOBALS['TL_LANG']['MOD']['event_feedback_reminder'] = ['Event Feedback Reminder', 'Event Feedback Reminder'];

/*
 * Frontend modules
 */
$GLOBALS['TL_LANG']['FMD']['event_feedback'] = 'Event Feedback';
$GLOBALS['TL_LANG']['FMD'][EventFeedbackFormController::TYPE] = ['Event Feedback Formular', 'Event Feedback Formular'];
