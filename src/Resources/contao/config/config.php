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

use Markocupic\SacEventFeedback\Model\EventFeedbackModel;

/**
 * Backend modules
 */
$GLOBALS['BE_MOD']['event_feedback']['event_feedback'] = array(
	'tables' => array('tl_event_feedback')
);

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_event_feedback'] = EventFeedbackModel::class;
