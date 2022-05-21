<?php

declare(strict_types=1);

/*
 * This file is part of SAC Event Feedback.
 *
 * (c) Marko Cupic 2022 <m.cupic@gmx.ch>
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

/*
 * Table tl_event_feedback
 */
$GLOBALS['TL_DCA']['tl_event_feedback'] = [
    // Config
    'config'   => [
        'dataContainer'    => 'Table',
        'enableVersioning' => true,
        'sql'              => [
            'keys' => [
                'id'   => 'primary',
                'uuid' => 'index',
                'uuid' => 'unique',
            ],
        ],
    ],
    'list'     => [
        'sorting'           => [
            'mode'        => 2,
            'fields'      => ['dateAdded'],
            'flag'        => 1,
            'panelLayout' => 'filter;sort,search,limit',
        ],
        'label'             => [
            'fields' => [
                'uuid',
                'pid',
            ],
            'format' => '%s, %s',
        ],
        'global_operations' => [
            'all' => [
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ],
        ],
        'operations'        => [
            'edit'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_event_feedback']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ],
            'copy'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_event_feedback']['copy'],
                'href'  => 'act=copy',
                'icon'  => 'copy.gif',
            ],
            'delete' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_event_feedback']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\''.$GLOBALS['TL_LANG']['MSC']['deleteConfirm'].'\'))return false;Backend.getScrollOffset()"',
            ],
            'show'   => [
                'label'      => &$GLOBALS['TL_LANG']['tl_event_feedback']['show'],
                'href'       => 'act=show',
                'icon'       => 'show.gif',
                'attributes' => 'style="margin-right:3px"',
            ],
        ],
    ],
    // Palettes
    'palettes' => [
        '__selector__' => ['addSubpalette'],
        'default'      => '{first_legend},pid,form,uuid,dateAdded;{survey_legend},learningEffectIndex,learningGoalsAchievedIndex,theoryAndPracticeBalanceIndex,recommendationIndex,safetyFeelingIndex,durationIndex,improvementOpportunity,highlights,comments,wildcard',
    ],
    // Fields
    'fields'   => [
        'id'                            => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'pid'                           => [
            'foreignKey' => 'tl_calendar_events.title',
            'relation'   => ['type' => 'belongsTo', 'load' => 'lazy'],
            'eval'       => ['rgxp' => 'natural', 'tl_class' => 'w50 wizard'],
            'sql'        => 'int(10) unsigned NOT NULL default 0',
        ],
        'tstamp'                        => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'dateAdded'                     => [
            'inputType' => 'text',
            'default'   => time(),
            'sorting'   => true,
            'flag'      => 6,
            'eval'      => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql'       => 'int(10) unsigned NOT NULL default 0',
        ],
        'form'                          => [
            'inputType'  => 'select',
            'foreignKey' => 'tl_form.title',
            'options'    => FormModel::findAll()->fetchEach('id'),
            'relation'   => ['type' => 'belongsTo', 'load' => 'lazy'],
            'eval'       => ['rgxp' => 'natural', 'tl_class' => 'w50 wizard'],
            'sql'        => 'int(10) unsigned NOT NULL default 0',
        ],
        'uuid'                          => [
            'inputType' => 'text',
            'exclude'   => true,
            'search'    => true,
            'filter'    => true,
            'sorting'   => true,
            'eval'      => ['mandatory' => true, 'unique' => true, 'readonly' => true, 'tl_class' => 'w50'],
            'sql'       => 'varchar(64) BINARY NULL',
        ],
        'learningEffectIndex'           => [
            'inputType' => 'select',
            'exclude'   => true,
            'search'    => true,
            'filter'    => true,
            'sorting'   => true,
            'reference' => $GLOBALS['TL_LANG']['tl_event_feedback']['learningEffectIndexReference'] ?? '',
            'options'   => ['1', '2', '3', '4'],
            'eval'      => ['mandatory' => true, 'readonly' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'learningGoalsAchievedIndex'    => [
            'inputType' => 'select',
            'exclude'   => true,
            'search'    => true,
            'filter'    => true,
            'sorting'   => true,
            'reference' => $GLOBALS['TL_LANG']['tl_event_feedback']['learningEffectIndexReference'] ?? '',
            'options'   => ['1', '2', '3', '4'],
            'eval'      => ['mandatory' => true, 'readonly' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'theoryAndPracticeBalanceIndex' => [
            'inputType' => 'select',
            'exclude'   => true,
            'search'    => true,
            'filter'    => true,
            'sorting'   => true,
            'reference' => $GLOBALS['TL_LANG']['tl_event_feedback']['learningEffectIndexReference'] ?? '',
            'options'   => ['1', '2', '3', '4'],
            'eval'      => ['mandatory' => true, 'readonly' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'recommendationIndex'           => [
            'inputType' => 'select',
            'exclude'   => true,
            'search'    => true,
            'filter'    => true,
            'sorting'   => true,
            'reference' => $GLOBALS['TL_LANG']['tl_event_feedback']['learningEffectIndexReference'] ?? '',
            'options'   => ['1', '2', '3', '4'],
            'eval'      => ['mandatory' => true, 'readonly' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'safetyFeelingIndex'            => [
            'inputType' => 'select',
            'exclude'   => true,
            'search'    => true,
            'filter'    => true,
            'sorting'   => true,
            'reference' => $GLOBALS['TL_LANG']['tl_event_feedback']['learningEffectIndexReference'] ?? '',
            'options'   => ['1', '2', '3', '4'],
            'eval'      => ['mandatory' => true, 'readonly' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'durationIndex'                 => [
            'inputType' => 'select',
            'exclude'   => true,
            'search'    => true,
            'filter'    => true,
            'sorting'   => true,
            'reference' => $GLOBALS['TL_LANG']['tl_event_feedback']['durationIndexReference'] ?? '',
            'options'   => ['1', '2', '3', '4', '5'],
            'eval'      => ['mandatory' => true, 'readonly' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'improvementOpportunity'        => [
            'inputType' => 'textarea',
            'exclude'   => true,
            'search'    => true,
            'filter'    => true,
            'eval'      => ['readonly' => true, 'rte' => 'tinyMCE', 'tl_class' => 'clr'],
            'sql'       => 'text NULL',
        ],
        'highlights'                    => [
            'inputType' => 'textarea',
            'exclude'   => true,
            'search'    => true,
            'filter'    => true,
            'eval'      => ['readonly' => true, 'rte' => 'tinyMCE', 'tl_class' => 'clr'],
            'sql'       => 'text NULL',
        ],
        'wildcard'                      => [
            'inputType' => 'textarea',
            'exclude'   => true,
            'search'    => true,
            'filter'    => true,
            'eval'      => ['rte' => 'tinyMCE', 'tl_class' => 'clr'],
            'sql'       => 'text NULL',
        ],
        'comments'                      => [
            'inputType' => 'textarea',
            'exclude'   => true,
            'search'    => true,
            'filter'    => true,
            'eval'      => ['readonly' => true, 'rte' => 'tinyMCE', 'tl_class' => 'clr'],
            'sql'       => 'text NULL',
        ],
    ],
];
