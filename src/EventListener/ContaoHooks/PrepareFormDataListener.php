<?php

declare(strict_types=1);

/*
 * This file is part of SAC Event Feedback Bundle.
 *
 * (c) Marko Cupic 2021 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/sac-event-feedback
 */

namespace Markocupic\SacEventFeedback\EventListener\ContaoHooks;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Form;

/**
 * @Hook(PrepareFormDataListener::TYPE, priority=PrepareFormDataListener::PRIORITY)
 */
class PrepareFormDataListener
{
    public const TYPE = 'prepareFormData';
    public const PRIORITY = 100;

    public function __invoke($arrSubmitted, $arrLabels, $arrFields, Form $form): void
    {
        if ($form->isSacEventFeedbackForm) {
            $form->storeValues = '1';
            $form->targetTable = 'tl_event_feedback';
        }
    }
}
