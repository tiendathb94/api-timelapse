<?php

namespace App\Jobs;

use App\Models\RequestVideoTimelapse;
use Aws\Exception\AwsException;
use Aws\S3\S3Client;
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
        $request_video = RequestVideoTimelapse::query()
            ->with('camera.project.group')
            ->where([
                'code' => $this->path,
                // 'is_handled' => 0
            ])->firstOrFail();

        if (!file_exists(public_path('video'))) mkdir(public_path('video'));

        $pathVideo = public_path('video');
        $startDate =  str_replace(['-', ':'], '', $request_video->start_date);
        $endDate = str_replace(['-', ':'], '', $request_video->end_date);
        $startTime = str_replace(['-', ':'], '', $request_video->start_time);
        $endTime = str_replace(['-', ':'], '', $request_video->end_time);

        $fileName = sprintf("%s-%s-%s-%s.mp4", $startDate, $endDate, $startTime, $endTime);
        $sourceFile = $pathVideo . '/' . $fileName;

        $images_path = public_path('images') . '/' . $this->path . '/seq-%08d.jpg';

        $ffmpeg_cmd = "ffmpeg -y -framerate 25 -i $images_path -c:v libx264 -crf 25 -movflags +faststart -an $sourceFile";

        Log::info('---------------------------');
        Log::info($sourceFile);
        Log::info($images_path);
        Log::info($ffmpeg_cmd);
        Log::info('---------------------------');

        exec($ffmpeg_cmd);

        $url = $this->uploadVideo($sourceFile, sprintf("%s/%s/%s/videos/%s", $request_video->camera->project->group->code, $request_video->camera->project->code, $request_video->camera->code, $fileName));

        $request_video->is_handled = 1;
        $request_video->video_url = $url;
        $request_video->save();

        $this->emptyFolderImage(public_path('images') . '/' . $this->path);
    }

    private function uploadVideo($sourceFile, $fileName)
    {
        $client = new S3Client([
            'region'  => "us-west-1",
            'version' => 'latest',
            'credentials' => [
                'key'    => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret'),
            ],
            'endpoint' => config('filesystems.disks.s3.endpoint'),
            'use_path_style_endpoint' => true,
        ]);

        $bucket = config('filesystems.disks.s3.bucket');

        try {
            // Upload the video
            $result = $client->putObject([
                'Bucket' => $bucket,
                'Key'    => $fileName,
                'SourceFile' => $sourceFile,
                'ACL'    => 'public-read',
                'ContentType' => 'video/mp4',
            ]);
            $url = $result->get('ObjectURL');
            // Get the URL of the uploaded video
            Log::info("Video uploaded successfully: " . $url);
            return $url;
        } catch (AwsException $e) {
            Log::info("Error uploading video: " . $e->getMessage());
            return false;
        }
    }

    private function emptyFolderImage($path, $pattern = "jpg")
    {

        if (is_dir($path)) {
            $files = glob($path . '/*.' . $pattern, GLOB_BRACE);
            foreach ($files as $file) {
                if (is_file($file)) unlink($file);
            }
            if (count(array_diff(scandir($path), array('.', '..'))) === 0) rmdir($path);
        }
    }
}
