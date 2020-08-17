<?php

namespace App\Repositories;

use App\Models\Book;
use Illuminate\Support\Collection;

class BookRepository
{
    protected $model;

    public function __construct(Book $book) {
        $this->model = $book;
    }

    public function all(): Collection {
        return $this->model->all();
    }

    public function create(array $data): Book {
        return $this->model->create([
            'name' => $data['name'],
            'image' => $data['image']->store('public/books/previews'),
            'file' => $data['file']->store('public/books/files'),
        ]);
    }

    public function update(int $id, array $data) {
        $updateData['name'] = $data['name'];

        if ($data['image']) {
            $updateData['image'] = $data['image']->store('public/books/previews');
        }

        if ($data['file']) {
            $updateData['file'] = $data['file']->store('public/books/previews');
        }

        return $this->model->where('id', $id)
            ->update($updateData);
    }

    public function destroy($id) {
        $this->model->destroy($id);
    }
}