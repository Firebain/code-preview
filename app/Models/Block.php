<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    public const SIMPLE_TYPE = "simple";
    public const NO_ORDER_TYPE = "no_order";
    public const FOR_MASTERS_TYPE = "for_masters";

    public static $types = [self::SIMPLE_TYPE, self::NO_ORDER_TYPE, self::FOR_MASTERS_TYPE];

    protected $fillable = ['name', 'type'];

    public function lessons() {
        return $this->hasMany('App\Models\Lesson');
    }
}
