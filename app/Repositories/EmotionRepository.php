<?php

namespace App\Repositories;

use App\Models\Emotion;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;

class EmotionRepository 
{
    protected $model;

    public function __construct(Emotion $emotion) {
        $this->model = $emotion;
    }

    public function all(): Collection {
        return $this->model->all();
    }

    public function currentEmotionsOnUserCount(): int {
        return auth()->user()->emotions()
            ->whereDate('emotion_user.created_at', Carbon::today())
            ->count();
    }

    public function getEmotionsBetweenDatesForCurrentUser(Carbon $starts_at, Carbon $ends_at) {
        return auth()->user()->emotions()
            ->whereDate('emotion_user.created_at', '>=', $starts_at)
            ->whereDate('emotion_user.created_at', '<=', $ends_at)
            ->get()
            ->groupBy('id')
            ->reduce(function ($carry, $emotions) {
                $carry[] = [
                    'name' => $emotions->first()->name,
                    'count' => $emotions->count()
                ];

                return $carry;
            }, []);
    }

    public function store($data): Emotion {
        return $this->model->create($data);
    }

    public function attachEmotionToUser($id) {
        auth()->user()->emotions()->attach($id);
    }

    public function update($id, $name) {
        $this->model
            ->where('id', $id)
            ->update([
                'name' => $name
            ]);
    }

    public function destroy($id) {
        $this->model->destroy($id);
    }
    
}