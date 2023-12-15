<?php

namespace Core;

use Core\Traits\Queryable;

abstract class Model
{
    use Queryable;

    public int $id;
}
