<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = ['name'];

    public function fields() {
        return $this->hasMany('App\Models\TableField');
    }

    public function getFieldsWithAnswerAttribute() {
        $addAnswer = function($fields, callable $closure) {
            return $fields->map(function ($field) use ($closure) {
                $field->answer = $closure($field->id);

                return $field;
            });
        };

        $fields = $this->fields;

        if ($this->pivot) {
            $answers = $this->pivot->answers;

            $answers = $answers->reduce(function ($carry, $field) {
                $carry[$field->id] = $field->pivot->answer;

                return $carry;
            });

            $fields = $addAnswer($fields, function($id) use ($answers) {
                return !empty($answers[$id]) ? $answers[$id] : '';
            });
        } else {
            $fields = $addAnswer($fields, function() {
                return '';
            });
        }

        return $fields;
    }
}
