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

/*
 * Table tl_event_feedback_reminder
 */
$GLOBALS['TL_DCA']['tl_event_feedback_reminder'] = [
    // Config
    'config'   => [
        'dataContainer' => 'Table',
        'ptable'        => 'tl_calendar_events_member',
        //'closed' => true,
        //'notDeletable' => true,
        //'notEditable' => true,
        //'notCopyable'       => true,
        'sql'           => [
            'keys' => [
                'id'                 => 'primary',
                'uuid'               => 'index',
                'uuid,executionDate' => 'unique',
            ],
        ],
    ],
    'list'     => [
        'sorting'           => [
            'mode'        => 2,
            'fields'      => ['uuid', 'dateAdded'],
            'flag'        => 1,
            'panelLayout' => 'filter;sort,search,limit',
        ],
        'label'             => [
            'fields' => ['uuid', 'pid'],
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
                'href' => 'act=edit',
                'icon' => 'edit.gif',
            ],
            'copy'   => [
                'href' => 'act=copy',
                'icon' => 'copy.gif',
            ],
            'delete' => [
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\''.($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null).'\'))return false;Backend.getScrollOffset()"',
            ],

            'show' => [
                'href'       => 'act=show',
                'icon'       => 'show.gif',
                'attributes' => 'style="margin-right:3px"',
            ],
        ],
    ],
    // Palettes
    'palettes' => [
        'default' => '{title_legend},uuid,dateAdded,executionDate',
    ],
    // Fields
    'fields'   => [
        'id'            => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'pid'           => [
            'foreignKey' => 'tl_calendar_events_member.CONCAT(firstname," ",lastname, " [", sacMemberId, "]")',
            'sql'        => 'int(10) unsigned NOT NULL default 0',
            'relation'   => [
                'type' => 'belongsTo',
                'load' => 'lazy',
            ],
        ],
        'tstamp'        => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'dateAdded'     => [
            'inputType' => 'text',
            'default'   => time(),
            'sorting'   => true,
            'flag'      => 6,
            'eval'      => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql'       => 'int(10) unsigned NOT NULL default 0',
        ],
        'uuid'          => [
            'inputType' => 'text',
            'exclude'   => true,
            'search'    => true,
            'filter'    => true,
            'sorting'   => true,
            'eval'      => ['mandatory' => true, 'readonly' => true, 'tl_class' => 'w50'],
            'sql'       => "char(36) NOT NULL default ''",
        ],
        'executionDate' => [
            'inputType' => 'text',
            'sorting'   => true,
            'flag'      => 6,
            'eval'      => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql'       => "varchar(10) NOT NULL default ''",
        ],
        'expiration'    => [
            'inputType' => 'text',
            'sorting'   => true,
            'flag'      => 6,
            'eval'      => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql'       => "varchar(10) NOT NULL default ''",
        ],
    ],
];
