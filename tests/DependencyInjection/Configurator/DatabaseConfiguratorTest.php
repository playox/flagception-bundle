<?php

namespace Flagception\Tests\FlagceptionBundle\DependencyInjection\Configurator;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Flagception\Bundle\FlagceptionBundle\DependencyInjection\Configurator\DatabaseConfigurator;
use Flagception\Bundle\FlagceptionBundle\DependencyInjection\FlagceptionExtension;
use Flagception\Database\Activator\DatabaseActivator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Test for database configurator
 *
 * @author Michel Chowanski <chowanski@bestit-online.de>
 * @package Flagception\Tests\FlagceptionBundle\DependencyInjection\Configurator
 */
class DatabaseConfiguratorTest extends TestCase
{
    /**
     * The container
     *
     * @var ContainerBuilder
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $container = new ContainerBuilder();
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../../../src/Resources/config'));
        $loader->load('configurators.yml');

        $this->container = $container;
    }

    /**
     * Test key
     *
     * @return void
     */
    public function testKey()
    {
        static::assertEquals('database', (new DatabaseConfigurator())->getKey());
    }

    /**
     * Test activator default state
     *
     * @return void
     */
    public function testActivatorDefaultState()
    {
        $config = [];
        $extension = new FlagceptionExtension();
        $extension->load($config, $this->container);

        static::assertFalse($this->container->hasDefinition('flagception.activator.database_activator'));
    }

    /**
     * Test activator default state
     *
     * @return void
     */
    public function testActivatorDefaultPriority()
    {
        $config = [
            [
                'activators' => [
                    'database' => [
                        'enable' => true,
                        'url' => 'mysql://foo'
                    ]
                ]
            ]
        ];
        $extension = new FlagceptionExtension();
        $extension->load($config, $this->container);

        $definition = $this->container->getDefinition('flagception.activator.database_activator');
        static::assertEquals(220, $definition->getTag('flagception.activator')[0]['priority']);
    }

    /**
     * Test activator can be enabled
     *
     * @return void
     */
    public function testActivatorCanByEnabled()
    {
        $config = [
            [
                'activators' => [
                    'database' => [
                        'enable' => true,
                        'url' => 'pdo-sqlite://:memory:'
                    ]
                ]
            ]
        ];
        $extension = new FlagceptionExtension();
        $extension->load($config, $this->container);

        static::assertTrue($this->container->hasDefinition('flagception.activator.database_activator'));

        /** @var DatabaseActivator $activator */
        $activator = $this->container->get('flagception.activator.database_activator');

        static::assertInstanceOf(Connection::class, $activator->getConnection());
    }

    /**
     * Test activator can be enabled by string
     *
     * @return void
     */
    public function testActivatorCanByEnabledByString()
    {
        $config = [
            [
                'activators' => [
                    'database' => [
                        'enable' => 'true',
                        'url' => 'pdo-sqlite://:memory:'
                    ]
                ]
            ]
        ];
        $extension = new FlagceptionExtension();
        $extension->load($config, $this->container);

        static::assertTrue($this->container->hasDefinition('flagception.activator.database_activator'));

        /** @var DatabaseActivator $activator */
        $activator = $this->container->get('flagception.activator.database_activator');

        static::assertInstanceOf(Connection::class, $activator->getConnection());
    }

    /**
     * Test set activator priority
     *
     * @return void
     */
    public function testActivatorSetPriority()
    {
        $config = [
            [
                'activators' => [
                    'database' => [
                        'enable' => true,
                        'priority' => 10,
                        'url' => 'pdo-sqlite://:memory:'
                    ]
                ]
            ]
        ];
        $extension = new FlagceptionExtension();
        $extension->load($config, $this->container);

        $definition = $this->container->getDefinition('flagception.activator.database_activator');
        static::assertEquals(10, $definition->getTag('flagception.activator')[0]['priority']);

        /** @var DatabaseActivator $activator */
        $activator = $this->container->get('flagception.activator.database_activator');

        static::assertInstanceOf(Connection::class, $activator->getConnection());
    }

    /**
     * Test set activator by url
     *
     * @return void
     */
    public function testActivatorByUrl()
    {
        $config = [
            [
                'activators' => [
                    'database' => [
                        'enable' => true,
                        'url' => 'pdo-sqlite://:memory:'
                    ]
                ]
            ]
        ];
        $extension = new FlagceptionExtension();
        $extension->load($config, $this->container);

        static::assertTrue($this->container->hasDefinition('flagception.activator.database_activator'));

        /** @var DatabaseActivator $activator */
        $activator = $this->container->get('flagception.activator.database_activator');

        static::assertInstanceOf(Connection::class, $activator->getConnection());
    }

