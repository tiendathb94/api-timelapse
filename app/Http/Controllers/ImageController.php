<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Log;

class ImageController extends Controller
{
    public function getImage(Request $request)
    {
        $date = $request->get('date', Carbon::now()->format('Y-m-d'));

        $projectId = $request->projectId;
        $cameraId = $request->cameraId;

        $year = Carbon::parse($date)->format('Y');
        $month = Carbon::parse($date)->format('m');
        $day = Carbon::parse($date)->format('d');

        try {
            $client = new S3Client([
                'region'  => config('filesystems.disks.s3.region'),
                'version' => 'latest',
                'credentials' => [
                    'key'    => config('filesystems.disks.s3.key'),
                    'secret' => config('filesystems.disks.s3.secret'),
                ],
                'endpoint' => config('filesystems.disks.s3.endpoint'),
                'use_path_style_endpoint' => true,
            ]);

            $result = $client->listObjectsV2([
                'Bucket' => config('filesystems.disks.s3.bucket'),
                'Prefix' => 'user0000015/project0001/camera0001/photos/2024/10/05',
            ]);

            $data = array_map(function ($item) {
                $item['Url'] = sprintf("%s/%s/%s", config('filesystems.disks.s3.endpoint'), config('filesystems.disks.s3.bucket'), $item['Key']);
                return $item;
            }, $result['Contents']);
        } catch (\Throwable $th) {
            $data = [];
            Log::debug($th->getMessage());
        }

        return response()->json(['data' => $data]);
    }
}
