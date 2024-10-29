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
            "name" => $this->name,
              "description" => $this->description ? $this->description : "No description",
              "price" => $this->price . " $",
              "quantity" => $this->stock ,
              "image" => $this->image ? asset("images/".$this->image) : null,
          ];
    }
}
