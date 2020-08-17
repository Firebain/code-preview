<?php

namespace App\Repositories;

use App\Models\Teaching;
use Illuminate\Support\Collection;

class TeachingRepository
{
    protected $model;

    public function __construct(Teaching $teaching) {
        $this->model = $teaching;
    }

    public function all(): Collection {
        return $this->model->all();
    }

    public function create(array $data): Teaching {
        return $this->model->create($data);
    }

    public function update(Teaching $teaching, array $data) {
        $teaching->title = $data['title'];
        $teaching->file = $data['file'] ?: $teaching->file;

        $teaching->save();
    }

    public function destroy(Teaching $teaching) {
        $teaching->delete();
    }
}