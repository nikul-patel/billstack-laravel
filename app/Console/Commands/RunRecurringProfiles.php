<?php

namespace App\Console\Commands;

use App\Models\RecurringProfile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class RunRecurringProfiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:run-recurring-profiles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate invoices for recurring profiles whose run date is due.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = now()->startOfDay();

        $dueProfiles = RecurringProfile::with(['business', 'items'])
            ->whereNotNull('next_run_date')
            ->whereDate('next_run_date', '<=', $now)
            ->get();

        if ($dueProfiles->isEmpty()) {
            $this->info('No recurring profiles are due right now.');

            return self::SUCCESS;
        }

        $processed = 0;

        foreach ($dueProfiles as $profile) {
            if ($profile->items->isEmpty()) {
                $this->warn("Profile #{$profile->id} skipped because it has no default items.");

                continue;
            }
            try {
                $profile->generateInvoice();
                $profile->advanceToNextRun();
                $processed++;
            } catch (Throwable $e) {
                Log::error('Failed to process recurring profile', [
                    'profile_id' => $profile->id,
                    'message' => $e->getMessage(),
                ]);
                $this->error("Profile #{$profile->id} failed: {$e->getMessage()}");
            }
        }

        $this->info("Processed {$processed} recurring profiles.");

        return self::SUCCESS;
    }
}
