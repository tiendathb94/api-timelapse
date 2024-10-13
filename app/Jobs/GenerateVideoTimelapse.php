<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GenerateVideoTimelapse implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    protected $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!file_exists(public_path('video'))) mkdir(public_path('video'));

        $videoname = public_path('video') . '/' . 'timelapse_' . $this->path . '_1080p.mp4';
        $images_path = public_path('images') . '/' . $this->path . '/seq-%08d.jpg';

        $ffmpeg_cmd = "ffmpeg -y -framerate 25 -i $images_path -c:v libx264 -crf 25 -movflags +faststart -an $videoname";

        Log::info('---------------------------');
        Log::info($videoname);
        Log::info($images_path);
        Log::info($ffmpeg_cmd);
        Log::info('---------------------------');

        exec($ffmpeg_cmd);

        // put s3 and send mail
    }
}
