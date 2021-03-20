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
    protected $signature = 'make:routes {name} {--type=web}';

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
        $type = $this->option('type');
        $source = base_path("stubs/routes.$type.stub");
        $destination = base_path("routes/$type.php");

        if(!file_exists($source) && in_array($type, ['web', 'api'])) {
            $source = __DIR__ ."/../../stubs/routes.$type.stub";
        }

        abort_if(!file_exists($source), 500, "Missing stub file routes.$type.stub");
        abort_if(!file_exists($destination), 500, "Missing routes file routes/$type.php");

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
