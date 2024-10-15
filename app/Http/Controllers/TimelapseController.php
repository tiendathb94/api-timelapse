<?php

namespace App\Http\Controllers;

use App\Jobs\DownloadImageFromS3;
use App\Models\Camera;
use App\Models\RequestVideoTimelapse;
use Aws\S3\S3Client;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimelapseController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $videos = RequestVideoTimelapse::where('user_id', $user->id)->with('camera')->orderBy('created_at', 'DESC')->get();

        return response()->json(['data' => $videos]);
    }

    public function createTimelapse(Request $request)
    {
        $user = Auth::user();

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $start_time = $request->get('start_time', '00:00:00');
        $end_time = $request->get('end_time', '23:59:59');
        $cameraId = $request->camera_id;

        $camera = Camera::query()->where('active', 1)->findOrFail($cameraId);

        $project = $camera->project;

        $maxId = RequestVideoTimelapse::query()->max('id');
        $code = RequestVideoTimelapse::createVideoCode($maxId + 1);

        $arrayInsert = [
            'user_id' => Auth::id(),
            'receiver_mail' => $user->email,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'is_handled' => 0,
            'camera_id' => $cameraId,
            'code' => $code,
            'start_time' => $start_time,
            'end_time' => $end_time,
        ];

        $request_video_timelapse = RequestVideoTimelapse::query()
            ->create($arrayInsert);

        $period = CarbonPeriod::create($start_date, $end_date);

        $newData = [];

        foreach ($period as $date) {

            $year = $date->format('Y');
            $month = $date->format('m');
            $day = $date->format('d');

            $prefix = sprintf('%s/%s/%s/photos/%s/%s/%s', $user->group->code, $project->code, $camera->code, $year, $month, $day);

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
                $item['FileName'] = $fileName;

                return $item;
            }, data_get($result, 'Contents', []));

            array_multisort(array_column($data, 'DateTime'), SORT_ASC, $data);

            $data = array_filter($data, function ($value) use ($date, $start_time, $end_time) {
                $lte = Carbon::parse($value['DateTime'])->lte(Carbon::parse($date->format('Y-m-d') . ' ' . $start_time));
                $gte = Carbon::parse($value['DateTime'])->gte(Carbon::parse($date->format('Y-m-d') . ' ' . $end_time));
                return $lte && $gte && preg_match('/\.(jpg|jpeg|png)$/i', $value['FileName']);
            });

            $newData = array_merge($newData, $data);
        }

        $saved = 0;

        foreach ($newData as $value) {
            $saved++;
            DownloadImageFromS3::dispatch($value['Url'], $request_video_timelapse->code, '/seq-' . sprintf('%08d', $saved) . '.jpg', count($newData))->onQueue('download-image-s3');
        }

        return response()->json(['message' => 'Request Success. Please waiting...']);
    }
}
