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

        $datimToday = new \DateTimeImmutable(Date::parse('Y-m-d H:i:s', time()));
        $dateEventEnd = new \DateTimeImmutable(Date::parse('Y-m-d', $event->endDate));

        $datimStartReminding = $datimToday;
        // Prevent sending reminders before the event end date is reached
        if ($datimToday->getTimestamp() <= $dateEventEnd->getTimestamp()) {
            $datimStartReminding = $dateEventEnd;
        }

        $datimExpiration = $datimStartReminding->modify('+'.$arrConfig['feedback_expiration_time'].' day');

        foreach ($arrConfig['send_reminder_after_days'] as $intDays) {
            $datimSendReminder = $datimStartReminding->modify('+'.$intDays.' day');

            $set = [
                'pid' => $eventMember->id,
                'uuid' => $eventMember->uuid,
                'executionDate' => $datimSendReminder->getTimestamp(),
                'dateAdded' => time(),
                'tstamp' => time(),
                'expiration' => $datimExpiration->getTimestamp(),
            ];

            // Prevent inserting duplicate records
            // See $GLOBALS['TL_DCA']['tl_event_feedback_reminder']['config']['sql']['keys']['uuid,executionDate'] = 'unique'
            Database::getInstance()
                ->prepare('INSERT INTO tl_event_feedback_reminder %s ON DUPLICATE KEY UPDATE dateAdded=VALUES(dateAdded), tstamp=VALUES(tstamp)')
                ->set($set)
                ->execute()
            ;
        }
    }
}
