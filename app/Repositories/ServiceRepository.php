<?php

namespace App\Repositories;

use App\Models\Service;
use Illuminate\Support\Collection;

class ServiceRepository 
{
    protected $model;

    public function __construct(Service $service) {
        $this->model = $service;
    }

    public function all(): Collection {
        return $this->model
            ->with('items')
            ->get()
            ->groupBy('type');
    }

    public function create(array $data, array $items = []): Service {
        $service = $this->model->create($data);

        foreach ($items as $item) {
            $service->items()->create($item);
        }

        return $service;
    }

    public function find($value, $grouped = false) {
        $services = $this->model->find($value);

        return $grouped ? $services->groupBy('type') : $services;
    }
}