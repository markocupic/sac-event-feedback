<?php

declare(strict_types=1);

/*
 * This file is part of SAC Event Evaluation Bundle.
 *
 * (c) Marko Cupic 2021 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/sac-event-evaluatio
 */

namespace Markocupic\SacEventEvaluation\DataContainer;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\Database;
use Contao\DataContainer;

class TlCalendar
{
    /**
     * @var ContaoFramework
     */
    private $framework;

    /**
     * @var array
     */
    private $onlineEvaluationConfigs;

    public function __construct(ContaoFramework $framework, array $onlineEvaluationConfigs)
    {
        $this->framework = $framework;
        $this->onlineEvaluationConfigs = $onlineEvaluationConfigs;
    }

    /**
     * @Callback(table="tl_calendar", target="fields.onlineEvaluationConfiguration.options")
     */
    public function getOnlineEvaluationConfigurations(DataContainer $dc): array
    {
        return array_keys($this->onlineEvaluationConfigs);
    }

    /**
     * @Callback(table="tl_calendar", target="fields.onlineEvaluationNotification.options")
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
     * @Callback(table="tl_calendar", target="fields.onlineEvaluationForm.options")
     */
    public function getOnlineEvaluationForm(DataContainer $dc): array
    {
        $arrOptions = [];

        $databaseAdapter = $this->framework->getAdapter(Database::class);

        $objDb = $databaseAdapter
            ->getInstance()
            ->prepare('SELECT * FROM tl_form WHERE isSacEventEvaluationForm=?')
            ->execute('1')
        ;

        while ($objDb->next()) {
            $arrOptions[$objDb->id] = $objDb->title;
        }

        return $arrOptions;
    }


}
