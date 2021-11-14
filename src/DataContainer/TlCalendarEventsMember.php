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

namespace Markocupic\SacEventFeedback\DataContainer;

use Contao\CalendarEventsMemberModel;
use Contao\CalendarModel;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Contao\FormModel;
use Markocupic\SacEventFeedback\Model\EventFeedbackModel;

class TlCalendarEventsMember
{
    /**
     * @var ContaoFramework
     */
    private $framework;

    /**
     * @var array
     */
    private $onlineFeedbackConfigs;

    public function __construct(ContaoFramework $framework, array $onlineFeedbackConfigs)
    {
        $this->framework = $framework;
        $this->onlineFeedbackConfigs = $onlineFeedbackConfigs;
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
        $formModel = FormModel::findByPk($calendarModel->onlineFeedbackForm);
        $notificationModel = FormModel::findByPk($calendarModel->onlineFeedbackNotification);

        $eventFeedbackModel = EventFeedbackModel::findByUuid($calendarEventsMemberModel->uuid);

        $arrConfig = null;

        if ($calendarModel && '' !== $calendarModel->onlineFeedbackConfiguration) {
            $arrConfig = ($this->onlineFeedbackConfigs[$calendarModel->onlineFeedbackConfiguration] ?? null);
        }

        // Do nothing if event feedback has already filled out.
        if (null !== $eventFeedbackModel || null === $calendarModel || null === $calendarEventsMemberModel || null === $calendarEventsModel) {
            return $value;
        }

        if ('1' === $value && $calendarModel->enableOnlineEventFeedback && $calendarEventsModel->enableOnlineEventFeedback) {
            $calendarEventsMemberModel->doOnlineEventFeedback = '1';

            if (null !== $formModel && $arrConfig && null === $eventFeedbackModel) {
                $arrData = serialize([
                    'form' => $formModel->id,
                    'config' => $arrConfig,
                    'notification' => $notificationModel->id,
                ]);
                $calendarEventsMemberModel->onlineEventFeedbackData = serialize($arrData);
                $calendarEventsMemberModel->save();
            }
        }

        if ('' === $value && null === $eventFeedbackModel) {
            $calendarEventsMemberModel->doOnlineEventFeedback = '';
            $calendarEventsMemberModel->onlineEventFeedbackData = null;
            $calendarEventsMemberModel->save();
        }

        // Return the processed value
        return $value;
    }
}
