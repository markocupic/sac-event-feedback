<?php

/*
 * This file is part of SAC Event Evaluation Bundle.
 * 
 * (c) Marko Cupic 2021 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/sac-event-evaluation
 */
declare(strict_types=1);

namespace Markocupic\SacEventEvaluation\Tests\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\DelegatingParser;
use Contao\TestCase\ContaoTestCase;
use Markocupic\SacEventEvaluation\ContaoManager\Plugin;
use Markocupic\SacEventEvaluation\MarkocupicSacEventEvaluation;
use Markocupic\SacEventToolBundle\MarkocupicSacEventToolBundle;

/**
 * Class PluginTest
 *
 * @package Markocupic\SacEventEvaluation\Tests\ContaoManager
 */
class PluginTest extends ContaoTestCase
{
    /**
     * Test Contao manager plugin class instantiation
     */
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(Plugin::class, new Plugin());
    }

    /**
     * Test returns the bundles
     */
    public function testGetBundles(): void
    {
        $plugin = new Plugin();

        /** @var array $bundles */
        $bundles = $plugin->getBundles(new DelegatingParser());

        $this->assertCount(2, $bundles);
        $this->assertInstanceOf(BundleConfig::class, $bundles[0]);
        $this->assertSame(MarkocupicSacEventEvaluation::class, $bundles[0]->getName());
        $this->assertSame([ContaoCoreBundle::class, MarkocupicSacEventToolBundle::class], $bundles[0]->getLoadAfter());
    }

}
