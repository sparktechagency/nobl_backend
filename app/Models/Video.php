<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $guarded = ['id'];

    public function getThumbnailAttribute($thumbnail)
    {
        return asset('uploads/videos/thumbnail/') . '/' . $thumbnail;
    }
    public function getVideoAttribute($video)
    {
        return asset('uploads/videos/video') . '/' . $video;
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
