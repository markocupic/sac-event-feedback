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

namespace Markocupic\SacEventFeedback;

use Contao\CalendarEventsMemberModel;
use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\Database;
use Contao\Date;
use Contao\FormModel;
use Contao\MemberModel;
use Contao\PageModel;
use Markocupic\SacEventFeedback\Model\EventFeedbackModel;
use Markocupic\SacEventFeedback\Model\EventFeedbackReminderModel;
use Markocupic\SacEventToolBundle\CalendarEventsHelper;
use NotificationCenter\Model\Notification;

class EventFeedbackHelper
{
    private array $onlineFeedbackConfigs;

    public function __construct(array $onlineFeedbackConfigs)
    {
        $this->onlineFeedbackConfigs = $onlineFeedbackConfigs;
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

    /**
     * @throws \Exception
     */
    public function deleteFeedbackReminder(CalendarEventsMemberModel $eventMember): void
    {
        Database::getInstance()
            ->prepare('DELETE FROM tl_event_feedback_reminder WHERE uuid=?')
            ->execute($eventMember->uuid)
        ;
    }

    public function getEventFeedbackFromUuid(string $uuid): ?EventFeedbackModel
    {
        return EventFeedbackModel::findOneByUuid($uuid);
    }

    public function getEventFromUuid(string $uuid): ?CalendarEventsModel
    {
        $eventMember = CalendarEventsMemberModel::findOneByUuid($uuid);

        if (null !== $eventMember) {
            if (null !== ($event = CalendarEventsModel::findByPk($eventMember->eventId))) {
                return $event;
            }
        }

        return null;
    }

    public function getFrontendUserFromUuid(string $uuid): ?MemberModel
    {
        $eventMember = CalendarEventsMemberModel::findOneByUuid($uuid);

        if (null !== $eventMember) {
            if (null !== ($member = MemberModel::findByPk($eventMember->contaoMemberId))) {
                return $member;
            }
        }

        return null;
    }

    /**
     * @throws \Exception
     */
    public function addFeedbackReminder(CalendarEventsMemberModel $eventMember): void
    {
        if (null === ($event = CalendarEventsModel::findByPk($eventMember->eventId))) {
            return;
        }

        $arrConfig = $this->getOnlineFeedbackConfiguration($event);

        if (empty($arrConfig['send_reminder_after_days'])) {
            throw new \Exception('Array "feedback_expiration_time" should not be empty!');
        }

        if (!isset($arrConfig['feedback_expiration_time'])) {
            throw new \Exception('Key "feedback_expiration_time" should not be empty!');
        }

        $dateToday = new \DateTimeImmutable(Date::parse('Y-m-d'));
        $dateEventEnd = new \DateTimeImmutable(Date::parse('Y-m-d', $event->endDate));

        $dateStartReminding = $dateToday;
        // Prevent sending reminders before the event end date is reached
        if ($dateToday->getTimestamp() <= $dateEventEnd->getTimestamp()) {
            $dateStartReminding = $dateEventEnd;
        }

        $dateExpiration = $dateStartReminding->modify('+'.$arrConfig['feedback_expiration_time'].' day');

        foreach ($arrConfig['send_reminder_after_days'] as $intDays) {
            $dateSendReminder = $dateStartReminding->modify('+'.$intDays.' day');

            $objDb = Database::getInstance()
                ->prepare('SELECT * FROM tl_event_feedback_reminder WHERE uuid=? AND executionDate=?')
                ->execute($eventMember->uuid, $dateStartReminding->getTimestamp())
            ;

            // Prevent inserting duplicate records
            if (!$objDb->numRows) {
                $set = [
                    'pid' => $eventMember->id,
                    'uuid' => $eventMember->uuid,
                    'executionDate' => $dateSendReminder->getTimestamp(),
                    'dateAdded' => time(),
                    'tstamp' => time(),
                    'feedbackExpirationDate' => $dateExpiration->getTimestamp(),
                ];

                $objReminder = new EventFeedbackReminderModel();
                $objReminder->mergeRow($set);
                $objReminder->save();
            }
        }
    }

    public function sendReminder(): void
    {
        /** @var PageModel $objPage */
        global $objPage;

        $tstampToday = strtotime(Date::parse('Y-m-d'));

        // Delete no more used records
        Database::getInstance()
            ->prepare('DELETE FROM tl_event_feedback_reminder WHERE feedbackExpirationDate<?')
            ->execute($tstampToday)
        ;

        $objReminder = Database::getInstance()
            ->prepare('SELECT * FROM tl_event_feedback_reminder WHERE executionDate=?')
            ->limit(20)
            ->execute($tstampToday)
        ;

        while ($objReminder->next()) {
            if (null !== ($member = CalendarEventsMemberModel::findOneByUuid($objReminder->uuid))) {
                $event = CalendarEventsModel::findByPk($member->eventId);

                if (null !== $event) {
                    if (!$this->eventHasValidFeedbackConfiguration($event)) {
                        throw new \Exception($GLOBALS['TL_LANG']['ERR']['sacEvFb']['invalidEventFeedbackConfiguration']);
                    }
                    $notification = $this->getNotification($event);
                    $arrTokens = $this->setNotificationTokens($member);

                    $arrResult = $notification->send($arrTokens, $objPage->language);

                    if (!empty($arrResult) && \is_array($arrResult)) {
                        ++$member->countOnlineEventFeedbackNotifications;
                        $member->save();
                    }
                }
            }

            // Delete reminder
            Database::getInstance()
                ->prepare('DELETE FROM tl_event_feedback_reminder WHERE id=?')
                ->execute($objReminder->id)
            ;
        }
    }

    public function getNotification(CalendarEventsModel $event): ?Notification
    {
        if (null === ($calendar = CalendarModel::findByPk($event->pid))) {
            return null;
        }

        if (!$calendar->enableOnlineEventFeedback) {
            return null;
        }

        return Notification::findByPk($calendar->onlineFeedbackNotification);
    }

    public function getForm(CalendarEventsModel $event): ?FormModel
    {
        if (null === ($calendar = CalendarModel::findByPk($event->pid))) {
            return null;
        }

        if (!$calendar->enableOnlineEventFeedback) {
            return null;
        }

        return FormModel::findByPk($calendar->onlineFeedbackForm);
    }

    public function getPage(CalendarEventsModel $event): ?PageModel
    {
        if (null === ($calendar = CalendarModel::findByPk($event->pid))) {
            return null;
        }

        if (!$calendar->enableOnlineEventFeedback) {
            return null;
        }

        return PageModel::findByPk($calendar->onlineFeedbackPage);
    }

    public function getOnlineFeedbackConfiguration(CalendarEventsModel $event): ?array
    {
        if (null === ($calendar = CalendarModel::findByPk($event->pid))) {
            return null;
        }

        if (!$calendar->enableOnlineEventFeedback) {
            return null;
        }

        if (!$calendar->onlineFeedbackConfiguration || !isset($this->onlineFeedbackConfigs[$calendar->onlineFeedbackConfiguration])) {
            return null;
        }

        return $this->onlineFeedbackConfigs[$calendar->onlineFeedbackConfiguration];
    }

    public function setNotificationTokens(CalendarEventsMemberModel $member): array
    {
        if (null === ($event = CalendarEventsModel::findByPk($member->eventId))) {
            throw new \Exception('Could not find the event the member belongs to.');
        }

        if (!$this->eventHasValidFeedbackConfiguration($event)) {
            throw new \Exception($GLOBALS['TL_LANG']['ERR']['sacEvFb']['invalidEventFeedbackConfiguration']);
        }

        $page = $this->getPage($event);

        $objInstructor = CalendarEventsHelper::getMainInstructor($event);
        $arrTokens = [];
        $arrTokens['instructor_name'] = CalendarEventsHelper::getMainInstructorName($event);
        $arrTokens['instructor_email'] = $objInstructor ? $objInstructor->email : '';
        $arrTokens['admin_email'] = $GLOBALS['TL_ADMIN_EMAIL'];
        $arrTokens['participant_firstname'] = $member->firstname;
        $arrTokens['participant_lastname'] = $member->lastname;
        $arrTokens['participant_email'] = $member->email;
        $arrTokens['participant_uuid'] = $member->uuid;
        $arrTokens['event_name'] = $event->title;
        $arrTokens['feedback_url'] = sprintf('%s?event-reg-uuid=%s', $page->getAbsoluteUrl(), $member->uuid);

        return $arrTokens;
    }
}
