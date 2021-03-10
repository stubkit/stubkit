<?php

namespace StubKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use StubKit\Support\Syntax;

class ViewsMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:views {name} {--index} {--create} {--show} {--edit}';

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

        $this->views = config('stubkit.views', [
            'index', 'create', 'show', 'edit',
        ]);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $all = $this->isUsingAllViews();

        foreach ($this->views as $index => $view) {
            if (! $all && ! $this->option($view)) {
                unset($this->views[$index]);
                continue;
            }

            if(! $this->makeView($view)) {
                return 1;
            }
        }

        $this->handleOutput();

        return 0;
    }

    /**
     * Check if any view options were set.
     *
     * @return bool
     */
    public function isUsingAllViews()
    {
        return ! in_array(true, Arr::only($this->options(), $this->views));
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
//        $path = config('stubkit.views.path', 'js/Pages/{{model.studlyPlural}}/{{view.studly}}.vue');
        $path = config('stubkit.view_path');

        $values = [
            'view' => $view,
            'model' => Str::reset($this->argument('name'))
        ];

        $syntax = (new Syntax())->make(
            $values,
            config('stubkit.variables.*', [])
        );

        $path = $syntax->parse($path);

        if (file_exists(base_path("stubs/view.${view}.stub"))) {
            $stub = base_path("stubs/view.${view}.stub");
        } elseif (file_exists(__DIR__."/../../stubs/view.${view}.stub")) {
            $stub = __DIR__."/../../stubs/view.${view}.stub";
        } else {
            $stub = false;
        }

        $path = resource_path($path);

        $content = ($stub) ? file_get_contents($stub) : '';

        $folder = Str::beforeLast($path, DIRECTORY_SEPARATOR);

        if (file_exists($path)) {
            $this->info('Views already exists!');

            return false;
        }

        if(! is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        return file_put_contents($path, $content);
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
