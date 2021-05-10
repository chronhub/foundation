<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests;

class OrchestraWithDefaultConfig extends TestCaseWithOrchestra
{
    /**
     * @test
     */
    public function it_assert_default_configuration(): void
    {
        $config = [
            'clock'     => \Chronhub\Foundation\Clock\UniversalSystemClock::class,
            'messaging' => [

                'factory'     => \Chronhub\Foundation\Message\Factory\GenericMessageFactory::class,
                'serializer'  => \Chronhub\Foundation\Message\Serializer\GenericMessageSerializer::class,
                'alias'       => \Chronhub\Foundation\Message\Alias\AliasFromInflector::class,
                'decorators'  => [
                    \Chronhub\Foundation\Message\Decorator\MarkEventId::class,
                    \Chronhub\Foundation\Message\Decorator\MarkEventType::class,
                    \Chronhub\Foundation\Message\Decorator\MarkEventTime::class,
                ],
                'producer' => [
                    'default'     => 'sync',
                    'sync'        => true,
                    'per_message' => [
                        'service' => \Chronhub\Foundation\Message\Producer\PerMessageProducer::class,
                        'queue'   => \Chronhub\Foundation\Message\Producer\IlluminateQueue::class,
                    ],
                    'async'       => [
                        'service' => \Chronhub\Foundation\Message\Producer\AsyncAllMessageProducer::class,
                        'queue'   => \Chronhub\Foundation\Message\Producer\IlluminateQueue::class,
                    ]
                ],
                'subscribers' => [
                    \Chronhub\Foundation\Reporter\Subscribers\MakeMessage::class
                ],
            ],
            'reporting' => [
                'command' => [
                    'default' => [
                        'service_id'     => null,
                        'concrete'       => null,
                        'tracker_id'     => null,
                        'handler_method' => 'command',
                        'messaging'      => [
                            'decorators'  => [],
                            'subscribers' => [
                                \Chronhub\Foundation\Reporter\Subscribers\HandleCommand::class,
                            ],
                            'producer'    => 'default',
                        ],
                        'map'            => []
                    ]
                ],
                'event'   => [
                    'default' => [
                        'service_id'     => null,
                        'concrete'       => null,
                        'tracker_id'     => null,
                        'handler_method' => 'onEvent',
                        'messaging'      => [
                            'decorators'  => [],
                            'subscribers' => [
                                \Chronhub\Foundation\Reporter\Subscribers\HandleEvent::class,
                            ],
                            'producer'    => 'default',
                        ],
                        'map'            => []
                    ]
                ],
                'query'   => [
                    'default' => [
                        'service_id'     => null,
                        'concrete'       => null,
                        'tracker_id'     => null,
                        'handler_method' => 'query',
                        'messaging'      => [
                            'decorators'  => [],
                            'subscribers' => [
                                \Chronhub\Foundation\Reporter\Subscribers\HandleQuery::class,
                            ],
                            'producer'    => 'default',
                        ],
                        'map'            => []
                    ]
                ]
            ],
        ];

        $this->assertEquals($this->app['config']->get('reporter'), $config);
    }
}
