<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AutoTraderService;
use Illuminate\Support\Facades\Log;

class SyncAutoTraderVehicles extends Command
{
    protected $signature = 'cars:sync-autotrader';
    protected $description = 'Sync vehicle list from AutoTrader and store it locally';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(AutoTraderService $autoTraderService)
    {
        Log::info('Starting AutoTrader sync job...');

        $result = $autoTraderService->getVehicleList();

        if (isset($result['error'])) {
            Log::error('AutoTrader sync failed: ' . $result['error']);
            $this->error('Sync failed: ' . $result['error']);
        } else {
            Log::info('AutoTrader sync completed. Total vehicles synced: ' . count($result['cars'] ?? []));
            $this->info('Vehicle list synced successfully.');
        }

        return Command::SUCCESS;
    }
}
