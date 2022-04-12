<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
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
            'vehicle_number' => $this->vehicle_number,
            'vehicle_type' => $this->vehicle_type,
            // 'zone' => $this->zone,
            'city_id' => $this->city_id,
            'country_id' => $this->country_id,
            'state_id' => $this->state_id,
            'groomer_id' => $this->groomer_id,
            'type' => $this->type,
            'city' => $this->city,
            'groomer' => $this->groomer,
            'zone' => $this->zone,
        ];
    }
}
