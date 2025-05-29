<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
           return [
            'id'                => $this->id,
            'name'              => $this->name,
            'description'       => $this->description,
            'logo'              => $this->logo_url, // accessor or full path
            'contact'           => $this->contact_information,
            'rating'            => $this->rating,
            'status'            => $this->status,
            'is_top_pick'       => $this->is_top_pick,
            'review_count'      => $this->review_count,
            'order_count'       => $this->order_count,
            'visit_count'       => $this->visit_count,
            'last_activity_at'  => $this->last_activity_at,
            'created_at'        => $this->created_at,
        ];
    }
}
