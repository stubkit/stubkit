<?php

namespace StubKit\Support;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class Scaffold
{
    protected $maker;

    protected $syntax;

    protected $variables;

    /**
     * Scaffold constructor.
     * @param Command $maker
     */
    public function __construct(Command $maker)
    {
        $this->maker = $maker;

        $this->syntax = new Syntax();
    }

    public function call($commands, $variables = [])
    {
        $this->syntax->make($variables, config('stubkit.variables.*', []));

        foreach ($commands as $command) {
            $this->executeTheCommand($command);
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

        $this->maker->info(trim($output));

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
            $this->maker->line($buffer);
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
        $this->maker->comment('----------------------------------------------------------');
        $this->maker->comment("| ${phrase}");
        $this->maker->comment('----------------------------------------------------------');
    }
}
