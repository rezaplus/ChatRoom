<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Message;
use Carbon\Carbon;

class ArchiveOldMessages extends Command
{
    protected $signature = 'messages:archive-old';

    protected $description = 'Archive messages older than 30 days';

    public function handle()
    {
        // Define the cutoff date
        $cutoffDate = Carbon::now()->subDays(30);

        // Find messages older than 30 days
        $messages = Message::where('created_at', '<', $cutoffDate)->get();

        // Archive the messages (you can define what archiving means, e.g., moving to an archive table)
        foreach ($messages as $message) {
            $message->archive();
        }

        $this->info("Archived messages older than 30 days.");
    }
}
