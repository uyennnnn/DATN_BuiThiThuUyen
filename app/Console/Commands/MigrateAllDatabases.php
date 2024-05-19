<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MigrateAllDatabases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrate for all databases';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $databases = [];
        $sitesFile = base_path('sites.json');

        if (File::exists($sitesFile)) {
            $databases = json_decode(File::get($sitesFile), true);
        }

        foreach ($databases as $database) {
            $this->info("Migrating database: $database");
            config(['database.connections.'.$database => $this->getDatabaseConfig($database)]);
            $this->call('migrate', ['--force' => true, '-n' => true, '--database' => $database]);
        }

        $this->info('All databases migrated successfully.');
    }

    private function getDatabaseConfig($name)
    {
        return [
            'driver' => config('database.default'),
            'host' => config('database.connections.mysql.host'),
            'port' => config('database.connections.mysql.port'),
            'database' => $name,
            'username' => config('database.connections.mysql.username'),
            'password' => config('database.connections.mysql.password'),
        ];
    }
}
