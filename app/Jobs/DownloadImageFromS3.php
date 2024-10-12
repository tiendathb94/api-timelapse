<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;



class DownloadImageFromS3 implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    protected $link;
    protected $fileName;
    protected $maxFile;
    protected $path;

    public function __construct($link, $path, $fileName, $maxFile = false)
    {
        $this->link = $link;
        $this->fileName = $fileName;
        $this->maxFile = $maxFile;
        $this->path = $path;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info($this->path);
        $fileContent = file_get_contents($this->link);

        $image = ImageManager::imagick()->read($fileContent);
        $image->resize(1920, 1080);
        $destinationPath = public_path('images/' . $this->path);

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath);
        }

        $image->save($destinationPath . '/'  . $this->fileName);

        $files = glob($destinationPath . '/*.{png,jpg,jpeg,JPG}', GLOB_BRACE);

        if ($this->maxFile == count($files)){
            GenerateVideoTimelapse::dispatch($this->path)->onQueue('generate-video-timelapse');
        }
    }
}
