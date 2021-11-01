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

namespace Markocupic\SacEventEvaluation\ContaoManager;

use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Routing\RoutingPluginInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Contao\CoreBundle\ContaoCoreBundle;
use Markocupic\SacEventToolBundle\MarkocupicSacEventToolBundle;
use Markocupic\SacEventEvaluation\MarkocupicSacEventEvaluation;

/**
 * Class Plugin
 */
class Plugin implements BundlePluginInterface, RoutingPluginInterface
{
    /**
     * @return array
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(MarkocupicSacEventEvaluation::class)
                ->setLoadAfter([
                    ContaoCoreBundle::class,
                    MarkocupicSacEventToolBundle::class
                ]),
        ];
    }

    /**
     * @return null|\Symfony\Component\Routing\RouteCollection
     * @throws \Exception
     */
    public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel)
    {
        return $resolver
            ->resolve(__DIR__ . '/../Resources/config/routes.yml')
            ->load(__DIR__ . '/../Resources/config/routes.yml');
    }
}
