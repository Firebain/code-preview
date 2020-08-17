<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserTable extends JsonResource
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
            'pivot' => $this->pivot ? $this->pivot->id : null,
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->pivot ? $this->pivot->created_at->toDateString() : null,
            'fields' => UserTableField::collection($this->fields_with_answer)
        ];
    }
}
