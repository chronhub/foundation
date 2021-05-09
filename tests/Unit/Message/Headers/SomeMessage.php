<?php
declare(strict_types=1);

namespace Chronhub\Foundation\Tests\Unit\Message\Headers;

use Chronhub\Foundation\Message\Headers\HeadersTrait;
use Chronhub\Foundation\Support\Contracts\Message\Header;

class SomeMessage
{
    use HeadersTrait;

    public function setEventTypeAttribute($eventType): void
    {
        $this->attributes[Header::EVENT_TYPE] = $eventType;
    }

    public function setEventIdAttribute($eventId): void
    {
        $this->attributes[Header::EVENT_ID] = $eventId;
    }

    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    public function offsetExists($offset)
    {
        return ! is_null($this->getAttribute($offset));
    }

    public function offsetGet($offset)
    {
        return $this->getAttribute($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->setAttribute($offset, $value);
    }

    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset], $this->relations[$offset]);
    }

    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    public function __unset($key)
    {
        $this->offsetUnset($key);
    }

    public function toArray()
    {
        return array_merge($this->attributesToArray());
    }

    public function toJson($options = 0)
    {
        $json = json_encode($this->jsonSerialize(), $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \RuntimeException("error json");
        }

        return $json;
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Handle dynamic static method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }

    /**
     * Convert the model to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Prepare the object for serialization.
     *
     * @return array
     */
    public function __sleep()
    {
        $this->mergeAttributesFromClassCasts();

        $this->classCastCache = [];

        return array_keys(get_object_vars($this));
    }
}
