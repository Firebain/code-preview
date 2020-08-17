<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\BookRepository;

class BookController extends Controller
{
    protected $books;

    public function __construct(BookRepository $books)
    { 
        $this->books = $books;
    }

    public function index()
    {
        return $this->books->all();
    }
}
