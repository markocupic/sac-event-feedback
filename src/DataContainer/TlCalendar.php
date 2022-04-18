<?php

declare(strict_types=1);

/*
 * This file is part of SAC Event Feedback.
 *
 * (c) Marko Cupic 2022 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/sac-event-feedback
 */

namespace Markocupic\SacEventFeedback\DataContainer;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\Database;
use Contao\DataContainer;

class TlCalendar
{
    private ContaoFramework $framework;
    private array $onlineFeedbackConfigs;

    public function __construct(ContaoFramework $framework, array $onlineFeedbackConfigs)
    {
        $this->framework = $framework;
        $this->onlineFeedbackConfigs = $onlineFeedbackConfigs;
    }

    /**
     * @Callback(table="tl_calendar", target="fields.onlineFeedbackConfiguration.options")
     */
    public function getOnlineFeedbackConfigurations(DataContainer $dc): array
    {
        return array_keys($this->onlineFeedbackConfigs);
    }

    /**
     * @Callback(table="tl_calendar", target="fields.onlineFeedbackNotification.options")
     */
    public function getNotifications(DataContainer $dc): array
    {
        $arrOptions = [];

        $databaseAdapter = $this->framework->getAdapter(Database::class);

        $objDb = $databaseAdapter
            ->getInstance()
            ->execute('SELECT * FROM tl_nc_notification')
        ;

        while ($objDb->next()) {
            $arrOptions[$objDb->id] = $objDb->title;
        }

        return $arrOptions;
    }

    /**
     * @Callback(table="tl_calendar", target="fields.onlineFeedbackForm.options")
     */
    public function getOnlineFeedbackForm(DataContainer $dc): array
    {
        $arrOptions = [];

        $databaseAdapter = $this->framework->getAdapter(Database::class);

        $objDb = $databaseAdapter
            ->getInstance()
            ->prepare('SELECT * FROM tl_form WHERE isSacEventFeedbackForm=?')
            ->execute('1')
        ;

        while ($objDb->next()) {
            $arrOptions[$objDb->id] = $objDb->title;
        }

        return $arrOptions;
    }
}
