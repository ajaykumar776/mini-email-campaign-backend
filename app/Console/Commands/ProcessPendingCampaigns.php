<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Campaign;
use App\Jobs\SendCampaignEmail;

class ProcessPendingCampaigns extends Command
{
    protected $signature = 'campaigns:process';
    protected $description = 'Process pending campaigns in chunks';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $chunkSize = 100; // Adjust this size based on your requirements

        Campaign::where('status', 'pending')
            ->orderBy('created_at')
            ->chunk($chunkSize, function ($campaigns) {
                foreach ($campaigns as $campaign) {
                    dispatch(new SendCampaignEmail($campaign, $campaign->user_id));
                    $this->info('Campaign ID ' . $campaign->id . ' dispatched for processing.');
                }
            });
    }
}
