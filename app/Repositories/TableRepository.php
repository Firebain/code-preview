<?php

namespace App\Repositories;

use App\Models\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use App\Pivots\TableUser;

class TableRepository 
{
    protected $model;

    public function __construct(Table $table) {
        $this->model = $table;
    }

    public function isFieldOfTable(int $table_id, int $field_id): bool {
        return $this->model
            ->where('id', $table_id)
            ->whereHas('fields', function ($query) use ($field_id) {
                $query->where('id', $field_id);
            })
            ->exists();
    }

    public function createTableOnCurrentUser(int $table_id, array $fields): Table {
        $user = auth()->user();

        $user->tables()->attach($table_id);

        $table = $user->tables()->orderBy('pivot_id', 'desc')->first();

        $fields = collect($fields)->reduce(function($carry, $field) {
            if ($field['answer']) {
                $carry[$field['id']]['answer'] = $field['answer'];
            }

            return $carry;
        });

        $table->pivot->answers()->attach($fields);

        return $table;
    }

    public function updateTableOnCurrentUser(int $pivot, array $fields): Table {
        $fields = collect($fields)->reduce(function($carry, $field) {
            if ($field['answer']) {
                $carry[$field['id']]['answer'] = $field['answer'];
            }

            return $carry;
        });

        $table = auth()->user()->tables()
            ->where('table_user.id', $pivot)
            ->first();

        $table->pivot->answers()->sync($fields);

        return $table;
    }

    public function getCurrentTablesForUser(): Collection {
        $user = auth()->user();

        $tables = $this->model->all();
        $user_tables = $user->tables()
            ->whereDate('table_user.created_at', Carbon::today())
            ->get();

        $user_tables = $user_tables->reduce(function($carry, $table) {
            $carry[$table->id][] = $table;

            return $carry;
        });

        $tables = $tables->flatMap(function($table) use ($user_tables) {
            $sameTables = !empty($user_tables[$table->id]) ? $user_tables[$table->id] : null;

            return $sameTables ?: [$table];
        });

        return $tables;
    }

    public function getHistoryOfTable(int $id): Collection {
        $user = auth()->user();

        return $user->tables()
            ->wherePivot('table_id', $id)
            ->orderBy('table_user.created_at', 'desc')
            ->get();
    }
}