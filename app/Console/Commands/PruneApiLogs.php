<?php
namespace App\Console\Commands;
use App\Models\ApiLog;
use Illuminate\Console\Command;

class PruneApiLogs extends Command
{
    protected $signature = 'app:prune-api-logs';
    protected $description = 'Prune old API logs from the database.';

    public function handle()
    {
        $this->info('Pruning old API logs...');
        // Hapus log yang lebih tua dari 30 hari
        $deleted = ApiLog::where('created_at', '<=', now()->subDays(30))->delete();
        $this->info("Done. Deleted {$deleted} old log entries.");
        return 0;
    }
}