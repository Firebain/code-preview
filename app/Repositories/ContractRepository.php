<?php

namespace App\Repositories;

use App\Models\Contract;
use Illuminate\Support\Collection;

class ContractRepository
{
    protected $model;

    public function __construct(Contract $contract) {
        $this->model = $contract;
    }

    public function all(): Collection {
        return $this->model->all();
    }

    public function create(array $data): Contract {
        return $this->model->create($data);
    }

    public function update(Contract $contract, array $data) {
        $contract->title = $data['title'];
        $contract->file = $data['file'] ?: $contract->file;

        $contract->save();
    }

    public function destroy(Contract $contract) {
        $contract->delete();
    }
}