<?php

namespace App\Jobs;

use App\Models\Nickname;
use App\Models\User;
use App\Services\NicknameGeneratorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateNicknameJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(NicknameGeneratorService $service): void
    {
        User::query()
            ->select('id')
            ->chunkById(200, function ($users) use ($service) {
                foreach ($users as $user) {
                    Nickname::create([
                        'user_id' => $user->id,
                        'nickname' => $service->generate()
                    ]);
                }
            });
    }
}
