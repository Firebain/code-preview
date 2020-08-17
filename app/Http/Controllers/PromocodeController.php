<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Promocode;

class PromocodeController extends Controller
{
    public function activate(Request $request) {
        $promocode = Promocode::where('code', $request->code)
            ->where('activation_times', '>', 0)
            ->first();

        if (!$promocode) {
            return response()->json([
                'status' => 'error',
                'message' => 'Такого купона не существует'
            ]);
        }

        $promocode->activation_times -= 1;
        $promocode->save();

        $user = $request->user();

        $user->promocode_id = $promocode->id;
        $user->save();

        return 'ok';
    }
}