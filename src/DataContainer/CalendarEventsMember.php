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

namespace Markocupic\SacEventFeedback\DataContainer;

use Contao\CalendarEventsMemberModel;
use Contao\CalendarEventsModel;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Markocupic\SacEventFeedback\EventFeedbackHelper;
use Markocupic\SacEventFeedback\FeedbackReminder\CreateFeedbackReminderTask;
use Markocupic\SacEventFeedback\FeedbackReminder\FeedbackReminder;

class CalendarEventsMember
{
    private EventFeedbackHelper $eventFeedbackHelper;
    private FeedbackReminder $feedbackReminder;
    private CreateFeedbackReminderTask $createFeedbackReminderTask;

    public function __construct(EventFeedbackHelper $eventFeedbackHelper, FeedbackReminder $feedbackReminder, CreateFeedbackReminderTask $createFeedbackReminderTask)
    {
        $this->eventFeedbackHelper = $eventFeedbackHelper;
        $this->feedbackReminder = $feedbackReminder;
        $this->createFeedbackReminderTask = $createFeedbackReminderTask;
    }

    /**
     * tl_calendar_events_member.hasParticipated save callback.
     *
     * @Callback(table="tl_calendar_events_member", target="fields.hasParticipated.save")
     */
    public function onCompletedEvent(string $value, DataContainer $dc): string
    {
        $calendarEventsMemberModel = CalendarEventsMemberModel::findByPk($dc->id);
        $calendarEventsModel = CalendarEventsModel::findByPk($calendarEventsMemberModel->eventId);

        if (null === $calendarEventsMemberModel || null === $calendarEventsModel || !$this->eventFeedbackHelper->eventHasValidFeedbackConfiguration($calendarEventsModel)) {
            return $value;
        }

        // Do nothing if member did already a feedback
        if (null !== $this->eventFeedbackHelper->getEventFeedbackFromUuid($calendarEventsMemberModel->uuid)) {
            return $value;
        }

        // If $value === ''
        if (!$value && !$calendarEventsMemberModel->countOnlineEventFeedbackNotifications) {
            $this->feedbackReminder->deleteFeedbackReminderByEventMember($calendarEventsMemberModel);
        }

        // If $value === '1'
        if ($value && !$calendarEventsMemberModel->countOnlineEventFeedbackNotifications) {
            $this->createFeedbackReminderTask->create($calendarEventsMemberModel);
        }

        // Return the processed value
        return $value;
    }
}
