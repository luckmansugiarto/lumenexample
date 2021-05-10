<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeachingSessionResource extends JsonResource
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
            'session_id' => $this->id,
            'session_name' => $this->session_name,
            'start_date' => $this->start_time,
            'end_date' => $this->end_time,
            'books' => BookResource::collection($this->whenLoaded('books'))
        ];
    }
}
