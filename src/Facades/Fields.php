<?php

namespace StubKit\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \StubKit\Support\Fields
 */
class Fields extends Facade
{
    /**
     * Get the facade accessor.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \StubKit\Support\Fields::class;
    }
}
