<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = ['user_id', 'transaction_id', 'ends_at'];

    protected $dates = [
        'ends_at',
    ];

    public function transaction()
    {
        return $this->belongsTo('App\Models\Transaction');
    }
}
