<?php

namespace App\Jobs;

use App\Mail\OffNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOffNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $off;

    /**
     * Create a new job instance.
     */
    public function __construct($user, $off)
    {
        $this->user = $user;
        $this->off = $off;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $email = $this->user->email1 ?? $this->user->email ?? null;

        if (!$email) {
            return;
        }

        Mail::to($email)->send(
            new OffNotification($this->off)
        );
    }
}