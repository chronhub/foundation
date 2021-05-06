<?php

namespace Chronhub\Foundation\Support\Contracts\Message;

use JsonSerializable;

interface Header extends JsonSerializable
{
    public const EVENT_ID = '__event_id';
    public const EVENT_TYPE = '__event_type';
    public const EVENT_TIME = '__time';

    public const BUS_NAME = '__bus_name';
    public const ASYNC_MARKER = '__async_marker';

    public const AGGREGATE_ID = '__aggregate_id';
    public const AGGREGATE_ID_TYPE = '__aggregate_id_type';
    public const AGGREGATE_TYPE = '__aggregate_type';
    public const AGGREGATE_VERSION = '__aggregate_version';
    public const INTERNAL_POSITION = '__internal_position';
    public const EVENT_CAUSATION_ID = '__event_causation_id';
    public const EVENT_CAUSATION_TYPE = '__event_causation_type';

    public function name(): string;

    /**
     * @return mixed
     */
    public function toValue();
}
