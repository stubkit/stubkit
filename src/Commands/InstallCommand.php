<?php

namespace StubKit\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stubkit:install {--no-overrides}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install StubKit config & stubs';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->comment('Publishing StubKit Configuration...');
        $this->callSilent('vendor:publish', ['--tag' => 'stubkit-config']);
        $this->callSilent('vendor:publish', ['--tag' => 'stubkit-mappings']);
        $this->callSilent('vendor:publish', ['--tag' => 'stubkit-fields']);
        $this->comment('Publishing Laravel Stubs...');
        $this->callSilent('stub:publish');
        $this->comment('Publishing StubKit Stubs...');
        $this->callSilent('vendor:publish', ['--tag' => 'stubkit-stubs']);
        if(!$this->hasOption('no-overrides')) {
            $this->callSilent('vendor:publish', [
                '--tag' => 'stubkit-stub-overrides',
                '--force' => true,
            ]);
        }
        return 1;
    }
}
