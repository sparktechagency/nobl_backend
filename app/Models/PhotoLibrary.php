<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhotoLibrary extends Model
{
    protected $guarded = ['id'];

    public function getPhotoAttribute($photo)
    {
        return asset('uploads/photo_library/') . '/' . $photo;
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
