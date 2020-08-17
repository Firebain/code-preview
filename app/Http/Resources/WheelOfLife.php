<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WheelOfLife extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'color' => $this->color,
            'selected' => $this->selected,
            'purposes' => WheelOfLifePurposes::collection($this->purposes),
        ];
    }
}
