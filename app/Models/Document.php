<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $guarded = ['id'];

    public function getFileAttribute($file)
    {
        return asset('uploads/documents/file') . '/' . $file;
    }
    public function getThumbnailAttribute($thumbnail)
    {
        return asset('uploads/documents/thumbnail') . '/' . $thumbnail;
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
