<?php

namespace StubKit\Commands;

use Illuminate\Console\Command;
use StubKit\Facades\StubKit;
use StubKit\Support\Syntax;

class ScaffoldMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:scaffold {name} {--type=} {--fields=}';

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

        $this->callSilent('view:clear');
        $this->callSilent('cache:clear');

        StubKit::setScaffold($this->argument('name'));
        StubKit::setFields($this->option('fields'));

        $commands = $this->getCommands();

        if (empty($commands)) {
            $this->error('No scaffolds exist!');

            return 1;
        }

        StubKit::call($commands, [
            'scaffold' => $this->argument('name'),
        ], $this);

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
        } else {
            return config('stubkit.scaffolds.default', []);
        }
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
