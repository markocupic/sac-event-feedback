<?php

declare(strict_types=1);

/*
 * This file is part of SAC Event Feedback.
 *
 * (c) Marko Cupic 2023 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/sac-event-feedback
 */

namespace Markocupic\SacEventFeedback;

use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\FormModel;
use Contao\MemberModel;
use Contao\PageModel;
use Markocupic\SacEventFeedback\Model\EventFeedbackModel;
use Markocupic\SacEventToolBundle\Model\CalendarEventsMemberModel;
use NotificationCenter\Model\Notification;

class EventFeedbackHelper
{
    public function __construct(
        private readonly array $feedbackConfig,
    ) {
    }

    public function eventHasValidFeedbackConfiguration(CalendarEventsModel $event): bool
    {
        if (!$event->enableOnlineEventFeedback) {
            return false;
        }

        if (null === ($calendar = CalendarModel::findByPk($event->pid))) {
            return false;
        }

        // Test is online evaluation enabled on calendar level
        if (!$calendar->enableOnlineEventFeedback) {
            return false;
        }

        // Test has configuration
        if (null === $this->getOnlineFeedbackConfiguration($event)) {
            return false;
        }

        // Test has form
        if (null === $this->getForm($event)) {
            return false;
        }

        // Test has notification
        if (null === $this->getNotification($event)) {
            return false;
        }

        // Test has valid page
        if (null === $this->getPage($event)) {
            return false;
        }

        return true;
    }

    public function getEventFeedbackFromUuid(string $uuid): EventFeedbackModel|null
    {
        return EventFeedbackModel::findOneByUuid($uuid);
    }

    public function getEventFromUuid(string $uuid): CalendarEventsModel|null
    {
        $eventMember = CalendarEventsMemberModel::findOneByUuid($uuid);

        if (null !== $eventMember) {
            if (null !== ($event = CalendarEventsModel::findByPk($eventMember->eventId))) {
                return $event;
            }
        }

        return null;
    }

    public function getFrontendUserFromUuid(string $uuid): MemberModel|null
    {
        $eventMember = CalendarEventsMemberModel::findOneByUuid($uuid);

        if (null !== $eventMember) {
            if (null !== ($member = MemberModel::findByPk($eventMember->contaoMemberId))) {
                return $member;
            }
        }

        return null;
    }

    public function getForm(CalendarEventsModel $event): FormModel|null
    {
        if (null === ($calendar = CalendarModel::findByPk($event->pid))) {
            return null;
        }

        if (!$calendar->enableOnlineEventFeedback) {
            return null;
        }

        return FormModel::findByPk($calendar->onlineFeedbackForm);
    }

    public function getNotification(CalendarEventsModel $event): Notification|null
    {
        if (null === ($calendar = CalendarModel::findByPk($event->pid))) {
            return null;
        }

        if (!$calendar->enableOnlineEventFeedback) {
            return null;
        }

        return Notification::findByPk($calendar->onlineFeedbackNotification);
    }

    public function getPage(CalendarEventsModel $event): PageModel|null
    {
        if (null === ($calendar = CalendarModel::findByPk($event->pid))) {
            return null;
        }

        if (!$calendar->enableOnlineEventFeedback) {
            return null;
        }

        return PageModel::findByPk($calendar->onlineFeedbackPage);
    }

    public function getOnlineFeedbackConfiguration(CalendarEventsModel $event): array|null
    {
        if (null === ($calendar = CalendarModel::findByPk($event->pid))) {
            return null;
        }

        if (!$calendar->enableOnlineEventFeedback) {
            return null;
        }

        if (!$calendar->onlineFeedbackConfiguration || !isset($this->feedbackConfig[$calendar->onlineFeedbackConfiguration])) {
            return null;
        }

        return $this->feedbackConfig[$calendar->onlineFeedbackConfiguration];
    }
}
