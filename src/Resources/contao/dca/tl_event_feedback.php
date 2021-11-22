<?php

declare(strict_types=1);

/*
 * This file is part of SAC Event Feedback Bundle.
 *
 * (c) Marko Cupic 2021 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/sac-event-feedback
 */

use Contao\FormModel;

/*
 * This file is part of SAC Event Feedback Bundle.
 *
 * (c) Marko Cupic 2021 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/sac-event-feedback
 */

/**
 * Table tl_event_feedback
 */
$GLOBALS['TL_DCA']['tl_event_feedback'] = array(
	// Config
	'config'   => array(
		'dataContainer'    => 'Table',
		'enableVersioning' => true,
		'sql'              => array(
			'keys' => array(
				'id' => 'primary',
				'uuid' => 'index',
				'uuid' => 'unique',
			)
		),
	),
	'list'     => array(
		'sorting'           => array(
			'mode'        => 2,
			'fields'      => array('dateAdded'),
			'flag'        => 1,
			'panelLayout' => 'filter;sort,search,limit'
		),
		'label'             => array(
			'fields' => array('uuid', 'pid'),
			'format' => '%s, %s',
		),
		'global_operations' => array(
			'all' => array(
				'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'       => 'act=select',
				'class'      => 'header_edit_all',
				'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			)
		),
		'operations'        => array(
			'edit'   => array(
				'label' => &$GLOBALS['TL_LANG']['tl_event_feedback']['edit'],
				'href'  => 'act=edit',
				'icon'  => 'edit.gif'
			),
			'copy'   => array(
				'label' => &$GLOBALS['TL_LANG']['tl_event_feedback']['copy'],
				'href'  => 'act=copy',
				'icon'  => 'copy.gif'
			),
			'delete' => array(
				'label'      => &$GLOBALS['TL_LANG']['tl_event_feedback']['delete'],
				'href'       => 'act=delete',
				'icon'       => 'delete.gif',
				'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'show'   => array(
				'label'      => &$GLOBALS['TL_LANG']['tl_event_feedback']['show'],
				'href'       => 'act=show',
				'icon'       => 'show.gif',
				'attributes' => 'style="margin-right:3px"'
			),
		)
	),
	// Palettes
	'palettes' => array(
		'__selector__' => array('addSubpalette'),
		'default'      => '{first_legend},pid,form,uuid,dateAdded;{survey_legend},learningEffectIndex,learningGoalsAchievedIndex,theoryAndPracticeBalanceIndex,recommendationIndex,safetyFeelingIndex,durationIndex,improvementOpportunity,highlights,comments,wildcard'
	),
	// Fields
	'fields'   => array(
		'id'                            => array(
			'sql' => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid'                           => array(
			'foreignKey' => 'tl_calendar_events.title',
			'relation'   => array('type' => 'belongsTo', 'load' => 'lazy'),
			'eval'       => array('rgxp' => 'natural', 'tl_class' => 'w50 wizard'),
			'sql'        => "int(10) unsigned NOT NULL default 0",
		),
		'tstamp'                        => array(
			'sql' => "int(10) unsigned NOT NULL default '0'"
		),
		'dateAdded'                     => array
		(
			'inputType' => 'text',
			'default'   => time(),
			'sorting'   => true,
			'flag'      => 6,
			'eval'      => array('rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'),
			'sql'       => "varchar(10) NOT NULL default ''",
		),
		'form' => array(
			'inputType'  => 'select',
			'foreignKey' => 'tl_form.title',
			'options' => FormModel::findAll()->fetchEach('id'),
			'relation'   => array('type' => 'belongsTo', 'load' => 'lazy'),
			'eval'       => array('rgxp' => 'natural', 'tl_class' => 'w50 wizard'),
			'sql'        => "int(10) unsigned NOT NULL default 0",
		),
		'uuid' => array(
			'inputType' => 'text',
			'exclude'   => true,
			'search'    => true,
			'filter'    => true,
			'sorting'   => true,
			'eval'      => array('mandatory' => true, 'unique'=> true, 'readonly' => true, 'tl_class' => 'w50'),
			'sql'       => 'varchar(64) BINARY NULL',
		),
		'learningEffectIndex'           => array(
			'inputType' => 'select',
			'exclude'   => true,
			'search'    => true,
			'filter'    => true,
			'sorting'   => true,
			'reference' => $GLOBALS['TL_LANG']['tl_event_feedback']['learningEffectIndexReference'],
			'options'   => array('1', '2', '3', '4'),
			'eval'      => array('mandatory' => true, 'readonly' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'),
			'sql'       => "char(1) NOT NULL default ''",
		),
		'learningGoalsAchievedIndex'    => array(
			'inputType' => 'select',
			'exclude'   => true,
			'search'    => true,
			'filter'    => true,
			'sorting'   => true,
			'reference' => $GLOBALS['TL_LANG']['tl_event_feedback']['learningEffectIndexReference'],
			'options'   => array('1', '2', '3', '4'),
			'eval'      => array('mandatory' => true, 'readonly' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'),
			'sql'       => "char(1) NOT NULL default ''",
		),
		'theoryAndPracticeBalanceIndex' => array(
			'inputType' => 'select',
			'exclude'   => true,
			'search'    => true,
			'filter'    => true,
			'sorting'   => true,
			'reference' => $GLOBALS['TL_LANG']['tl_event_feedback']['learningEffectIndexReference'],
			'options'   => array('1', '2', '3', '4'),
			'eval'      => array('mandatory' => true, 'readonly' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'),
			'sql'       => "char(1) NOT NULL default ''",
		),
		'recommendationIndex'           => array(
			'inputType' => 'select',
			'exclude'   => true,
			'search'    => true,
			'filter'    => true,
			'sorting'   => true,
			'reference' => $GLOBALS['TL_LANG']['tl_event_feedback']['learningEffectIndexReference'],
			'options'   => array('1', '2', '3', '4'),
			'eval'      => array('mandatory' => true, 'readonly' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'),
			'sql'       => "char(1) NOT NULL default ''",
		),
		'safetyFeelingIndex'            => array(
			'inputType' => 'select',
			'exclude'   => true,
			'search'    => true,
			'filter'    => true,
			'sorting'   => true,
			'reference' => $GLOBALS['TL_LANG']['tl_event_feedback']['learningEffectIndexReference'],
			'options'   => array('1', '2', '3', '4'),
			'eval'      => array('mandatory' => true, 'readonly' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'),
			'sql'       => "char(1) NOT NULL default ''",
		),
		'durationIndex'                 => array(
			'inputType' => 'select',
			'exclude'   => true,
			'search'    => true,
			'filter'    => true,
			'sorting'   => true,
			'reference' => $GLOBALS['TL_LANG']['tl_event_feedback']['durationIndexReference'],
			'options'   => array('1', '2', '3', '4', '5'),
			'eval'      => array('mandatory' => true, 'readonly' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'),
			'sql'       => "char(1) NOT NULL default ''",
		),
		'improvementOpportunity'        => array(
			'inputType' => 'textarea',
			'exclude'   => true,
			'search'    => true,
			'filter'    => true,
			'eval'      => array('readonly' => true, 'rte' => 'tinyMCE', 'tl_class' => 'clr'),
			'sql'       => 'text NULL'
		),
		'highlights'                    => array(
			'inputType' => 'textarea',
			'exclude'   => true,
			'search'    => true,
			'filter'    => true,
			'eval'      => array('readonly' => true, 'rte' => 'tinyMCE', 'tl_class' => 'clr'),
			'sql'       => 'text NULL'
		),
		'wildcard'                      => array(
			'inputType' => 'textarea',
			'exclude'   => true,
			'search'    => true,
			'filter'    => true,
			'eval'      => array('rte' => 'tinyMCE', 'tl_class' => 'clr'),
			'sql'       => 'text NULL'
		),
		'comments'                      => array(
			'inputType' => 'textarea',
			'exclude'   => true,
			'search'    => true,
			'filter'    => true,
			'eval'      => array('readonly' => true, 'rte' => 'tinyMCE', 'tl_class' => 'clr'),
			'sql'       => 'text NULL'
		),
	)
);
