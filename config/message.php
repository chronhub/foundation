<?php

return [
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

        'producer' => [
            'default'     => 'sync',
            'per_message' => [
                'queue' => '\Chronhub\Foundation\Message\Producer\IlluminateProducer::class',
            ],
            'async_all'   => [
                'queue' => '\Chronhub\Foundation\Message\Producer\IlluminateProducer::class',
            ]
        ],

        'subscribers' => [
            \Chronhub\Foundation\Reporter\Subscribers\MakeMessage::class
        ],
    ],
];
