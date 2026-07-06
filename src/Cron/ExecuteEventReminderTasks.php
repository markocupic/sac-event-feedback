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
use Contao\StringUtil;
use Contao\Validator;
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

        $logs = [];
        $now = time();
        $this->sendFeedbackReminder->sendRemindersByExecutionDate($now, 20, $logs);

        $fs = new Filesystem();

        if (!empty($logs)) {
            foreach ($logs as $message) {
                $fs->appendToFile($this->projectDir.'/sac-event-feedback.log', "\r\n");
                $fs->appendToFile($this->projectDir.'/sac-event-feedback.log', '================='.Date::parse('Y-m-d H:i:s').'===================');
                $fs->appendToFile($this->projectDir.'/sac-event-feedback.log', "\r\n");
                $fs->appendToFile($this->projectDir.'/sac-event-feedback.log', self::jsonEncodeWithBinaryUuids($message));
            }
        }
    }

    private static function jsonEncodeWithBinaryUuids(array $data): string
    {
        array_walk_recursive(
            $data,
            static function (&$value): void {
                // Nur echte Binärstrings anfassen (kein gültiges UTF-8)
                if (\is_string($value) && !mb_check_encoding($value, 'UTF-8')) {
                    $value = Validator::isBinaryUuid($value)
                        ? StringUtil::binToUuid($value) // 16-Byte-UUID -> "550e8400-e29b-41d4-a716-446655440000"
                        : bin2hex($value); // sonstiges Binär -> Hex
                }
            },
        );

        return json_encode($data, JSON_THROW_ON_ERROR);
    }
}
