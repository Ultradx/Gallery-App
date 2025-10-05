<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name'];

    public function screenshots()
    {
        return $this->belongsToMany(Screenshot::class);
    }
}
