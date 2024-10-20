<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $table = "groups";
    protected $guarded = [];

    public function projects()
    {
        return $this->hasMany(Project::class, 'group_id');
    }
}
