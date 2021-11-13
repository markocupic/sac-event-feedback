<?php

/*
 * This file is part of SAC Event Evaluation Bundle.
 *
 * (c) Marko Cupic 2021 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/sac-event-evaluatio
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;

PaletteManipulator::create()
	->addLegend('sac_event_evaluation_legend', 'title_legend', PaletteManipulator::POSITION_AFTER)
	->addField('isSacEventEvaluationForm', 'sac_event_evaluation_legend', PaletteManipulator::POSITION_APPEND)
	->applyToPalette('default', 'tl_form')
;

$GLOBALS['TL_DCA']['tl_form']['fields']['isSacEventEvaluationForm'] = array
(
	'exclude'                 => true,
	'search'                  => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50'),
	'sql'                     => "char(1) NOT NULL default ''"
);
