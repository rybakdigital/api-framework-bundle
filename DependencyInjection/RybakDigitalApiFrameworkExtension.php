<?php

namespace RybakDigital\Bundle\ApiFrameworkBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class RybakDigitalApiFrameworkExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        // use the defined config to replace parameters in the twig extension
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if (isset($config['request_formatter']['response']['headers'])) {
            $container->setParameter('rybak_digital_api_framework.request_formatter.response.headers', $config['request_formatter']['response']['headers']);
        } else {
            $container->setParameter('rybak_digital_api_framework.request_formatter.response.headers', array());
        }
    }
}
