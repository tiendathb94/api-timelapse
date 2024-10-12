<?php

namespace App\Http\Controllers;

use App\Models\Camera;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ImageController extends Controller
{
    public function getImage(Request $request)
    {
        $user = Auth::user();
        $date = $request->get('date', Carbon::now()->format('Y-m-d'));

        $cameraId = $request->camera_id;

        $camera = Camera::query()->where('active', 1)->findOrFail($cameraId);

        $project = $camera->project;

        $year = Carbon::parse($date)->format('Y');
        $month = Carbon::parse($date)->format('m');
        $day = Carbon::parse($date)->format('d');

        $prefix = sprintf('%s/%s/%s/photos/%s/%s/%s', $user->group->code, $project->code, $camera->code, $year, $month, $day);
        // dd($prefix);
        try {
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
            $result = $client->listObjectsV2([
                'Bucket' => config('filesystems.disks.s3.bucket'),
                'Prefix' => $prefix,
            ]);

            $data = array_map(function ($item) {
                $item['Url'] = sprintf("%s/%s/%s", config('filesystems.disks.s3.endpoint'), config('filesystems.disks.s3.bucket'), $item['Key']);
                $split = explode('/', $item['Key']);
                $fileName = $split[count($split) - 1];

                $dateString = substr($fileName, 0, 15);
                $item['DateTime'] = Carbon::createFromFormat('Ymd-His', $dateString)->format('Y-m-d H:i:s');
                
                return $item;
            }, data_get($result, 'Contents', []));
        } catch (\Throwable $th) {
            $data = [];
            Log::debug($th->getMessage());
        }
        array_multisort(array_column($data, 'DateTime'), SORT_ASC, $data);

        return response()->json(['data' => $data]);
    }
}
