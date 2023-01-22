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

namespace Markocupic\SacEventFeedback\FeedbackReminder;

use Contao\CalendarEventsModel;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\PageModel;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Markocupic\SacEventFeedback\EventFeedbackHelper;
use Markocupic\SacEventFeedback\Model\EventFeedbackReminderModel;
use Markocupic\SacEventToolBundle\CalendarEventsHelper;
use Markocupic\SacEventToolBundle\Model\CalendarEventsMemberModel;
use Psr\Log\LoggerInterface;
use ReallySimpleJWT\Token;

class SendFeedbackReminder
{
    public function __construct(
        private readonly Connection $connection,
        private readonly EventFeedbackHelper $eventFeedbackHelper,
        private readonly FeedbackReminder $feedbackReminder,
        private readonly array $feedbackConfig,
        private readonly string $secret,
        private readonly LoggerInterface|null $contaoGeneralLogger = null,
    ) {
    }

    public function sendReminder(EventFeedbackReminderModel $objReminder): void
    {
        /** @var PageModel $objPage */
        global $objPage;

        if (null !== ($objRegistration = CalendarEventsMemberModel::findOneByUuid($objReminder->uuid))) {
            $event = CalendarEventsModel::findByPk($objRegistration->eventId);

            if (null !== $event) {
                if (!$this->eventFeedbackHelper->eventHasValidFeedbackConfiguration($event)) {
                    throw new \Exception($GLOBALS['TL_LANG']['ERR']['sacEvFb']['invalidEventFeedbackConfiguration']);
                }
                $notification = $this->eventFeedbackHelper->getNotification($event);
                $arrTokens = $this->getNotificationTokens($objRegistration, $objReminder);

                $arrResult = $notification->send($arrTokens, $objPage->language);

                if (!empty($arrResult) && \is_array($arrResult)) {
                    ++$objRegistration->countOnlineEventFeedbackNotifications;
                    $objRegistration->save();

                    if ($this->contaoGeneralLogger) {
                        $message = sprintf(
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
     * @throws Exception
     */
    public function sendRemindersByExecutionDate(int $tstamp, int $limit = 20): void
    {
        try {
            $this->connection->beginTransaction();

            // Delete already dispatched or expired records.
            $this->connection->executeStatement(
                'DELETE FROM tl_event_feedback_reminder WHERE expiration < ? OR (dispatchTime > ? AND dispatchTime < ?)',
                [
                    $tstamp,
                    0,
                    time() - 60,
                ],
            );

            // Queue competing queries/requests on table "tl_event_feedback_reminder" with "FOR UPDATE" until the transaction is completed.
            // This should prevent competing queries and double emailing
            $result = $this->connection->executeQuery(
                sprintf('SELECT id FROM tl_event_feedback_reminder WHERE expiration > ? AND executionDate < ? AND dispatched = ? LIMIT 0,%d FOR UPDATE', $limit),
                [
                    time(),
                    $tstamp - $this->feedbackConfig['send_reminder_execution_delay'],
                    '',
                ]
            );

            $arrIds = $result->fetchFirstColumn();

            if (!empty($arrIds)) {
                foreach ($arrIds as $id) {
                    $reminderModel = EventFeedbackReminderModel::findByPk($id);

                    if (null !== $reminderModel) {
                        $set = [
                            'dispatched' => '1',
                            'dispatchTime' => time(),
                        ];

                        $this->connection->update('tl_event_feedback_reminder', $set, ['id' => $id]);

                        // Send notification
                        $this->sendReminder($reminderModel);
                    }
                }
            }

            $this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollBack();
        }
    }

    /**
     * @throws \Exception
     */
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

    private function generateJwt(CalendarEventsMemberModel $member, EventFeedbackReminderModel $reminder): string
    {
        $userId = (int) $member->id;
        $expiration = (int) $reminder->expiration;
        $issuer = 'localhost';

        return Token::create($userId, $this->secret, $expiration, $issuer);
    }
}
