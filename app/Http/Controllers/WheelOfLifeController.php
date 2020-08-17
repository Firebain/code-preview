<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\WheelOfLife as WheelOfLifeResource;
use App\Repositories\WheelOfLifeRepository;

class WheelOfLifeController extends Controller
{
    protected $wheel;

    public function __construct(WheelOfLifeRepository $wheel)
    {
        $this->wheel = $wheel;
    }

    public function index(Request $request) {
        $data = $this->wheel->all($request->user()->id);

        return WheelOfLifeResource::collection($data);
    }

    public function check(Request $request, $id) {
        $this->wheel->check($id, $request->user()->id);

        return 'ok';
    }

    public function uncheck(Request $request, $id) {
        $this->wheel->uncheck($id, $request->user()->id);

        return 'ok';
    }
}
