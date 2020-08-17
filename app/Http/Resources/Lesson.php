<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Lesson extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return [
        //     'id' => $this->id,
        //     'name' => $this->name,
        //     'parts' => LessonPart::collection($this->parts)
        // ];

        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'preview' => $this->preview,
            'video' => $this->video,
            'block' => $this->block,
            'number' => $this->number
        ];
    }
}
