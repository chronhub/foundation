<?php

declare(strict_types=1);

return [
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
];
