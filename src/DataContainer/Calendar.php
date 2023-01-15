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

namespace Markocupic\SacEventFeedback\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class Calendar
{
    private array $bundleConfig;
    private Connection $connection;

    public function __construct(Connection $connection, array $bundleConfig)
    {
        $this->connection = $connection;
        $this->bundleConfig = $bundleConfig;
    }

    #[AsCallback(table: 'tl_calendar', target: 'fields.onlineFeedbackConfiguration.options')]
    public function getOnlineFeedbackConfigurations(DataContainer $dc): array
    {
        return array_keys($this->bundleConfig);
    }

    /**
     * @throws Exception
     */
    #[AsCallback(table: 'tl_calendar', target: 'fields.onlineFeedbackNotification.options')]
    public function getNotifications(DataContainer $dc): array
    {
        return $this->connection->fetchAllKeyValue('SELECT id, title FROM tl_nc_notification');
    }

    /**
     * @throws Exception
     */
    #[AsCallback(table: 'tl_calendar', target: 'fields.onlineFeedbackForm.options')]
    public function getOnlineFeedbackForm(DataContainer $dc): array
    {
        $result = $this->connection->executeQuery('SELECT id, title FROM tl_form WHERE isSacEventFeedbackForm = ?', ['1']);

        return $result->fetchAllKeyValue();
    }
}
