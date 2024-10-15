<?php

namespace App\Jobs;

use App\Mail\VideoSendMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendMail implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    protected $request_video;

    public function __construct($request_video)
    {
        $this->request_video = $request_video;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->request_video->video_url && $this->request_video->receiver_mail) {
            Mail::to($this->request_video->receiver_mail)->send(new VideoSendMail($this->request_video));
        }
    }
}
