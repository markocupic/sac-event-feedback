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

namespace Markocupic\SacEventEvaluation;

use Markocupic\SacEventEvaluation\DependencyInjection\Compiler\AddSessionBagsPass;
use Markocupic\SacEventEvaluation\DependencyInjection\MarkocupicSacEventEvaluationExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class MarkocupicSacEventEvaluation.
 */
class MarkocupicSacEventEvaluation extends Bundle
{
    public function getContainerExtension(): MarkocupicSacEventEvaluationExtension
    {
        return new MarkocupicSacEventEvaluationExtension();
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
