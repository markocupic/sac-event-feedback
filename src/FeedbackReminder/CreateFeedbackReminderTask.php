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

namespace Markocupic\SacEventFeedback\FeedbackReminder;

use Contao\CalendarEventsMemberModel;
use Contao\CalendarEventsModel;
use Contao\Database;
use Contao\Date;
use Markocupic\SacEventFeedback\EventFeedbackHelper;

class CreateFeedbackReminderTask
{
    private EventFeedbackHelper $eventFeedbackHelper;

    public function __construct(EventFeedbackHelper $eventFeedbackHelper)
    {
        $this->eventFeedbackHelper = $eventFeedbackHelper;
    }

    /**
     * @throws \Exception
     */
    public function create(CalendarEventsMemberModel $eventMember): void
    {
        if (null === ($event = CalendarEventsModel::findByPk($eventMember->eventId))) {
            return;
        }

        $arrConfig = $this->eventFeedbackHelper->getOnlineFeedbackConfiguration($event);

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

            $set = [
                'pid' => $eventMember->id,
                'uuid' => $eventMember->uuid,
                'executionDate' => $dateSendReminder->getTimestamp(),
                'dateAdded' => time(),
                'tstamp' => time(),
                'feedbackExpirationDate' => $dateExpiration->getTimestamp(),
            ];

            // Prevent inserting duplicate records
            // See $GLOBALS['TL_DCA']['tl_event_feedback_reminder']['config']['sql']['keys']['uuid,executionDate'] = 'unique'
            Database::getInstance()
                ->prepare(
                    'INSERT INTO tl_event_feedback_reminder %s '.
                    'ON DUPLICATE KEY UPDATE '.
                    'dateAdded=VALUES(dateAdded), tstamp=VALUES(tstamp)'
                )
                ->set($set)
                ->execute()
            ;
        }
    }
}
