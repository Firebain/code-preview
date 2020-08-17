<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $fillable = ["type", "key", "value", "content_type"];

    public $timestamps = false;
}
