<?php

namespace App\Console\Commands;

use App\Services\MachineryExpiryService;
use Illuminate\Console\Command;

class CheckMachineryExpiriesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'machinery:check-expiries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan machinery documents for upcoming or past expirations and generate idempotent compliance alerts';

    /**
     * Execute the console command.
     */
    public function handle(MachineryExpiryService $service): int
    {
        $this->info('Starting machinery document compliance expiry scan...');
        $result = $service->checkExpiries();

        $this->line(" - Documents scanned: {$result['scanned']}");
        $this->line(" - New alerts generated: {$result['alerts_created']}");
        $this->line(" - Already alerted today: {$result['already_alerted_today']}");
        $this->info($result['message']);

        return self::SUCCESS;
    }
}
