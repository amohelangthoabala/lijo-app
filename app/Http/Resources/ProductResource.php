<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'restaurant' => [
                'id' => $this->restaurant->id,
                'name' => $this->restaurant->name,
            ],
            'name' => $this->name,
            'description' => $this->description,
            'base_price' => $this->base_price,
            'variants' => $this->variants->map(fn($variant) => [
                'id' => $variant->id,
                'name' => $variant->name,
                'price' => $variant->price,
            ]),
            'images' => $this->images->map(fn($img) => asset('storage/' . $img->image_path)),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
