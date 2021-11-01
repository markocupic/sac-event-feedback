<?php


use Contao\CoreBundle\DataContainer\PaletteManipulator;

PaletteManipulator::create()
    ->addLegend('sac_event_evaluation_legend', 'title_legend', PaletteManipulator::POSITION_AFTER)
    ->addField('isSacEventEvaluationForm', 'sac_event_evaluation_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default','tl_form')
;

$GLOBALS['TL_DCA']['tl_form']['fields']['isSacEventEvaluationForm'] = array
(
    'exclude'                 => true,
    'search'                  => true,
    'inputType'               => 'checkbox',
    'eval'                    => array('tl_class'=>'w50'),
    'sql'                     => "char(1) NOT NULL default ''"
);
