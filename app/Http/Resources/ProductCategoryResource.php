<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCategoryResource extends JsonResource
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
            'brand' => $this->brand,
            'price' => $this->price,
            'type' => $this->type,
            'quantity' => $this->quantity,
            'category'=>$this->category->name,
            'category_id'=>$this->category->id,
            'image_url' => asset("storage/{$this->image}"), // Adjust the path to your images
           // 'details_url' => route('products.show', ['id' => $this->id]),
            // Add other product details as needed
        ];
    }
}
