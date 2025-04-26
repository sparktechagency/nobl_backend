<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoComment extends Model
{
    protected $guarded = ['id'];

    public function video(){
        return $this->belongsTo(Video::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
