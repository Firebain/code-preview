<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Teaching as TeachingResource;
use App\Repositories\TeachingRepository;

class TeachingController extends Controller
{
    protected $teaching;

    public function __construct(TeachingRepository $teaching)
    {
        $this->teaching = $teaching;
    }

    public function index() {
        $teachingResources = $this->teaching->all();

        return TeachingResource::collection($teachingResources);
    }
}
