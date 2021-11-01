<?php

declare(strict_types=1);

/*
 * This file is part of SAC Event Evaluation Bundle.
 * 
 * (c) Marko Cupic 2021 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/sac-event-evaluation
 */

use Markocupic\SacEventEvaluation\Controller\FrontendModule\EventEvaluationController;

/**
 * Backend modules
 */
$GLOBALS['TL_LANG']['MOD']['event_evaluation'] = 'Event Evaluation';
$GLOBALS['TL_LANG']['MOD']['event_evaluation'] = ['Event Evaluation', 'Event Evaluation'];

/**
 * Frontend modules
 */
$GLOBALS['TL_LANG']['FMD']['event_evaluation'] = 'Event Evaluation';
$GLOBALS['TL_LANG']['FMD'][EventEvaluationController::TYPE] = ['Event Evaluation', 'Event Evaluation'];

