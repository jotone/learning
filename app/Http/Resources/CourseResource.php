<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'url' => $this->url,
            'description' => $this->description,
            'img_url' => $this->img_url,
            'link' => $this->link,
            'expire_link' => $this->expire_link,
            'support_link' => $this->support_link,
            'fb_link' => $this->fb_link,
            'optional_duration' => $this->optional_duration,
            'optional_expire_page' => $this->optional_expire_page,
            'status' => $this->status,
            'position' => $this->position,
            'created_at' => $this->created_at->format('j/M/Y H:i')
        ];
    }
}
