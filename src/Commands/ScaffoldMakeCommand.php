<?php

namespace StubKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use StubKit\Facades\StubKit;
use StubKit\Shortcuts\Nested;
use StubKit\Shortcuts\Pivot;
use StubKit\Support\Syntax;

class ScaffoldMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:scaffold {name} {--type=} {--fields=} {--pivot=} {--nested=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new scaffold';

    /**
     * StubKit Syntax Instance.
     *
     * @var Syntax
     */
    public $syntax;

    /**
     * StubKit Shortcut Instance.
     *
     * @var mixed
     */
    public $shortcut;

    /**
     * Construct the command.
     */
    public function __construct()
    {
        parent::__construct();

        $this->syntax = new Syntax();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->asciiHeader();

        $this->shortcut = $this->getShortcut();

        if (! is_null(optional($this->shortcut)->error)) {
            $this->error($this->shortcut->error);

            return 1;
        }

        $fields = is_null($this->shortcut)
            ? $this->option('fields')
            : $this->shortcut->fields;

        $scaffold = $this->argument('name');

        StubKit::setScaffold($scaffold);
        StubKit::setShortcut($this->shortcut);
        StubKit::setFields($fields);

        $commands = $this->getCommands();

        if (empty($commands)) {
            $this->error('No scaffolds exist!');

            return 1;
        }

        $values = [
            'scaffold' => $scaffold,
        ];

        if ($this->shortcut) {
            $values = array_merge($values, $this->shortcut->values);
        }

        StubKit::call($commands, $values, $this);

        return 0;
    }

    /**
     * Get the commands from proper config.
     *
     * @return array
     */
    public function getCommands()
    {
        if ($type = $this->option('type')) {
            return config("stubkit.scaffolds.{$type}", []);
        } elseif ($type = optional($this->shortcut)->type) {
            return config("stubkit.scaffolds.${type}", []);
        } else {
            return config('stubkit.scaffolds.default', []);
        }
    }

    public function getShortcut()
    {
        $shortcut = Arr::first(array_keys(array_filter([
            Pivot::class => $this->option('pivot'),
            Nested::class => $this->option('nested'),
        ])));

        if (! $shortcut) {
            return null;
        }

        $shortcut = app($shortcut);

        $shortcut->settings(
            $this->argument('name')
        );

        $shortcut->make(
            $this->option($shortcut->type),
            $this->option('fields')
        );

        return $shortcut;
    }

    public function asciiHeader()
    {
        $this->line('                                    ');
        $this->line('                __  __      _     _ ');
        $this->line('               / _|/ _|    | |   | |');
        $this->line(' ___  ___ __ _| |_| |_ ___ | | __| |');
        $this->line('/ __|/ __/ _` |  _|  _/ _ \| |/ _` |');
        $this->line('\__ \ (_| (_| | | | || (_) | | (_| |');
        $this->line('|___/\___\__,_|_| |_| \___/|_|\__,_|');
        $this->line('                                    ');
    }
}
