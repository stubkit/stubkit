<?php

namespace StubKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use StubKit\Facades\StubKit;
use StubKit\Support\Syntax;

class RoutesMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:routes {name} {--type=web} {--stub=} {--to=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new set of routes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $to = $this->option('type');
        $stub = $this->option('type');

        if($this->option('to')) {
            $to = $this->option('to');
        }

        if($this->option('stub')) {
            $stub = $this->option('stub');
        }

        $source = base_path("stubs/routes.{$stub}.stub");
        $destination = base_path("routes/{$to}.php");

        if (! file_exists($source) && in_array($stub, ['web', 'api'])) {
            $source = __DIR__."/../../stubs/routes.{$stub}.stub";
        }

        abort_if(! file_exists($source), 500, "Missing stub file routes.{$stub}.stub");
        abort_if(! file_exists($destination), 500, "Missing routes file routes/{$to}.php");

        $content = file_get_contents($source);
        $current = file_get_contents($destination);

        $syntax = (new Syntax())->make(
            ['model' => $this->argument('name')],
            config('stubkit.variables.*', [])
        );

        $content = $syntax->parse($content);

        foreach (explode("\n", $content) as $line) {
            if (Str::contains($current, $line)) {
                $this->info('Routes already exists!');

                return 1;
            }
        }

        $content = rtrim($current)."\n\n${content}";

        file_put_contents($destination, $content);

        StubKit::updated($destination);

        $this->info('Routes created successfully.');

        return 0;
    }
}
