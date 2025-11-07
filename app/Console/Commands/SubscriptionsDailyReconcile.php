<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SubscriptionScheduler;

class SubscriptionsDailyReconcile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:daily-reconcile';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start/complete freezes and adjust subscription states daily';

    /**
     * Execute the console command.
     */
    public function handle(SubscriptionScheduler $scheduler): int
    {
        $scheduler->dailyReconcile();
        $this->info('Subscriptions reconciled.');
        return self::SUCCESS;
    }
}
