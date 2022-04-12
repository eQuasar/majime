<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ZoneResource extends JsonResource
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
            'name' => $this->name,
            'areas' => $this->areas_tab,
            'city_id' => $this->city_id,
            'city' => $this->city,
            'state_id' => $this->state_id,
            'country_id' => $this->country_id,
        ];
    }
}
