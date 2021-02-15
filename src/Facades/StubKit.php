<?php

namespace StubKit\Facades;

use Illuminate\Support\Facades\Facade;

class StubKit extends Facade
{
    /**
     * Get the facade accessor.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \StubKit\StubKit::class;
    }
}
