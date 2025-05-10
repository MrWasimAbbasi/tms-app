<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function context()
    {
        return $this->belongsTo(Context::class);
    }

    public function locale()
    {
        return $this->belongsTo(Locale::class);
    }
}
