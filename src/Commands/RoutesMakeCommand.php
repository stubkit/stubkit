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
    protected $signature = 'make:routes {name} {--api}';

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
        $path = 'routes/web.php';
        $filename = 'routes.web.stub';

        if ($this->option('api')) {
            $path = 'routes/api.php';
            $filename = 'routes.api.stub';
        }

        $source = base_path("stubs/${filename}");

        if (! file_exists(base_path("stubs/${filename}"))) {
            $source = __DIR__."/../../stubs/${filename}";
        }

        $content = file_get_contents($source);
        $current = file_get_contents(base_path($path));

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

        file_put_contents(base_path($path), $content);

        StubKit::updated(base_path($path));

        $this->info('Routes created successfully.');

        return 0;
    }
}
