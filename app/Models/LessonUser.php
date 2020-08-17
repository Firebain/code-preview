<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonUser extends Model
{
    protected $fillable = ['lesson_id', 'user_id', 'home_work'];
}
