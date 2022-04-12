<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PetResource extends JsonResource
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
            'user_id' => $this->user_id,
            'name' => $this->name,
            'breed_id' => $this->breed_id,
            'image' => $this->image,
            'dob' => date('l jS \of F Y',strtotime($this->dob)),
            'dob_simple' => $this->dob,
            'aggresive' => $this->aggresive_level,
            'coat_level' => $this->coat,
            'client_id' => $this->client_id,
            'cat_id' => $this->pet_cat_id,
            'category' => $this->category,
            'breed' => $this->breed,
        ];
    }
}
