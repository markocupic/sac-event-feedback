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

namespace Markocupic\SacEventFeedback\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class MarkocupicSacEventFeedbackExtension.
 */
class MarkocupicSacEventFeedbackExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return Configuration::ROOT_KEY;
    }

    /**
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('parameters.yml');
        $loader->load('services.yml');

        $rootKey = $this->getAlias();

        $container->setParameter($rootKey.'.delete_feedbacks_after', $config['delete_feedbacks_after']);
        $container->setParameter($rootKey.'.secret', $config['secret']);
        $container->setParameter($rootKey.'.docx_template', $config['docx_template']);
        $container->setParameter($rootKey.'.cloudconvert_api_key', $config['cloudconvert_api_key']);
        $container->setParameter($rootKey.'.configs', $config['configs']);
    }
}
