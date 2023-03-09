<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'first_name'       => $this->first_name,
            'last_name'        => empty($this->last_name) ? null : $this->last_name,
            'email'            => $this->email,
            'img_url'          => $this->img_url,
            'about'            => $this->about,
            'status'           => $this->status,
            'status_name'      => config('enums.user.statuses')[$this->status],
            'activated_at'     => $this->activated_at ? $this->activated_at->format('j/M/Y H:i') : null,
            'last_activity'    => $this->last_activity ? $this->last_activity->format('j/M/Y H:i') : null,
            'role_slug'        => $this->role->slug,
            'role_name'        => $this->role->name,
            'timezone'         => $this->info->timezone ?? '',
            'country'          => $this->info->country ?? '',
            'city'             => $this->info->city ?? '',
            'state_region'     => $this->info->state_region ?? '',
            'address'          => $this->info->address ?? '',
            'extended_address' => $this->info->extended_address ?? '',
            'zip'              => $this->info->zip ?? '',
            'phone'            => $this->info->phone ?? '',
            'shirt_size'       => $this->info->shirt_size ?? '',
            'created_at'       => $this->created_at->format('j/M/Y H:i')
        ];
    }
}