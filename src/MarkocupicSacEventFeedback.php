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

namespace Markocupic\SacEventFeedback;

use Markocupic\SacEventFeedback\DependencyInjection\Compiler\AddSessionBagsPass;
use Markocupic\SacEventFeedback\DependencyInjection\MarkocupicSacEventFeedbackExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class MarkocupicSacEventFeedback.
 */
class MarkocupicSacEventFeedback extends Bundle
{
    public function getContainerExtension(): MarkocupicSacEventFeedbackExtension
    {
        return new MarkocupicSacEventFeedbackExtension();
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new AddSessionBagsPass());
    }
}
