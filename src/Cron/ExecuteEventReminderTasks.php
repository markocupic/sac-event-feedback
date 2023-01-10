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

namespace Markocupic\SacEventFeedback\Cron;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCronJob;
use Contao\CoreBundle\Framework\ContaoFramework;
use Markocupic\SacEventFeedback\FeedbackReminder\SendFeedbackReminder;

#[AsCronJob('minutely')]
class ExecuteEventReminderTasks
{
    private ContaoFramework $framework;
    private SendFeedbackReminder $sendFeedbackReminder;

    public function __construct(ContaoFramework $framework, SendFeedbackReminder $sendFeedbackReminder)
    {
        $this->framework = $framework;
        $this->sendFeedbackReminder = $sendFeedbackReminder;
    }

    public function __invoke(): void
    {
        // Initialize the Contao framework
        $this->framework->initialize(true);

        $tstampToday = time();

        $this->sendFeedbackReminder->sendRemindersByExecutionDate($tstampToday, 20);
    }
}
