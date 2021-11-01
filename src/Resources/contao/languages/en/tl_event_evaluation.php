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

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_event_evaluation']['survey_legend'] = "Auswertungsergebnisse";

/**
 * Operations
 */
$GLOBALS['TL_LANG']['tl_event_evaluation']['edit'] = ["Datensatz mit ID: %s bearbeiten", "Datensatz mit ID: %s bearbeiten"];
$GLOBALS['TL_LANG']['tl_event_evaluation']['copy'] = ["Datensatz mit ID: %s kopieren", "Datensatz mit ID: %s kopieren"];
$GLOBALS['TL_LANG']['tl_event_evaluation']['delete'] = ["Datensatz mit ID: %s löschen", "Datensatz mit ID: %s löschen"];
$GLOBALS['TL_LANG']['tl_event_evaluation']['show'] = ["Datensatz mit ID: %s ansehen", "Datensatz mit ID: %s ansehen"];

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_event_evaluation']['uuid'] = ["UUID"];
$GLOBALS['TL_LANG']['tl_event_evaluation']['dateAdded'] = ["Erstellt am"];
$GLOBALS['TL_LANG']['tl_event_evaluation']['learningEffectIndex'] = ["Ich habe etwas gelernt."];
$GLOBALS['TL_LANG']['tl_event_evaluation']['learningGoalsAchievedIndex'] = ["Die Lernziele wurden erreicht."];
$GLOBALS['TL_LANG']['tl_event_evaluation']['theoryAndPracticeBalanceIndex'] = ["Theorie und Praxis waren richtig bemessen."];
$GLOBALS['TL_LANG']['tl_event_evaluation']['recommendationIndex'] = ["Würdest du den Kurs weiterempfehlen!"];
$GLOBALS['TL_LANG']['tl_event_evaluation']['safetyFeelingIndex'] = ["Während des Kurses fühlte ich mich sicher."];
$GLOBALS['TL_LANG']['tl_event_evaluation']['durationIndex'] = ["Der Kurs hatte die richtige Dauer."];
$GLOBALS['TL_LANG']['tl_event_evaluation']['improvementOpportunity'] = ["Was kann verbessert werden?"];
$GLOBALS['TL_LANG']['tl_event_evaluation']['highlights'] = ["Was sollte unbedingt beibehalten werden?"];
$GLOBALS['TL_LANG']['tl_event_evaluation']['wildcard'] = ["Platzhalter"];
$GLOBALS['TL_LANG']['tl_event_evaluation']['comments'] = ["Kommentare/Anregungen"];

/**
 * References
 */
$GLOBALS['TL_LANG']['tl_event_evaluation']['learningEffectIndexReference'] = [
    '1' => 'Ja, sehr!',
    '2' => 'Ja!',
    '3' => 'Nein!',
    '4' => 'Nein, gar nicht!',
];

$GLOBALS['TL_LANG']['tl_event_evaluation']['durationIndexReference'] = [
    '1' => 'Zu kurz!',
    '2' => 'Etwas zu kurz!',
    '3' => 'Genau richtig!',
    '4' => 'Etwas zu lange!',
    '5' => 'Zu lange!',
];

