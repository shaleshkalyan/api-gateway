<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RecordClickJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $urlId,
        public ?string $ip,
        public ?string $userAgent
    ) {}

    public function handle(): void
    {
        DB::table('url_monitoring')->insert([
            'url_id'     => $this->urlId,
            'ip_address' => $this->ip,
            'user_agent' => $this->userAgent,
            'created_at' => now(),
        ]);
    }
}
