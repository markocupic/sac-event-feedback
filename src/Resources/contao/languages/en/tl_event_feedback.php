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
 * Legends
 */
$GLOBALS['TL_LANG']['tl_event_feedback']['survey_legend'] = "Auswertungsergebnisse";

/**
 * Operations
 */
$GLOBALS['TL_LANG']['tl_event_feedback']['edit'] = array("Datensatz mit ID: %s bearbeiten", "Datensatz mit ID: %s bearbeiten");
$GLOBALS['TL_LANG']['tl_event_feedback']['copy'] = array("Datensatz mit ID: %s kopieren", "Datensatz mit ID: %s kopieren");
$GLOBALS['TL_LANG']['tl_event_feedback']['delete'] = array("Datensatz mit ID: %s löschen", "Datensatz mit ID: %s löschen");
$GLOBALS['TL_LANG']['tl_event_feedback']['show'] = array("Datensatz mit ID: %s ansehen", "Datensatz mit ID: %s ansehen");

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_event_feedback']['form'] = array("Formular", 'Wählen Sie das Formular aus.');
$GLOBALS['TL_LANG']['tl_event_feedback']['uuid'] = array("UUID");
$GLOBALS['TL_LANG']['tl_event_feedback']['dateAdded'] = array("Erstellt am");
$GLOBALS['TL_LANG']['tl_event_feedback']['learningEffectIndex'] = array("Ich habe etwas gelernt.");
$GLOBALS['TL_LANG']['tl_event_feedback']['learningGoalsAchievedIndex'] = array("Die Lernziele wurden erreicht.");
$GLOBALS['TL_LANG']['tl_event_feedback']['theoryAndPracticeBalanceIndex'] = array("Theorie und Praxis waren richtig bemessen.");
$GLOBALS['TL_LANG']['tl_event_feedback']['recommendationIndex'] = array("Würdest du den Kurs weiterempfehlen!");
$GLOBALS['TL_LANG']['tl_event_feedback']['safetyFeelingIndex'] = array("Während des Kurses fühlte ich mich sicher.");
$GLOBALS['TL_LANG']['tl_event_feedback']['durationIndex'] = array("Der Kurs hatte die richtige Dauer.");
$GLOBALS['TL_LANG']['tl_event_feedback']['improvementOpportunity'] = array("Was kann verbessert werden?");
$GLOBALS['TL_LANG']['tl_event_feedback']['highlights'] = array("Was sollte unbedingt beibehalten werden?");
$GLOBALS['TL_LANG']['tl_event_feedback']['wildcard'] = array("Platzhalter");
$GLOBALS['TL_LANG']['tl_event_feedback']['comments'] = array("Kommentare/Anregungen");

/**
 * References
 */
$GLOBALS['TL_LANG']['tl_event_feedback']['learningEffectIndexReference'] = array(
    '1' => 'Ja, sehr!',
    '2' => 'Ja!',
    '3' => 'Nein!',
    '4' => 'Nein, gar nicht!',
);

$GLOBALS['TL_LANG']['tl_event_feedback']['durationIndexReference'] = array(
    '1' => 'Zu kurz!',
    '2' => 'Etwas zu kurz!',
    '3' => 'Genau richtig!',
    '4' => 'Etwas zu lange!',
    '5' => 'Zu lange!',
);

