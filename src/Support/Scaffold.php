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
        $this->maker->callSilent('view:clear');
        $this->maker->callSilent('cache:clear');

        $this->syntax->make($variables, config('stubkit.variables.*', []));

        $progressBar = $this->maker->getOutput()->createProgressBar(count($commands));

        $progressBar->setFormat("[<fg=red>%bar%</>][%step%/%steps%] %command%\n%result%");
        $progressBar->setMessage(count($commands),'steps');
        $progressBar->setMessage('0','step');
        $progressBar->setMessage('Preparing..','command');
        $progressBar->setBarWidth(count($commands));
        $progressBar->setMessage('', 'result');
        $progressBar->start();

        $log = '';

        $divider = str_repeat('-', 50);

        foreach ($commands as $index => $command) {
            $command = $this->syntax->parse($command);
            $output = $this->executeTheCommand($command);
            $log .= "$divider\n> $command\n$divider\n$output\n\n";
            $progressBar->setMessage($index,'step');
            $progressBar->setMessage($command,'command');
            // $progressBar->setMessage($output, 'result');
            $progressBar->advance();
        }

        $progressBar->setMessage(count($commands),'step');
        $progressBar->setMessage($this->syntax->get('scaffold.studly') .' scaffolded.','command');
        $progressBar->finish();

        if(!is_dir(storage_path('logs'))) {
            mkdir(storage_path('logs'), 0777, true);
        }

        file_put_contents(storage_path('logs/scaffold.log'), $log);
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

        return trim($output);
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
            // $this->maker->line($buffer);
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
}
