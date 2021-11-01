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
    ->addLegend('sac_event_evaluation_legend', 'protected_legend', PaletteManipulator::POSITION_BEFORE)
    ->addField('enableOnlineEvaluation', 'sac_event_evaluation_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_calendar')
;

// Palettes
$GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'][] = 'enableOnlineEvaluation';
$GLOBALS['TL_DCA']['tl_calendar']['subpalettes']['enableOnlineEvaluation'] = 'onlineEvaluationConfiguration,onlineEvaluationNotification,onlineEvaluationForm';


$GLOBALS['TL_DCA']['tl_calendar']['fields']['enableOnlineEvaluation'] = array
(
    'exclude'   => true,
    'search'    => true,
    'inputType' => 'checkbox',
    'eval'      => array('submitOnChange' => true, 'tl_class' => 'w50'),
    'sql'       => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_calendar']['fields']['onlineEvaluationConfiguration'] = array
(
    'exclude'   => true,
    'search'    => true,
    'inputType' => 'select',
    'eval'      => array('mandatory' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'),
    'sql'       => "varchar(64) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_calendar']['fields']['onlineEvaluationNotification'] = array
(
    'exclude'   => true,
    'search'    => true,
    'inputType' => 'select',
    'eval'      => array('mandatory' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'),
    'sql'       => "varchar(64) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_calendar']['fields']['onlineEvaluationForm'] = array
(
    'exclude'   => true,
    'search'    => true,
    'inputType' => 'select',
    'eval'      => array('mandatory' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'),
    'sql'       => "varchar(64) NOT NULL default ''"
);
