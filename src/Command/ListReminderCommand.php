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

namespace Markocupic\SacEventFeedback\Command;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Date;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Markocupic\SacEventFeedback\Model\EventFeedbackReminderModel;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Run "bin/contao-console sac-event-feedback:send-reminder" on the console.
 *
 * to send registered reminders. This ist very useful when developing, and you
 * don't feel like triggering cron jobs.
 */
#[AsCommand(name: 'sac-event-feedback:list-reminder')]
class ListReminderCommand extends Command
{
    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly Connection $connection,
    ) {
        parent::__construct();

        $this->framework->initialize(true);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('SAC Event Feedback/Evaluation: List');

        $reminderIds = $this->connection->fetchFirstColumn(
            // 'SELECT id FROM tl_event_feedback_reminder WHERE expiration > ? AND dispatched
            // = ? FOR UPDATE',
            'SELECT id FROM tl_event_feedback_reminder WHERE expiration > ? AND dispatched = ? LIMIT 0,1000',

            [
                time(),
                0,
            ],
            [
                Types::INTEGER,
                Types::INTEGER,
            ],
        );

        if (empty($reminderIds)) {
            $io->success('No reminders found.');

            return Command::SUCCESS;
        }

        foreach ($reminderIds as $id) {
            $reminderModel = EventFeedbackReminderModel::findById($id);

            if (null === $reminderModel) {
                continue;
            }

            $memberModel = $reminderModel->getRelated('pid');

            if (null === $memberModel) {
                continue;
            }

            $text = \sprintf('Reminder exec-time: %s member: %s [%s]',
                Date::parse('Y-m-d H:i', $reminderModel->executionDate),
                $memberModel->firstname.' '.$memberModel->lastname,
                $memberModel->sacMemberId,
            );

            $io->writeln($text);
        }

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
