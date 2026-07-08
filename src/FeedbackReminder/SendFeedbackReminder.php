<?php

declare(strict_types=1);

/*
 * This file is part of SAC Event Feedback.
 *
 * (c) Marko Cupic <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/sac-event-feedback
 */

namespace Markocupic\SacEventFeedback\FeedbackReminder;

use Contao\CalendarEventsModel;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Markocupic\SacEventFeedback\EventFeedbackHelper;
use Markocupic\SacEventFeedback\Model\EventFeedbackReminderModel;
use Markocupic\SacEventToolBundle\Model\CalendarEventsMemberModel;
use Markocupic\SacEventToolBundle\Util\CalendarEventsUtil;
use Psr\Log\LoggerInterface;
use ReallySimpleJWT\Token;
use Symfony\Component\Lock\LockFactory;
use Terminal42\NotificationCenterBundle\NotificationCenter;

readonly class SendFeedbackReminder
{
    public function __construct(
        private CalendarEventsUtil $calendarEventsUtil,
        private Connection $connection,
        private EventFeedbackHelper $eventFeedbackHelper,
        private FeedbackReminder $feedbackReminder,
        private NotificationCenter $notificationCenter,
        private LockFactory $lockFactory,
        private array $feedbackConfig,
        private string $secret,
        private LoggerInterface|null $contaoGeneralLogger = null,
        private LoggerInterface|null $contaoErrorLogger = null,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function sendReminder(EventFeedbackReminderModel $objReminder): void
    {
        if (null !== ($objRegistration = CalendarEventsMemberModel::findOneByUuid($objReminder->uuid))) {
            $event = CalendarEventsModel::findById($objRegistration->eventId);

            if (null !== $event) {
                if (true !== ($errorCode = $this->eventFeedbackHelper->eventHasValidFeedbackConfiguration($event))) {
                    $this->writeErrorToContaoLog($errorCode, $event, $objReminder);

                    return;
                }

                // The notification has already been checked for existence. See
                // EventFeedbackHelper::eventHasValidFeedbackConfiguration()
                $notificationId = $this->eventFeedbackHelper->getNotificationId($event);

                $arrTokens = $this->getNotificationTokens($objRegistration, $event, $objReminder);

                $receiptCollection = $this->notificationCenter->sendNotification($notificationId, $arrTokens);

                if ($receiptCollection->count()) {
                    ++$objRegistration->countOnlineEventFeedbackNotifications;
                    $objRegistration->save();

                    if ($this->contaoGeneralLogger) {
                        $message = \sprintf(
                            'An event feedback reminder for event "%s" ID %d has been sent to frontend user "%s %s" (event registration ID %d).',
                            $event->title,
                            $event->id,
                            $objRegistration->firstname,
                            $objRegistration->lastname,
                            $objRegistration->id,
                        );

                        $this->contaoGeneralLogger->info(
                            $message,
                            ['contao' => new ContaoContext(__METHOD__, 'SEND_EVENT_FEEDBACK_REMINDER')],
                        );
                    }
                }
            }
        }

        // Delete reminder
        $this->feedbackReminder->deleteReminder($objReminder);
    }

    /**
     * This method performs the following operations:
     * 1. Deletes expired or invalid reminder records from the database.
     * 2. Fetches the list of pending reminders that need to be sent.
     * 3. Sends a limited number of reminders based on the defined execution date and ensures no more than the specified limit (`$limit`) records are processed.
     * 4. Updates the database to mark reminders as dispatched after they are sent.
     * 5. All operations are enclosed in a database transaction to ensure atomicity.
     * *
     */
    public function sendRemindersByExecutionDate(int $tstamp, int $limit = 20, array &$logs = []): void
    {
        $lock = $this->lockFactory->createLock(self::class);
        $lock->acquire(true);

        $this->connection->beginTransaction();

        try {
            // Delete already dispatched or expired records.
            $this->connection->executeStatement(
                'DELETE FROM tl_event_feedback_reminder WHERE expiration < ? OR (dispatchTime > ? AND dispatchTime < ?)',
                [
                    $tstamp,
                    0,
                    $tstamp - 60,
                ],
                [
                    Types::INTEGER,
                    Types::INTEGER,
                    Types::INTEGER,
                ],
            );

            // Queue competing queries/requests on table "tl_event_feedback_reminder" with
            // "FOR UPDATE" until the transaction is completed. This should prevent competing
            // queries and double emailing
            $reminderIds = $this->connection->fetchFirstColumn(
                // 'SELECT id FROM tl_event_feedback_reminder WHERE expiration > ? AND dispatched
                // = ? FOR UPDATE',
                'SELECT id FROM tl_event_feedback_reminder WHERE expiration > ? AND dispatched = ? LIMIT 0,1000',
                [
                    $tstamp,
                    0,
                ],
                [
                    Types::INTEGER,
                    Types::INTEGER,
                ],
            );

            if (!empty($reminderIds)) {
                $count = 0;

                foreach ($reminderIds as $id) {
                    if ($count >= $limit) {
                        break;
                    }

                    $reminderModel = EventFeedbackReminderModel::findById($id);

                    if (null === $reminderModel) {
                        continue;
                    }

                    $memberModel = $reminderModel->getRelated('pid');

                    if (null === $memberModel) {
                        continue;
                    }

                    $configuration = $this->getConfiguration($reminderModel);

                    $delay = 0;

                    if (null !== $configuration) {
                        $delay = $configuration['send_reminder_execution_delay'] ?? 0;
                    }

                    if ($reminderModel->executionDate > $tstamp - $delay) {
                        continue;
                    }

                    $reminderModel->dispatched = 1;
                    $reminderModel->dispatchTime = time();
                    $reminderModel->save();

                    // Send the notification
                    $this->sendReminder($reminderModel);

                    $logs[] = [
                        'tl_event_feedback_reminder' => $reminderModel->row(),
                        'tl_calendar_events_member' => $memberModel->row(),
                    ];

                    ++$count;
                }
            }

            $this->connection->commit();
        } catch (\Throwable $e) {
            $this->connection->rollBack();

            $this->contaoErrorLogger?->error((string) $e);
        } finally {
            $lock->release();
        }
    }

    private function getConfiguration(EventFeedbackReminderModel $eventFeedbackReminderModel): array|null
    {
        try {
            $registration = $eventFeedbackReminderModel->getRelated('pid');

            if (null === $registration) {
                return null;
            }

            $event = $registration->getRelated('eventId');

            if (null === $event) {
                return null;
            }

            $calendar = $event->getRelated('pid');

            if (null === $calendar) {
                return null;
            }

            return $this->feedbackConfig[$calendar->onlineFeedbackConfiguration];
        } catch (\Throwable $e) {
            $this->contaoErrorLogger?->error((string) $e);

            return null;
        }
    }

    /**
     * @throws \Exception
     */
    private function getNotificationTokens(CalendarEventsMemberModel $member, CalendarEventsModel $event, EventFeedbackReminderModel $reminder): array
    {
        $page = $this->eventFeedbackHelper->getPage($event);
        $token = $this->generateJwt($member, $reminder);

        $objInstructor = $this->calendarEventsUtil->getMainInstructor($event);
        $arrTokens = [];
        $arrTokens['instructor_name'] = $this->calendarEventsUtil->getMainInstructorName($event);
        $arrTokens['instructor_email'] = $objInstructor ? $objInstructor->email : '';
        $arrTokens['admin_email'] = $GLOBALS['TL_ADMIN_EMAIL'];
        $arrTokens['participant_firstname'] = $member->firstname;
        $arrTokens['participant_lastname'] = $member->lastname;
        $arrTokens['participant_email'] = $member->email;
        $arrTokens['participant_uuid'] = $member->uuid;
        $arrTokens['event_name'] = StringUtil::revertInputEncoding($event->title);
        $arrTokens['feedback_url'] = \sprintf('%s?token=%s', $page->getAbsoluteUrl(), $token);

        return $arrTokens;
    }

    private function writeErrorToContaoLog(string $errorCode, CalendarEventsModel $event, EventFeedbackReminderModel $objReminder): void
    {
        $errorMsg = \sprintf(
            'Could not send event feedback reminder due to misconfiguration. Error code: "%s". Event ID: "%d". Reminder-UUID: "%s".',
            $errorCode,
            $event->id,
            $objReminder->uuid,
        );

        $this->contaoErrorLogger->error($errorMsg);
    }

    private function generateJwt(CalendarEventsMemberModel $member, EventFeedbackReminderModel $reminder): string
    {
        $userId = $member->id;
        $expiration = (int) $reminder->expiration;
        $issuer = 'localhost';

        return Token::create($userId, $this->secret, $expiration, $issuer);
    }
}
