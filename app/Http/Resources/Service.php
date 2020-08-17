<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Service extends JsonResource
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
            'price' => $this->price,
            'icon' => [
                'blue' => $this->icon_blue,
                'white' => $this->icon_white
            ],
            'items' => ServiceItem::collection($this->items)
        ];
    }
}
