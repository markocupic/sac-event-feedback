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

use Contao\CalendarEventsMemberModel;
use Contao\Database;
use Markocupic\SacEventFeedback\Model\EventFeedbackReminderModel;

class FeedbackReminder
{
    /**
     * @throws \Exception
     */
    public function deleteFeedbackReminderByEventMember(CalendarEventsMemberModel $eventMember): void
    {
        Database::getInstance()
            ->prepare('DELETE FROM tl_event_feedback_reminder WHERE uuid=?')
            ->execute($eventMember->uuid)
        ;
    }

    public function deleteReminder(EventFeedbackReminderModel $objReminder): void
    {
        $objReminder->delete();
    }
}
