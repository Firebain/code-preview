<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = ['title', 'content', 'video', 'block_id', 'number', 'preview'];

    public function block() {
        return $this->belongsTo('App\Models\Block');
    }
}
