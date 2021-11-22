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

/**
 * Table tl_event_feedback_reminder
 */
$GLOBALS['TL_DCA']['tl_event_feedback_reminder'] = array(
	// Config
	'config'   => array(
		'dataContainer'    => 'Table',
		'ptable' => 'tl_calendar_events_member',
		//'closed' => true,
		//'notDeletable' => true,
		//'notEditable' => true,
		//'notCopyable'       => true,
		'sql'              => array(
			'keys' => array(
				'id' => 'primary',
				'uuid' => 'index',
				'uuid,executionDate' => 'unique',
			)
		),
	),
	'list'     => array(
		'sorting'           => array(
			'mode'        => 2,
			'fields'      => array('uuid', 'dateAdded'),
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
				'href'  => 'act=edit',
				'icon'  => 'edit.gif'
			),
			'copy'   => array(
				'href'  => 'act=copy',
				'icon'  => 'copy.gif'
			),
			'delete' => array(
				'href'       => 'act=delete',
				'icon'       => 'delete.gif',
				'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),

			'show'   => array(
				'href'       => 'act=show',
				'icon'       => 'show.gif',
				'attributes' => 'style="margin-right:3px"'
			),
		)
	),
	// Palettes
	'palettes' => array(
		'default'      => '{title_legend},uuid,dateAdded,executionDate'
	),
	// Fields
	'fields'   => array(
		'id'                            => array(
			'sql' => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid'                           => array(
			'foreignKey' => 'tl_calendar_events_member.CONCAT(firstname," ",lastname, " [", sacMemberId, "]")',
			'sql'        => "int(10) unsigned NOT NULL default 0",
			'relation'   => array('type' => 'belongsTo', 'load' => 'lazy')
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
		'uuid'                          => array(
			'inputType' => 'text',
			'exclude'   => true,
			'search'    => true,
			'filter'    => true,
			'sorting'   => true,
			'eval'      => array('mandatory' => true, 'readonly' => true, 'tl_class' => 'w50'),
			'sql'       => "char(36) NOT NULL default ''",
		),
		'executionDate'                     => array
		(
			'inputType' => 'text',
			'sorting'   => true,
			'flag'      => 6,
			'eval'      => array('rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'),
			'sql'       => "varchar(10) NOT NULL default ''",
		),
		'expiration'                     => array
		(
			'inputType' => 'text',
			'sorting'   => true,
			'flag'      => 6,
			'eval'      => array('rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'),
			'sql'       => "varchar(10) NOT NULL default ''",
		),
	)
);
