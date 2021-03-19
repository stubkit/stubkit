<?php

namespace StubKit\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use StubKit\Facades\StubKit;
use StubKit\Support\Syntax;
use Symfony\Component\Process\Process;

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

        $this->syntax->make([
            'scaffold' => $this->argument('name'),
        ], config('stubkit.variables.*', []));

        $commands = $this->getCommands();

        if (empty($commands)) {
            $this->error('No scaffolds exist!');

            return 1;
        }

        foreach ($commands as $command) {
            $this->executeTheCommand($command);
        }

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

    /**
     * Perform the console process.
     *
     * @param string $command
     *
     * @return string
     */
    public function executeTheCommand(string $command)
    {
        $command = $this->syntax->parse($command);

        $output = '';

        try {
            if ($this->needsNewProcess($command)) {
                $this->heading($command);
                $this->handleCommandWithNewProcess($command);
            } else {
                $this->heading("php artisan ${command}");
                $process = app(Kernel::class);
                $process->call($command);
                $output = $process->output();
            }
        } catch (Exception $e) {
            $output = $e->getMessage();
        }

        $this->info(trim($output));

        return $output;
    }

    /**
     * @param $command
     * @return bool
     */
    public function needsNewProcess($command)
    {
        $commands = array_keys(Artisan::all());

        $command = Str::contains($command, ' ')
            ? explode(' ', $command, 2)[0]
            : $command;

        return ! in_array($command, $commands) || $command == 'test';
    }

    /**
     * Run tests in user's console to avoid bug.
     *
     * @param string $command
     *
     * @return void
     */
    public function handleCommandWithNewProcess(string $command)
    {
        if (Str::startsWith($command, 'test')) {
            $command = "php artisan ${command}";
        }

        $process = Process::fromShellCommandline($command);

        $process->run(function ($type, $buffer) {
            $this->line($buffer);
        });
    }

    /**
     * Make migration exception uniform with other commands.
     *
     * @param string $output
     *
     * @return string
     */
    public function handleTableExists(string $output)
    {
        if (Str::contains($output, 'class already exists.')) {
            $output = "Table already exists!\n";
        }

        return $output;
    }

    /**
     * Make a bulky heading for the console output.
     *
     * @param string $phrase
     *
     * @return void
     */
    public function heading(string $phrase)
    {
        $this->comment('----------------------------------------------------------');
        $this->comment("| ${phrase}");
        $this->comment('----------------------------------------------------------');
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
