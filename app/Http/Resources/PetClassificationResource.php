<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PetClassificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'pet_class' => $this->pet_class,
            'age' => $this->age,
            'breed_id' => $this->breed_id,
            'classes' => $this->classes,
            'ages' => $this->ages,
            'breed' => $this->breed,
        ];
    }
}
