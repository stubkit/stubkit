<?php

namespace StubKit\Listeners;

use StubKit\Facades\StubKit;

class TrackModified
{
    public function handle($event)
    {
        if (StubKit::allows($event->command)) {
            return;
        } elseif ($event->command == 'make:scaffold') {
            StubKit::scaffolding(true);
            StubKit::track();
        } elseif (! StubKit::scaffolding()) {
            StubKit::track();
        }
    }
}
