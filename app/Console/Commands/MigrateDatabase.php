<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MigrateDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:subdomain';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command migrate database for subdomain';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $database = request()->getHost();
            config(['database.connections.'.$database => $this->getDatabaseConfig($database)]);
            $this->call('migrate', ['--force' => true, '-n' => true, '--database' => $database]);
            // Log::info($database.' migrated successfully.');

            return true;
        } catch (\Exception $e) {
            // Log::error($database.' migration failed: '.$e->getMessage());

            return false;
        }
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
