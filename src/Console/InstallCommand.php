<?php

namespace Laratrans\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laratrans:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Laratrans package.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->comment('Publishing Laratrans Configuration...');
        $this->callSilent('vendor:publish', ['--tag' => 'laratrans-config']);

        $this->info('Laratrans scaffolding installed successfully.');
    }
}