    /**
     * Test set activator by dbal
     *
     * @return void
     */
    public function testActivatorByDbal()
    {
        $this->container->set('my.dbal.service', DriverManager::getConnection([
            'dbname' => 'mydb',
            'user' => 'user',
            'password' => 'secret',
            'host' => 'localhost',
            'driver' => 'pdo_mysql'
        ]));

        $config = [
            [
                'activators' => [
                    'database' => [
                        'enable' => true,
                        'dbal' => 'my.dbal.service'
                    ]
                ]
            ]
        ];

        $extension = new FlagceptionExtension();
        $extension->load($config, $this->container);

        static::assertTrue($this->container->hasDefinition('flagception.activator.database_activator'));

        /** @var DatabaseActivator $activator */
        $activator = $this->container->get('flagception.activator.database_activator');

        static::assertInstanceOf(Connection::class, $activator->getConnection());
    }

    /**
     * Test set activator by credentials
     *
     * @return void
     */
    public function testActivatorByCredentials()
    {
        $config = [
            [
                'activators' => [
                    'database' => [
                        'enable' => true,
                        'credentials' => [
                            'dbname' => 'mydb',
                            'user' => 'user',
                            'password' => 'secret',
                            'host' => 'localhost',
                            'driver' => 'pdo_mysql'
                        ]
                    ]
                ]
            ]
        ];
        $extension = new FlagceptionExtension();
        $extension->load($config, $this->container);

        static::assertTrue($this->container->hasDefinition('flagception.activator.database_activator'));

        /** @var DatabaseActivator $activator */
        $activator = $this->container->get('flagception.activator.database_activator');

        static::assertInstanceOf(Connection::class, $activator->getConnection());
    }

    /**
     * Test set activator by invalid credentials
     *
     * @return void
     */
    public function testActivatorByInvalidCredentials()
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = [
            [
                'activators' => [
                    'database' => [
                        'enable' => true,
                        'credentials' => [
                            'dbname' => 'mydb',
                            'driver' => 'pdo_mysql'
                        ]
                    ]
                ]
            ]
        ];
        $extension = new FlagceptionExtension();
        $extension->load($config, $this->container);

        $this->container->hasDefinition('flagception.activator.database_activator');

        /** @var DatabaseActivator $activator */
        $activator = $this->container->get('flagception.activator.database_activator');

        static::assertInstanceOf(Connection::class, $activator->getConnection());
    }

    /**
     * Test set activator with missing connection field
     *
     * @return void
     */
    public function testActivatorByMissingConnectionField()
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = [
            [
                'activators' => [
                    'database' => [
                        'enable' => true
                    ]
                ]
            ]
        ];
        $extension = new FlagceptionExtension();
        $extension->load($config, $this->container);

        $this->container->hasDefinition('flagception.activator.database_activator');
    }

    /**
     * Test set activator cache is disabled by default
     *
     * @return void
     */
    public function testActivatorCacheIsDisabled()
    {
        $config = [
            [
                'activators' => [
                    'database' => [
                        'enable' => true,
                        'url' => 'pdo-sqlite://:memory:'
                    ]
                ]
            ]
        ];
        $extension = new FlagceptionExtension();
        $extension->load($config, $this->container);

        static::assertFalse($this->container->hasDefinition('flagception.activator.database_activator.cache'));
    }

    /**
     * Test set activator with cache
     *
     * @return void
     */
    public function testActivatorWithCache()
    {
        $config = [
            [
                'activators' => [
                    'database' => [
                        'enable' => true,
                        'url' => 'pdo-sqlite://:memory:',
                        'cache' => [
                            'enable' => true
                        ]
                    ]
                ]
            ]
        ];
        $extension = new FlagceptionExtension();
        $extension->load($config, $this->container);

        static::assertTrue($this->container->hasDefinition('flagception.activator.database_activator.cache'));

        $definition = $this->container->getDefinition('flagception.activator.database_activator.cache');
        static::assertEquals(
            'flagception.activator.database_activator',
            $definition->getDecoratedService()[0]
        );

        /** @var DatabaseActivator $activator */
        $activator = $this->container->get('flagception.activator.database_activator');

        static::assertInstanceOf(Connection::class, $activator->getConnection());
    }

    /**
     * Test set activator with cache by string
     *
     * @return void
     */
    public function testActivatorWithCacheByString()
    {
        $config = [
            [
                'activators' => [
                    'database' => [
                        'enable' => true,
                        'url' => 'pdo-sqlite://:memory:',
                        'cache' => [
                            'enable' => 'true'
                        ]
                    ]
                ]
            ]
        ];
        $extension = new FlagceptionExtension();
        $extension->load($config, $this->container);

        static::assertTrue($this->container->hasDefinition('flagception.activator.database_activator.cache'));

        /** @var DatabaseActivator $activator */
        $activator = $this->container->get('flagception.activator.database_activator');

        static::assertInstanceOf(Connection::class, $activator->getConnection());
    }
}
