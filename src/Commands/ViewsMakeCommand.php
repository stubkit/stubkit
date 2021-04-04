<?php

namespace StubKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use StubKit\Support\Syntax;

class ViewsMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:views {name} {--type=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new set of views';

    /**
     * The views.
     *
     * @var array
     */
    public $views = [];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->views = config('stubkit.views.stubs', []);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->option('type')) {
            $this->views = $this->option('type');
        }

        foreach ($this->views as $view) {
            if (! $this->makeView($view)) {
                return 1;
            }
        }

        $this->handleOutput();

        return 0;
    }

    /**
     * Locate and make the view with the found stub.
     *
     * @param string $view
     *
     * @return bool
     */
    public function makeView(string $view)
    {
        $syntax = (new Syntax())->make(
            ['model' => Str::reset($this->argument('name'))],
            config('stubkit.variables.*', [])
        );

        $syntax->make(
            ['view' => $syntax->parse($view)],
            config('stubkit.variables.*', [])
        );

        $path = $syntax->parse(config('stubkit.views.path'));

        if (file_exists(base_path("stubs/view.${view}.stub"))) {
            $stub = base_path("stubs/view.${view}.stub");
        } elseif (file_exists(__DIR__."/../../stubs/view.${view}.stub")) {
            $stub = __DIR__."/../../stubs/view.${view}.stub";
        } else {
            $stub = false;
        }

        $path = base_path($path);

        $content = ($stub) ? file_get_contents($stub) : '';

        $folder = Str::beforeLast($path, DIRECTORY_SEPARATOR);

        if (file_exists($path)) {
            $this->info('Views already exists!');

            return false;
        }

        if (! is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        return file_put_contents($path, $content) !== false;
    }

    /**
     * Format view count and output.
     *
     * @return void
     */
    public function handleOutput()
    {
        $views = Str::plural('view', count($this->views));

        $this->info(count($this->views)." ${views} created successfully.");
    }
}
