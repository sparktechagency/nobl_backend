<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audio extends Model
{
    protected $guarded = ['id'];

    public function getThumbnailAttribute($thumbnail)
    {
        return asset('uploads/audios/thumbnail/') . '/' . $thumbnail;
    }
    public function getAudioAttribute($audio)
    {
        return asset('uploads/audios/audio') . '/' . $audio;
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
