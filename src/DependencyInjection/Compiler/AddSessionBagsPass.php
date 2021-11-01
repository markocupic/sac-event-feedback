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

namespace Markocupic\SacEventEvaluation\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @internal
 */
class AddSessionBagsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('session')) {
            return;
        }

        $session = $container->findDefinition('session');
        $session->addMethodCall('registerBag', [new Reference('Markocupic\SacEventEvaluation\Session\Attribute\ArrayAttributeBag')]);
    }
}
