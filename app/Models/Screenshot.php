<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Screenshot extends Model
{
    protected $fillable = ['title', 'file_path'];

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
