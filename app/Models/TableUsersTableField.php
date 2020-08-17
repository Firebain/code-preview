<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TableUsersTableField extends Model
{
    protected $fillable = ['table_users_id', 'table_fields_id', 'answer'];
}
