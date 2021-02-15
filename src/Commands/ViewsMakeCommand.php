<?php

namespace StubKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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
        $folder = $this->getFolder();

        if (is_dir(resource_path("views/${folder}"))) {
            $this->info('Views already exists!');

            return 1;
        }

        $all = $this->isUsingAllViews();

        mkdir(resource_path("views/${folder}"));

        foreach ($this->views as $index => $view) {
            if (! $all && ! $this->option($view)) {
                unset($this->views[$index]);
                continue;
            }

            $this->makeView($folder, $view);
        }

        $this->handleOutput();

        return 0;
    }

    /**
     * Make a folder name based on argument.
     *
     * @return string
     */
    public function getFolder()
    {
        return Str::reset($this->argument('name'))->plural()->slug();
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
     * @param string $folder
     * @param string $view
     *
     * @return void
     */
    public function makeView(string $folder, string $view)
    {
        if (file_exists(base_path("stubs/view.${view}.stub"))) {
            $stub = base_path("stubs/view.${view}.stub");
        } elseif (file_exists(__DIR__."/../../stubs/view.${view}.stub")) {
            $stub = __DIR__."/../../stubs/view.${view}.stub";
        } else {
            $stub = false;
        }

        $content = ($stub)
            ? file_get_contents($stub)
            : '';

        file_put_contents(
            resource_path("views/${folder}/${view}.blade.php"),
            $content
        );
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
