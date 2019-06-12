<?php

namespace Gocanto\SimplePDF;

use Illuminate\Support\Arr;

class TemplateContext
{
    /** @var array */
    private $attributes;

    /**
     * @param array $attributes
     */
    private function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @param array $attributes
     * @return TemplateContext
     */
    public static function make(array $attributes): TemplateContext
    {
        return new static($attributes);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return (bool) Arr::has($this->attributes, $key);
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }
}
