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
use Contao\PageModel;
use Markocupic\SacEventFeedback\Model\EventFeedbackReminderModel;
use Markocupic\SacEventToolBundle\CalendarEventsHelper;
use NotificationCenter\Model\Notification;

class EventFeedbackHelper
{
    /**
     * @var array
     */
    private $onlineFeedbackConfigs;

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

        $dateToday = Date::parse('Y-m-d');

        $eventEndDate = new \DateTime(Date::parse('Y-m-d', $event->endDate));
        $eventEndDate->modify('+'.$arrConfig['feedback_expiration_time'].' day');
        $feedbackExpirationTstamp = $eventEndDate->getTimestamp();

        foreach ($arrConfig['send_reminder_after_days'] as $intDays) {
            $date = new \DateTime($dateToday);
            $date->modify('+'.$intDays.' day');

            $objDb = Database::getInstance()
                ->prepare('SELECT * FROM tl_event_feedback_reminder WHERE uuid=? AND executionDate=?')
                ->execute($eventMember->uuid, $date->getTimestamp())
            ;

            // Prevent inserting duplicate records
            if (!$objDb->numRows) {
                $set = [
                    'pid' => $eventMember->id,
                    'uuid' => $eventMember->uuid,
                    'executionDate' => $date->getTimestamp(),
                    'dateAdded' => time(),
                    'tstamp' => time(),
                    'feedbackExpirationDate' => $feedbackExpirationTstamp,
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
            if (null !== ($member = CalendarEventsMemberModel::findByUuid($objReminder->uuid))) {
                $event = CalendarEventsModel::findByPk($member->eventId);

                if (null !== $event) {
                    if (!$this->eventHasValidFeedbackConfiguration($event)) {
                        throw new \Exception($GLOBALS['TL_LANG']['ERR']['invalidEventFeedbackConfiguration']);
                    }
                    $notification = $this->getNotification($event);
                    $arrTokens = $this->setNotificationTokens($member);

                    $arrResult = $notification->send($arrTokens, $objPage->language);
                    if(is_array($arrResult) && !empty($arrResult))
                    {
                        $member->countOnlineEventFeedbackNotifications += 1;
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
            throw new \Exception('Could not find the event the member belongs to');
        }

        if (!$this->eventHasValidFeedbackConfiguration($event)) {
            throw new \Exception($GLOBALS['TL_LANG']['ERR']['invalidEventFeedbackConfiguration']);
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
        $arrTokens['feedback_url'] = sprintf('%s?action=sendEventFeedback&uuid=%s', $page->getAbsoluteUrl(), $member->uuid);

        return $arrTokens;
    }
}
