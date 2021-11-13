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

use Markocupic\SacEventEvaluation\Model\EventEvaluationModel;

/**
 * Backend modules
 */
$GLOBALS['BE_MOD']['event_evaluation']['event_evaluation'] = array(
	'tables' => array('tl_event_evaluation')
);

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_event_evaluation'] = EventEvaluationModel::class;
