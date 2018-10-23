<?php

namespace Statamic\Addons\Buck\Commands;

use Statamic\API\File;
use Statamic\API\Path;
use Statamic\Extend\Command;
use Illuminate\Support\Facades\Artisan;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'buck:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $migration = date('Y_m_d_His') . '_create_buck_tables.php';
        // copy the migration(s)
        File::copy(
            Path::assemble(addons_path($this->getAddonClassName()), 'resources', 'migrations', 'create_buck_tables.php'),
                   Path::assemble(site_path('database'), 'migrations', $migration),
                   true
        );

        // load the migration
        app('composer')->dumpAutoloads();

        // run the migration(s)
        Artisan::call('migrate');
    }
}
