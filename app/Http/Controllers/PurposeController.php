<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\WheelOfLifePurposes as WheelOfLifePurposesResource;
use App\Repositories\WheelOfLifeRepository;

class PurposeController extends Controller
{
    protected $wheel;

    public function __construct(WheelOfLifeRepository $wheel)
    {
        $this->wheel = $wheel;
    }

    public function store(Request $request) {
        $request->validate([
            'type_id' => ['required', 'integer', 'exists:wheel_of_life_types,id'],
            'text' => ['required', 'string']
        ]);

        $purpose = $this->wheel->createPurpose([
            'user_id' => $request->user()->id,
            'wheel_of_life_types_id' => $request->type_id,
            'data' => $request->text
        ]);

        return new WheelOfLifePurposesResource($purpose);
    }

    public function destroy(Request $request, $id) {
        $user_id = $request->user()->id;

        $purpose = $this->wheel->getPurpose($id);

        if ($purpose !== null && $purpose->user_id === $user_id) {
            $purpose->delete();
        }

        return 'ok';
    }
}