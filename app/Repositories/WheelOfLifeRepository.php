<?php

namespace App\Repositories;

use App\Models\WheelOfLifeTypes;
use App\Models\WheelOfLife;
use Illuminate\Support\Collection;
use App\Models\WheelOfLifePurpose;

class WheelOfLifeRepository 
{
    protected $model;

    protected $types;

    protected $purposes;

    public function __construct(WheelOfLife $model, WheelOfLifeTypes $types, WheelOfLifePurpose $purposes) {
        $this->model = $model;

        $this->types = $types;

        $this->purposes = $purposes;
    }

    public function all(int $user_id): Collection {
        return $this->types
            ->with([
                'purposes' => function ($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                }, 
                'allSelected' => function ($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                }])
            ->get();
    }

    public function check(int $type_id, int $user_id) {
        $this->model->create([
            'user_id' => $user_id,
            'wheel_of_life_types_id' => $type_id
        ]);
    }

    public function uncheck(int $type_id, int $user_id) {
        $this->model->where([
            'user_id' => $user_id,
            'wheel_of_life_types_id' => $type_id
        ])->delete();
    }

    public function createType(array $data): WheelOfLifeTypes {
        return $this->types->create($data);
    }

    public function getPurpose(int $id): WheelOfLifePurpose {
        return $this->purposes->find($id);
    }

    public function createPurpose(array $data): WheelOfLifePurpose {
        return $this->purposes->create($data);
    }
}