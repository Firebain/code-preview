<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Block;
use Illuminate\Support\Collection;

class BlockRepository
{
    protected $model;

    public function __construct(Block $block) {
        $this->model = $block;
    }

    public function all(): Collection {
        $user = auth()->user();

        $user->load(
            "lessons"
        );

        $prev_lesson_done_for = function ($lesson) use ($user) {
            return $user->lessons->contains(function ($prev_lesson) use ($lesson) {
                return $lesson->number - 1 === $prev_lesson->number;
            });
        };

        return $this->model
            ->with('lessons')
            ->orderBy('number')
            ->get()
            ->map(function($block) use ($prev_lesson_done_for, $user) {
                switch ($block->type) {
                    case Block::SIMPLE_TYPE:
                        $block->lessons = $block->lessons->map(function ($lesson) use ($prev_lesson_done_for, $user) {
                            $lesson->can_access = false;

                            if ($user->has_subscription) {
                                if ($lesson->number === 1 || $prev_lesson_done_for($lesson)) {
                                    $lesson->can_access = true;
                                }
                            } else {
                                if ($lesson->number === 1 || ($lesson->number === 2 && $prev_lesson_done_for($lesson))) {
                                    $lesson->can_access = true;
                                }
                            }

                            return $lesson;
                        });

                        break;

                    case Block::NO_ORDER_TYPE:
                        $first_lesson_in_block = $block->lessons->first();

                        $can_acccess_to_block = $user->has_subscription
                            && $first_lesson_in_block !== null
                            && $prev_lesson_done_for($first_lesson_in_block);

                        $block->lessons = $block->lessons->map(function ($lesson) use ($can_acccess_to_block) {
                            $lesson->can_access = $can_acccess_to_block;

                            return $lesson;
                        });

                        break;

                    case Block::FOR_MASTERS_TYPE:
                        $block->lessons = $block->lessons->map(function ($lesson, $key) use ($user, $prev_lesson_done_for) {
                            $lesson->can_access = $user->is_master && ($key === 0 || $prev_lesson_done_for($lesson));

                            return $lesson;
                        });

                        break;
                }

                return [
                    "name" => $block->name,
                    "lessons" => $block->lessons
                ];
            });
    }
}