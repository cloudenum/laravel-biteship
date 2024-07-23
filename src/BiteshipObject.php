<?php

namespace Cloudenum\Biteship;

/** @phpstan-consistent-constructor */
abstract class BiteshipObject implements \ArrayAccess, \JsonSerializable, \Stringable
{
    protected array $attributes = [];

    protected array $dynamicProperties = [];

    protected static array $traitInitializers = [];

    protected static array $booted = [];

    public function __construct(array $attributes = [])
    {
        $this->boot();
        $this->fillDynamicProperties($attributes);
    }

    private function boot(): void
    {
        // boot class if not booted yet
        if (!in_array(static::class, static::$booted)) {
            $this->bootTraits();
            $this->initializeTraits();
            static::$booted[] = static::class;
        }
    }

    /**
     * Boot the traits.
     */
    private function bootTraits(): void
    {
        $class = static::class;

        $booted = [];

        static::$traitInitializers[$class] = [];

        foreach (class_uses_recursive($class) as $trait) {
            $method = 'boot' . class_basename($trait);

            if (method_exists($class, $method) && !in_array($method, $booted)) {
                forward_static_call([$class, $method]);

                $booted[] = $method;
            }

            if (method_exists($class, $method = 'initialize' . class_basename($trait))) {
                static::$traitInitializers[$class][] = $method;

                static::$traitInitializers[$class] = array_unique(
                    static::$traitInitializers[$class]
                );
            }
        }
    }

    /**
     * Initialize the traits.
     */
    protected function initializeTraits(): void
    {
        foreach (static::$traitInitializers[static::class] as $method) {
            $this->{$method}();
        }
    }

    /**
     * Get an attribute from the object.
     */
    public function getAttribute(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }

    /**
     * Set an attribute on the object.
     */
    public function setAttribute(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Fill the dynamic properties of the object.
     */
    public function fillDynamicProperties(array $attributes): static
    {
        $properties = array_intersect_key($attributes, array_flip($this->dynamicProperties));

        foreach ($properties as $key => $value) {
            if ($this->isDynamicProperty($key)) {
                $this->setAttribute($key, $value);
            }
        }

        return $this;
    }

    /**
     * Determine if the given key is a dynamic property.
     *
     * @param  mixed  $key
     */
    public function isDynamicProperty($key): bool
    {
        return in_array($key, $this->dynamicProperties);
    }

    public function offsetExists($key): bool
    {
        return !is_null($this->getAttribute($key));
    }

    public function offsetGet($key): mixed
    {
        return $this->getAttribute($key);
    }

    public function offsetSet($key, $value): void
    {
        $this->setAttribute($key, $value);
    }

    public function offsetUnset($key): void
    {
        unset($this->attributes[$key]);
    }

    public function __get($key): mixed
    {
        if ($this->isDynamicProperty($key)) {
            return $this->getAttribute($key);
        }

        throw new \InvalidArgumentException("Property [{$key}] does not exist or inaccessible on this object.");
    }

    public function __set($key, $value): void
    {
        if ($this->isDynamicProperty($key)) {
            $this->setAttribute($key, $value);

            return;
        }

        throw new \InvalidArgumentException("Property [{$key}] does not exist or inaccessible on this object.");
    }

    public function __isset($key): bool
    {
        return $this->offsetExists($key);
    }

    public function __unset($key): void
    {
        $this->offsetUnset($key);
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function __toString(): string
    {
        return json_encode($this->jsonSerialize());
    }

    public function __sleep(): array
    {
        return array_keys(get_object_vars($this));
    }

    public function __wakeup(): void
    {
        $this->bootTraits();
        $this->initializeTraits();
    }
}
