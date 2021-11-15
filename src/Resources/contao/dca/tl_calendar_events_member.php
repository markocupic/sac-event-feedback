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

use Contao\CoreBundle\DataContainer\PaletteManipulator;

/**
 * @todo Remove this field from the default palette from the day we reach our first stable release.
 */
PaletteManipulator::create()
	->addLegend(
		'onlineFeedback_legend',
		'notes_legend',
		PaletteManipulator::POSITION_BEFORE
	)
	->addField(
		'countOnlineEventFeedbackNotifications',
		'onlineFeedback_legend',
        PaletteManipulator::POSITION_APPEND
	)
	->applyToPalette(
		'default',
		'tl_calendar_events_member'
	)
;

// Table config
$GLOBALS['TL_DCA']['tl_calendar_events_member']['ctable'][] = 'tl_event_reminder';

// Fields
$GLOBALS['TL_DCA']['tl_calendar_events_member']['fields']['countOnlineEventFeedbackNotifications'] = array
(
	'exclude'   => true,
	'search'    => true,
	'inputType' => 'text',
	'eval'      => array('rgxp' => 'natural', 'tl_class' => 'w50'),
	'sql'       => "int(3) unsigned NOT NULL default 0",
);
