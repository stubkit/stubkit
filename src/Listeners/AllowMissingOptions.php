<?php

namespace StubKit\Listeners;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use StubKit\Facades\StubKit;
use Symfony\Component\Console\Input\InputOption;

class AllowMissingOptions
{
    /**
     * Add missing options to the command.
     *
     * @param $event
     *
     * @return void
     */
    public function handle($event)
    {
        if (! StubKit::allows($event->command)) {
            return;
        }

        $commands = Artisan::all();

        $definition = $commands[$event->command]->getDefinition();

        $existing = array_keys($definition->getOptions());

        $options = Str::after((string) $event->input, "'{$event->command}' ");

        $options = explode(' ', $options);

        foreach ($options as $option) {
            preg_match('/--([\w_\-]+)/', $option, $matches);

            if (! isset($matches[1])) {
                continue;
            }

            if (! in_array($matches[1], $existing)) {
                $definition->addOption(new InputOption($matches[1], null, 4));
            }
        }
    }
}
