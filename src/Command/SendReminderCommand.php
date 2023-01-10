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

namespace Markocupic\SacEventFeedback\Command;

use Contao\CoreBundle\Framework\ContaoFramework;
use Markocupic\SacEventFeedback\FeedbackReminder\SendFeedbackReminder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Run "bin/contao-console sac-event-feedback:send-reminder" on the console.
 *
 * to send registered reminders. This ist very useful when developing,
 * and you don't feel like triggering cron jobs.
 */
#[AsCommand(name: 'sac-event-feedback:send-reminder')]
class SendReminderCommand extends Command
{
    private ContaoFramework $framework;
    private SendFeedbackReminder $sendFeedbackReminder;

    public function __construct(ContaoFramework $framework, SendFeedbackReminder $sendFeedbackReminder)
    {
        parent::__construct();

        $this->framework = $framework;
        $this->sendFeedbackReminder = $sendFeedbackReminder;

        $this->framework->initialize(true);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            '',
            'SAC Event Feedback/Evaluation',
            '=============================',
            '',
            'A maximum of 20 reminders have just been sent.',
            '',
        ]);

        $this->sendFeedbackReminder->sendRemindersByExecutionDate(time(), 20);

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this
            // the command help shown when running the command with the "--help" option
            ->setHelp('This command allows you to send reminders.')
        ;
    }
}
