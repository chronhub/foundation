<?php

declare(strict_types=1);

namespace Chronhub\Foundation\Tests;

abstract class OrchestraWithDefaultConfig extends TestCaseWithOrchestra
{
    protected function defineEnvironment($app): void
    {
        $app['config']->set('reporter', [
            'clock' => \Chronhub\Foundation\Clock\UniversalSystemClock::class,

            'messaging' => [
                'factory'    => \Chronhub\Foundation\Message\Factory\GenericMessageFactory::class,
                'serializer' => \Chronhub\Foundation\Message\Serializer\GenericMessageSerializer::class,
                'alias'      => \Chronhub\Foundation\Message\Alias\AliasFromInflector::class,
                'decorators' => [
                    \Chronhub\Foundation\Message\Decorator\MarkEventId::class,
                    \Chronhub\Foundation\Message\Decorator\MarkEventType::class,
                    \Chronhub\Foundation\Message\Decorator\MarkEventTime::class,
                ],

                // only default and sync can not be unset
                'producer' => [
                    'default'     => 'sync',
                    'sync'        => true,
                    'per_message' => [
                        'service' => \Chronhub\Foundation\Message\Producer\PerMessageProducer::class,
                        'queue'   => \Chronhub\Foundation\Message\Producer\IlluminateQueue::class,
                    ],
                    'async'       => [
                        // your registered service id (queue would not be used)
                        // or the provided one
                        'service' => \Chronhub\Foundation\Message\Producer\AsyncAllMessageProducer::class,

                        // default illuminate queue / nullable
                        // or service id
                        // or array['connection' => 'my_con , 'queue' => 'my_queue' ]
                        'queue'   => \Chronhub\Foundation\Message\Producer\IlluminateQueue::class,
                    ],
                ],

                'subscribers' => [
                    \Chronhub\Foundation\Reporter\Subscribers\MakeMessage::class,
                ],
            ],

            'reporting' => [
                /*
                 * Reporter command
                 */
                'command' => [
                    'default' => [
                        'service_id'     => null,
                        'concrete'       => null,
                        'tracker_id'     => null,
                        'handler_method' => 'command',
                        'messaging'      => [
                            'decorators'  => [
                                //\Chronhub\Foundation\Message\Decorator\AsyncMarkerMessageDecorator::class,
                            ],
                            'subscribers' => [
                                //\Chronhub\Foundation\Reporter\Subscribers\LogDomainCommand::class,
                                \Chronhub\Foundation\Reporter\Subscribers\HandleCommand::class,
                            ],
                            'producer'    => 'default',
                        ],
                        'map'            => [],
                    ],
                ],

                /*
                 * Reporter event
                 */
                'event'   => [
                    'default' => [
                        'service_id'     => null,
                        'concrete'       => null,
                        'tracker_id'     => null,
                        'handler_method' => 'onEvent',
                        'messaging'      => [
                            'decorators'  => [
                                //async dec if validation or async bus
                            ],
                            'subscribers' => [
                                \Chronhub\Foundation\Reporter\Subscribers\HandleEvent::class,
                            ],
                            'producer'    => 'default',
                        ],
                        'map'            => [],
                    ],
                ],

                /*
                 * Reporter query
                 */
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
                        'map'            => [],
                    ],
                ],
            ],
        ]);
    }
}
