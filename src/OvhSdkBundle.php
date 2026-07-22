<?php

declare(strict_types=1);

namespace ChuckBartowski\OvhSdk;

use ChuckBartowski\OvhSdk\Client\OvhClient;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

final class OvhSdkBundle extends AbstractBundle
{
    protected string $extensionAlias = 'ovh_sdk';

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->scalarNode('application_key')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('application_secret')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('consumer_key')->defaultValue('')->end()
                ->scalarNode('endpoint')->defaultValue('ovh-eu')->end()
                ->floatNode('timeout')->defaultValue(30.0)->end()
                ->booleanNode('retry_failed')->defaultFalse()->end()
                ->integerNode('max_retries')->defaultValue(3)->end()
            ->end();
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $services = $container->services();

        $services->set(OvhClient::class)
            ->args([
                $config['application_key'],
                $config['application_secret'],
                '' !== $config['consumer_key'] ? $config['consumer_key'] : null,
                $config['endpoint'],
                $config['timeout'],
                $config['retry_failed'],
                $config['max_retries'],
                service('http_client')->nullOnInvalid(),
            ]);

        $services->set(Ovh::class)
            ->args([service(OvhClient::class)])
            ->public();

        $services->alias('ovh_sdk.ovh', Ovh::class)->public();
    }
}
