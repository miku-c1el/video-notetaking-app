<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\CachedSearchResult;

class ClearExpiredCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-expired-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired cache entries from the query cache table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $deleted = CachedSearchResult::where('expires_at', '<', Carbon::now('Asia/Tokyo'))
        ->delete();

        $this->info("Deleted {$deleted} expired cache entries.");
    }
}
