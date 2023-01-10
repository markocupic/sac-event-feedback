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
use Contao\Database;
use Contao\Date;

#[AsCronJob('weekly')]
class DeleteOldFeedbacks
{
    private ContaoFramework $framework;
    private string $deleteFeedbacksAfter;

    public function __construct(ContaoFramework $framework, string $deleteFeedbacksAfter)
    {
        $this->framework = $framework;
        $this->deleteFeedbacksAfter = $deleteFeedbacksAfter;
    }

    public function __invoke(): void
    {
        // Initialize the Contao framework
        $this->framework->initialize(true);

        $datimToday = new \DateTimeImmutable(Date::parse('Y-m-d'));
        $datimExpired = $datimToday->modify('-'.$this->deleteFeedbacksAfter.' day');

        // Delete old feedbacks
        Database::getInstance()
            ->prepare('DELETE FROM tl_event_feedback WHERE dateAdded < ?')
            ->execute($datimExpired->getTimestamp())
        ;

        // Delete old reminders
        Database::getInstance()
            ->prepare('DELETE FROM tl_event_feedback_reminder WHERE dateAdded < ?')
            ->execute($datimExpired->getTimestamp())
        ;
    }
}
