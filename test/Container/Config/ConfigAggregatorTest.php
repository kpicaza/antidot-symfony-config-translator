<?php

declare(strict_types=1);

namespace Antidot\SymfonyConfigTranslator\Container\Config;

use PHPUnit\Framework\TestCase;
use Zend\ConfigAggregator\ArrayProvider;

class ConfigAggregatorTest extends TestCase
{
    /** @var array */
    private $providers;
    /** @var array */
    private $mergedConfig;

    public function testItShouldCreateMergedConfigFromGivenProviders(): void
    {
        $this->givenAnArrayOfConfigProviders();
        $this->whenConfigIsMerged();
        $this->thenItShouldReturnMergedConfig();
    }

    public function testItShouldCreateMergedConfigFromSymfonyStyleProviders(): void
    {
        $this->givenAnArrayOfSymfonyStyleConfigProviders();
        $this->whenConfigIsMerged();
        $this->thenItShouldReturnMergedConfig();
    }

    private function givenAnArrayOfConfigProviders(): void
    {
        $this->providers = [
            new ArrayProvider($this->factories()),
            new ArrayProvider($this->invokables()),
            new ArrayProvider($this->conditionals()),
            new ArrayProvider($this->aliases())
        ];
    }

    private function givenAnArrayOfSymfonyStyleConfigProviders(): void
    {
        $this->providers = [
            new ArrayProvider($this->symfonyFactories()),
            new ArrayProvider($this->symfonyServices()),
            new ArrayProvider($this->symfonyConditionals()),
            new ArrayProvider($this->symfonyAliases())
        ];
    }

    private function whenConfigIsMerged(): void
    {
        $aggregator = new ConfigAggregator($this->providers);

        $this->mergedConfig = $aggregator->getMergedConfig();
    }

    private function thenItShouldReturnMergedConfig(): void
    {
        $this->assertEquals($this->expectedConfig(), $this->mergedConfig);
    }

    private function factories(): array
    {
        return [
            'dependencies' => [
                'factories' => [
                    'some.service.name' => 'Some\Factory'
                ]
            ],
            'some.config' => [
                'some.key' => 'value1'
            ]
        ];
    }

    private function invokables(): array
    {
        return [
            'dependencies' => [
                'invokables' => [
                    'some.invokable.name' => 'Some\Invokable'
                ]
            ],
            'some.other.config' => [
                'some.key' => 'value1',
                'some.other.key' => ['value2']
            ]
        ];
    }

    private function conditionals(): array
    {
        return [
            'dependencies' => [
                'conditionals' => [
                    'some.class.name' => [
                        'class' => 'Some\Class',
                        'arguments' => [
                            'foo' => 'bar',
                            'baz' => 'doo',
                        ]
                    ]
                ]
            ],
            'some.class.config' => [
                'some.key' => 'value1',
                'some.other.key' => [
                    'value2'
                ]
            ]
        ];
    }

    private function aliases(): array
    {
        return [
            'dependencies' => [
                'aliases' => [
                    'some.alias' => 'Some\Class'
                ]
            ]
        ];
    }

    private function expectedConfig(): array
    {
        return [
            'dependencies' => [
                'conditionals' => [
                    'some.class.name' => [
                        'class' => 'Some\Class',
                        'arguments' => [
                            'foo' => 'bar',
                            'baz' => 'doo',
                        ]
                    ]
                ],
                'factories' => [
                    'some.service.name' => 'Some\Factory'
                ],
                'invokables' => [
                    'some.invokable.name' => 'Some\Invokable'
                ],
                'aliases' => [
                    'some.alias' => 'Some\Class'
                ],
            ],
            'some.class.config' => [
                'some.key' => 'value1',
                'some.other.key' => [
                    'value2'
                ]
            ],
            'some.config' => [
                'some.key' => 'value1'
            ],
            'some.other.config' => [
                'some.key' => 'value1',
                'some.other.key' => ['value2']
            ]
        ];
    }

    private function symfonyFactories(): array
    {
        return [
            'services' => [
                'some.service.name' => [
                    'factory' => 'Some\Factory'
                ]
            ],
            'parameters' => [
                'some.config' => [
                    'some.key' => 'value1'
                ]
            ]
        ];
    }

    private function symfonyServices(): array
    {
        return [
            'services' => [
                'some.invokable.name' => [
                    'class' => 'Some\Invokable'
                ]
            ],
            'parameters' => [
                'some.other.config' => [
                    'some.key' => 'value1',
                    'some.other.key' => ['value2']
                ]
            ]
        ];
    }

    private function symfonyConditionals(): array
    {
        return [
                'services' => [
                    'some.class.name' => [
                    'class' => 'Some\Class',
                    'arguments' => [
                        '$foo' => 'bar',
                        '$baz' => 'doo',
                    ]
                ]
            ],
            'parameters' => [
                'some.class.config' => [
                    'some.key' => 'value1',
                    'some.other.key' => [
                        'value2'
                    ]
                ]
            ]
        ];
    }

    private function symfonyAliases(): array
    {
        return [
            'services' => [
                'some.alias' => [
                    'alias' => 'Some\Class'
                ]
            ]
        ];
    }
}
