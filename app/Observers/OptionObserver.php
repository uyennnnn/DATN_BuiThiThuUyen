<?php

namespace App\Observers;

use App\Models\Option;
use Illuminate\Support\Facades\Cache;

class OptionObserver
{
    /**
     * Handle the Option "created" event.
     */
    public function created(Option $option): void
    {
        $this->clearOptionCache();
    }

    /**
     * Handle the Option "updated" event.
     */
    public function updated(Option $option): void
    {
        $this->clearOptionCache();
    }

    /**
     * Handle the Option "deleted" event.
     */
    public function deleted(Option $option): void
    {
        $this->clearOptionCache();
    }

    /**
     * Handle the Option "restored" event.
     */
    public function restored(Option $option): void
    {
        //
    }

    /**
     * Handle the Option "force deleted" event.
     */
    public function forceDeleted(Option $option): void
    {
        //
    }

    protected function clearOptionCache()
    {
        Cache::flush();
    }
}
