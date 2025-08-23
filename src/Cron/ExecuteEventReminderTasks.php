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

namespace Markocupic\SacEventFeedback\Cron;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCronJob;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Date;
use Doctrine\DBAL\Exception;
use Markocupic\SacEventFeedback\FeedbackReminder\SendFeedbackReminder;
use Symfony\Component\Filesystem\Filesystem;

#[AsCronJob('minutely')]
class ExecuteEventReminderTasks
{
    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly SendFeedbackReminder $sendFeedbackReminder,
        private readonly string $projectDir,
    ) {
    }

    /**
     * @throws Exception
     */
    public function __invoke(): void
    {
        // Initialize the Contao framework
        $this->framework->initialize();

        $log = [];
        $now = time();
        $this->sendFeedbackReminder->sendRemindersByExecutionDate($now, 20, $log);

        if (!empty($log)) {
            $fs = new Filesystem();

            foreach ($log as $message) {
                $fs->appendToFile($this->projectDir.'/sac-event-feedback.log', "\r\n");
                $fs->appendToFile($this->projectDir.'/sac-event-feedback.log', '================='.Date::parse('Y-m-d H:i:s').'===================');
                $fs->appendToFile($this->projectDir.'/sac-event-feedback.log', "\r\n");
                $fs->appendToFile($this->projectDir.'/sac-event-feedback.log', json_encode(mb_convert_encoding((string) $message, 'UTF-8', 'UTF-8')));
            }
        }
    }
}
