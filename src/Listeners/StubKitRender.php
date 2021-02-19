<?php

namespace StubKit\Listeners;

use Illuminate\Support\Str;
use StubKit\Facades\StubKit;

class StubKitRender
{
    public function handle($event)
    {
        if (! StubKit::allows($event->command)) {
            return;
        }

        if (StubKit::scaffolding() && $event->command != 'make:scaffold') {
            return;
        }

        $this->prepareSyntax($event);

        StubKit::render();
    }

    /**
     * Convert arguments and options into syntax.
     *
     * @param \Illuminate\Console\Events\CommandFinished $event
     *
     * @return void
     */
    public function prepareSyntax($event)
    {
        $values = array_filter($event->input->getOptions());

        if (StubKit::scaffolding()) {
            $values['scaffold'] = StubKit::scaffold();

            if (! isset($values['model'])) {
                $values['model'] = Str::reset($values['scaffold'])->singular()->studly();
            }

            if (! isset($values['fields']) && ! is_null(StubKit::fields())) {
                $values['fields'] = StubKit::fields();
            }
        } elseif (in_array($event->command, ['make:views', 'make:routes'])) {
            if (! isset($values['model'])) {
                $values['model'] = $event->input->getArgument('name');
            }
        }

        StubKit::syntax($values);
    }
}
