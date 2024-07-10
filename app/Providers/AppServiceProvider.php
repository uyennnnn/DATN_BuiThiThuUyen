<?php

namespace App\Providers;

use App\Models\Option;
use App\Observers\OptionObserver;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $domain = env('DOMAIN', request()->getHost());
        config(['database.connections.mysql.database' => $domain]);
        config(['filesystems.disks.public.root' => storage_path('app/public/').$domain]);

        DB::listen(function ($query) {
            // Log::debug(
            //     $query->sql,
            //     [
            //         'bindings' => $query->bindings,
            //         'time' => $query->time,
            //     ]
            // );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Option::observe(OptionObserver::class);
    }
}
