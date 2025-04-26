<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $guarded = ['id'];

    public function getThumbnailAttribute($thumbnail)
    {
        return asset('uploads/links') . '/' . $thumbnail;
    }

}
