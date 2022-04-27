<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BillingResource extends JsonResource
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
            'order_id' => $this->order_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'company' => $this->company,
            'address_1' => $this->address_1,
            'address_2' => $this->address_2,
            'city' => $this->city,
            'state' => $this->state,
            'postcode' => $this->postcode,
            'country' => $this->country,
            'email' => $this->email,
            'phone' => $this->phone,
        ];
    }
}
