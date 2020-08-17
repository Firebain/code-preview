<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceOperation extends Model
{
    const BONUS = 'bounus';
    const TRANSACTION = 'transaction';
    const WITHDRAWAL = 'withdrawal';

    public static $types = [self::BONUS, self::TRANSACTION, self::WITHDRAWAL];

    protected $fillable = ['type', 'sum', 'sender_id', 'receiver_id'];

    public function sender() {
        return $this->belongsTo('App\Models\User', 'sender_id');
    }

    public function receiver() {
        return $this->belongsTo('App\Models\User', 'receiver_id');
    }
}
