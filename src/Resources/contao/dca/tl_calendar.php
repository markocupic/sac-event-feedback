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

PaletteManipulator::create()
	->addLegend('sac_event_feedback_legend', 'protected_legend', PaletteManipulator::POSITION_BEFORE)
	->addField('enableOnlineEventFeedback', 'sac_event_feedback_legend', PaletteManipulator::POSITION_APPEND)
	->applyToPalette('default', 'tl_calendar')
;

// Palettes
$GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'][] = 'enableOnlineEventFeedback';
$GLOBALS['TL_DCA']['tl_calendar']['subpalettes']['enableOnlineEventFeedback'] = 'onlineFeedbackConfiguration,onlineFeedbackNotification,onlineFeedbackForm';

$GLOBALS['TL_DCA']['tl_calendar']['fields']['enableOnlineEventFeedback'] = array
(
	'exclude'   => true,
	'filter'    => true,
	'inputType' => 'checkbox',
	'eval'      => array('submitOnChange' => true, 'tl_class' => 'w50'),
	'sql'       => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_calendar']['fields']['onlineFeedbackConfiguration'] = array
(
	'exclude'   => true,
	'search'    => true,
	'inputType' => 'select',
	'eval'      => array('mandatory' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'),
	'sql'       => "varchar(64) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_calendar']['fields']['onlineFeedbackNotification'] = array
(
	'exclude'   => true,
	'search'    => true,
	'inputType' => 'select',
	'eval'      => array('mandatory' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'),
	'sql'       => "varchar(64) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_calendar']['fields']['onlineFeedbackForm'] = array
(
	'exclude'   => true,
	'search'    => true,
	'inputType' => 'select',
	'eval'      => array('mandatory' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'),
	'sql'       => "varchar(64) NOT NULL default ''"
);
