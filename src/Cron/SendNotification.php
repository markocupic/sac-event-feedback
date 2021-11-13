<?php

declare(strict_types=1);

/*
 * This file is part of SAC Event Evaluation Bundle.
 *
 * (c) Marko Cupic 2021 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/sac-event-evaluation
 */

namespace Markocupic\SacEventEvaluation\Cron;

    use Contao\CoreBundle\ServiceAnnotation\CronJob;
    use Contao\Date;
    use Contao\File;

    /**
     * @CronJob("minutely")
     */
    class SendNotification
    {
        public function __invoke(): void
        {
            //$objFile = new File('files/cron.txt');
            //$objFile->append('executed at '.Date::parse('H:i'));
            //$objFile->close();
        }
    }
