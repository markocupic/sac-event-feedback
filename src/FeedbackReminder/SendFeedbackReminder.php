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

namespace Markocupic\SacEventFeedback\FeedbackReminder;

use Markocupic\SacEventToolBundle\Model\CalendarEventsMemberModel;
use Contao\CalendarEventsModel;
use Contao\Database;
use Contao\PageModel;
use Markocupic\SacEventFeedback\EventFeedbackHelper;
use Markocupic\SacEventFeedback\Model\EventFeedbackReminderModel;
use Markocupic\SacEventToolBundle\CalendarEventsHelper;
use ReallySimpleJWT\Token;

class SendFeedbackReminder
{
    private EventFeedbackHelper $eventFeedbackHelper;
    private FeedbackReminder $feedbackReminder;
    private array $onlineFeedbackConfigs;
    private string $secret;

    public function __construct(EventFeedbackHelper $eventFeedbackHelper, FeedbackReminder $feedbackReminder, array $onlineFeedbackConfigs, string $secret)
    {
        $this->eventFeedbackHelper = $eventFeedbackHelper;
        $this->feedbackReminder = $feedbackReminder;
        $this->onlineFeedbackConfigs = $onlineFeedbackConfigs;
        $this->secret = $secret;
    }

    public function sendReminder(EventFeedbackReminderModel $objReminder): void
    {
        /** @var PageModel $objPage */
        global $objPage;

        if (null !== ($objMember = CalendarEventsMemberModel::findOneByUuid($objReminder->uuid))) {
            $event = CalendarEventsModel::findByPk($objMember->eventId);

            if (null !== $event) {
                if (!$this->eventFeedbackHelper->eventHasValidFeedbackConfiguration($event)) {
                    throw new \Exception($GLOBALS['TL_LANG']['ERR']['sacEvFb']['invalidEventFeedbackConfiguration']);
                }
                $notification = $this->eventFeedbackHelper->getNotification($event);
                $arrTokens = $this->getNotificationTokens($objMember, $objReminder);

                $arrResult = $notification->send($arrTokens, $objPage->language);

                if (!empty($arrResult) && \is_array($arrResult)) {
                    ++$objMember->countOnlineEventFeedbackNotifications;
                    $objMember->save();
                }
            }
        }

        // Delete reminder
        $this->feedbackReminder->deleteReminder($objReminder);
    }

    public function sendRemindersByExecutionDate($tstamp, $number = 20): void
    {
        // Delete no more used records
        Database::getInstance()
            ->prepare('DELETE FROM tl_event_feedback_reminder WHERE expiration < ?')
            ->execute($tstamp)
        ;

        $objReminder = Database::getInstance()
            ->prepare('SELECT * FROM tl_event_feedback_reminder WHERE executionDate < ?')
            ->limit($number)
            ->execute($tstamp - $this->onlineFeedbackConfigs['send_reminder_execution_delay'])
        ;

        while ($objReminder->next()) {
            $reminderModel = EventFeedbackReminderModel::findByPk($objReminder->id);
            $this->sendReminder($reminderModel);
        }
    }

    private function getNotificationTokens(CalendarEventsMemberModel $member, EventFeedbackReminderModel $reminder): array
    {
        if (null === ($event = CalendarEventsModel::findByPk($member->eventId))) {
            throw new \Exception('Could not find the event the member belongs to.');
        }

        if (!$this->eventFeedbackHelper->eventHasValidFeedbackConfiguration($event)) {
            throw new \Exception($GLOBALS['TL_LANG']['ERR']['sacEvFb']['invalidEventFeedbackConfiguration']);
        }

        $page = $this->eventFeedbackHelper->getPage($event);
        $token = $this->generateJwt($member, $reminder);

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
        $arrTokens['feedback_url'] = sprintf('%s?token=%s', $page->getAbsoluteUrl(), $token);

        return $arrTokens;
    }

    private function generateJwt(CalendarEventsMemberModel $member, EventFeedbackReminderModel $reminder)
    {
        $userId = (int) $member->id;
        $expiration = (int) $reminder->expiration;
        $issuer = 'localhost';

        return Token::create($userId, $this->secret, $expiration, $issuer);
    }
}
