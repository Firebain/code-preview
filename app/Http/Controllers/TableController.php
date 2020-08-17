<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pivots\TableUser;
use App\Http\Resources\UserTable as UserTableResource;
use App\Repositories\TableRepository;

class TableController extends Controller
{
    protected $tables;

    public function __construct(TableRepository $tables) {
        $this->tables = $tables;
    }

    public function index() {
        $tables = $this->tables->getCurrentTablesForUser();

        return UserTableResource::collection($tables);
    }

    public function store(Request $request) {
        $request->validate([
            'id' => ['bail', 'required', 'integer', 'exists:tables,id'],
            'fields.*.id' => [
                'bail',
                'required', 
                'integer', 
                'exists:table_fields,id',
                function($attribute, $value, $fail) use ($request) {
                    if (!(is_int($request->id) && $this->tables->isFieldOfTable($request->id, $value))) {
                        $fail("Поле не принадлежит таблице");
                    }
                }
            ],
            'fields.*.answer' => ['nullable', 'string'],
        ]);

        $table = $this->tables->createTableOnCurrentUser($request->id, $request->fields);

        return new UserTableResource($table);
    }

    public function update(Request $request, TableUser $pivot) {
        if (!$pivot->created_at->isToday()) {
            abort(403, "Срок редактирования истек");
        }

        if ($pivot->user_id !== auth()->user()->id) {
            abort(403, "Данная таблица не принадлежит текущему пользователю");
        }

        $request->validate([
            'fields.*.id' => [
                'bail',
                'required', 
                'integer', 
                'exists:table_fields,id',
                function($attribute, $value, $fail) use ($pivot) {
                    if (!$this->tables->isFieldOfTable($pivot->table->id, $value)) {
                        $fail("Поле не принадлежит таблице");
                    }
                }
            ],
            'fields.*.answer' => ['nullable', 'string'],
        ]);

        $table = $this->tables->updateTableOnCurrentUser($pivot->id, $request->fields);

        return new UserTableResource($table);
    }

    public function history(Request $request) {
        $request->validate([
            'id' => ['required', 'integer', 'exists:tables,id']
        ]);

        $tables = $this->tables->getHistoryOfTable($request->id);

        return UserTableResource::collection($tables);
    }
}
