<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OtherUserResource extends JsonResource
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
            'gender' => $this->gender,
            'email' => $this->email,
            'image' => $this->image,
            'dob' => date('d/m/Y',strtotime($this->dob)),
            'address' => $this->address,
            'country_id' => $this->country_id,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
            'area_id' => $this->area_id,
            'pincode' => $this->pincode,
            'user' => $this->user,
            'role_id' => $this->role_id,
        ];
    }
}
