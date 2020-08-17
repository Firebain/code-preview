<?php

namespace App\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TableUser extends Pivot
{
    public function answers() {
        return $this->belongsToMany('App\Models\TableField', 'table_field_table_user', 'table_user_id', 'table_field_id')
            ->withPivot('id', 'answer')
            ->withTimestamps();
    }

    public function table() {
        return $this->belongsTo('App\Models\Table');
    }
}
