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

namespace Markocupic\SacEventFeedback\Cron;

use Contao\CoreBundle\ServiceAnnotation\CronJob;
use Contao\Date;
use Markocupic\SacEventFeedback\FeedbackReminder\SendFeedbackReminder;

/**
 * @CronJob("minutely")
 */
class ExecuteEventReminderTasks
{
    private SendFeedbackReminder $sendFeedbackReminder;

    public function __construct(SendFeedbackReminder $sendFeedbackReminder)
    {
        $this->sendFeedbackReminder = $sendFeedbackReminder;
    }

    public function __invoke(): void
    {
        $tstampToday = strtotime(Date::parse('Y-m-d'));

        $this->sendFeedbackReminder->sendRemindersByExecutionDate($tstampToday, 20);
    }
}
