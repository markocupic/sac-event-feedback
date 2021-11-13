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

namespace Markocupic\SacEventEvaluation\DataContainer;

use Contao\CalendarEventsMemberModel;
use Contao\CalendarModel;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Contao\FormModel;
use Markocupic\SacEventEvaluation\Model\EventEvaluationModel;

class TlCalendarEventsMember
{
    /**
     * @var ContaoFramework
     */
    private $framework;

    /**
     * @var array
     */
    private $onlineEvaluationConfigs;

    public function __construct(ContaoFramework $framework, array $onlineEvaluationConfigs)
    {
        $this->framework = $framework;
        $this->onlineEvaluationConfigs = $onlineEvaluationConfigs;
    }

    /**
     * @Callback(table="tl_calendar_events_member", target="fields.hasParticipated.save")
     */
    public function onCompletedEvent($value, DataContainer $dc)
    {
        return $value;
        $calendarEventsMemberModel = CalendarEventsMemberModel::findByPk($dc->id);
        $calendarEventsModel = CalendarEventsModel::findByPk($calendarEventsMemberModel->eventId);
        $calendarModel = CalendarModel::findByPk($calendarEventsModel->pid);
        $formModel = FormModel::findByPk($calendarModel->onlineEvaluationForm);
        $notificationModel = FormModel::findByPk($calendarModel->onlineEvaluationNotification);

        $eventEvaluationModel = EventEvaluationModel::findByUuid($calendarEventsMemberModel->uuid);

        $arrConfig = null;

        if ($calendarModel && '' !== $calendarModel->onlineEvaluationConfiguration) {
            $arrConfig = ($this->onlineEvaluationConfigs[$calendarModel->onlineEvaluationConfiguration] ?? null);
        }

        // Do nothing if event evaluation has already filled out.
        if (null !== $eventEvaluationModel || null === $calendarModel || null === $calendarEventsMemberModel || null === $calendarEventsModel) {
            return $value;
        }

        if ('1' === $value && $calendarModel->enableOnlineEventEvaluation && $calendarEventsModel->enableOnlineEventEvaluation) {
            $calendarEventsMemberModel->doOnlineEventEvaluation = '1';

            if (null !== $formModel && $arrConfig && null === $eventEvaluationModel) {
                $arrData = serialize([
                    'form' => $formModel->id,
                    'config' => $arrConfig,
                    'notification' => $notificationModel->id,
                ]);
                $calendarEventsMemberModel->onlineEventEvaluationData = serialize($arrData);
                $calendarEventsMemberModel->save();
            }
        }

        if ('' === $value && null === $eventEvaluationModel) {
            $calendarEventsMemberModel->doOnlineEventEvaluation = '';
            $calendarEventsMemberModel->onlineEventEvaluationData = null;
            $calendarEventsMemberModel->save();
        }

        // Return the processed value
        return $value;
    }
}
