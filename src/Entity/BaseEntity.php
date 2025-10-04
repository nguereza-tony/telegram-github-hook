<?php

declare(strict_types=1);

namespace Quantum\Hub\Entity;

use Platine\Stdlib\Helper\Str;

/**
 * @class BaseEntity
 * @package Quantum\Hub\Entity
 */
class BaseEntity
{
    /**
     * Create new instance
     * @param array<string, mixed> $data
     */
    public function __construct(array $data = [])
    {
        $this->hydrate($data);
    }

    /**
     * Fill the property using the provided data
     * @param array<string, mixed> $data
     * @return void
     */
    protected function hydrate(array $data = []): void
    {
        foreach ($data as $key => $value) {
            $keyName = Str::camel($key, true);
            $setterMethod = sprintf('set%s', ucfirst($keyName));
            if (method_exists($this, $setterMethod)) {
                $this->{$setterMethod}($value);
            } elseif (property_exists($this, $keyName)) {
                $this->{$keyName} = $value;
            }
        }
    }
}
