<?php

namespace StubKit\Providers;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use StubKit\Listeners\AllowMissingOptions;
use StubKit\Listeners\StubKitRender;
use StubKit\Listeners\TrackModified;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register the listeners.
     */
    protected $listen = [
        CommandStarting::class => [
            TrackModified::class,
            AllowMissingOptions::class,
        ],
        CommandFinished::class => [
            StubKitRender::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
