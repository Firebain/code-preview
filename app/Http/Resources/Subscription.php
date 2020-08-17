<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Subscription extends JsonResource
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
            'created_at' => $this->created_at,
            'ends_at' => $this->ends_at,
            'days_left' => $this->ends_at->diffInDays(now()),
            'days_total' => $this->ends_at->diffInDays($this->created_at)
        ];
    }
}
