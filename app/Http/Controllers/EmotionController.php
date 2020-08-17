<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\EmotionRepository;
use App\Http\Resources\Emotion as EmotionResource;
use Illuminate\Support\Carbon;

class EmotionController extends Controller
{
    private $emotions;

    public function __construct(EmotionRepository $emotions)
    {
        $this->emotions = $emotions;
    }

    public function index()
    {
        $emotions = $this->emotions->all();

        return EmotionResource::collection($emotions);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => ['required', 'integer', 'exists:emotions,id']
        ]);

        if ($this->emotions->currentEmotionsOnUserCount() >= 3) {
            abort(401, 'Нельзя выбирать больше 3 эмоций в день');
        }

        $this->emotions->attachEmotionToUser($request->id);

        return 'ok';
    }

    public function history(Request $request)
    {
        $date_format = 'Y-m-d';

        $request->validate([
            'starts_at' => ['required', `date_format:{$date_format}`],
            'ends_at' => ['required', `date_format:{$date_format}`]
        ]);

        $starts_at = Carbon::createFromFormat($date_format, $request->starts_at);
        $ends_at = Carbon::createFromFormat($date_format, $request->ends_at);

        $emotions = $this->emotions->getEmotionsBetweenDatesForCurrentUser($starts_at, $ends_at);

        return response()->json($emotions);
    }
}
