<?php

namespace Core;

use Core\Traits\Queryable;
use ReflectionClass;
use ReflectionProperty;

abstract class Model
{
    use Queryable;

    public int $id;

    public function toArray(): array
    {
        $data = [];
        $reflect = new ReflectionClass($this);
        $props = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
        $vars = (array) $this;

        foreach($props as $prop) {
            $data[$prop->getName()] = $vars[$prop->getName()] ?? null;
        }

        return $data;
    }
}
