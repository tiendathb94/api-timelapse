<?php

namespace App\Models;

use Hashids\Hashids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestVideoTimelapse extends Model
{
    use HasFactory;

    protected $table = "request_video_timelapse";
    protected $guarded = [];

    public static function createVideoCode($id, $keyword = "vntimelapse", $length = 8)
    {
        $alphabet = "abcdefghijklmnopqrstuvwxyz123456789";
        $hashids = new Hashids($keyword, $length, $alphabet);
        return date('dmy') . strtoupper($hashids->encode($id));
    }

}
