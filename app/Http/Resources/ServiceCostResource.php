<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceCostResource extends JsonResource
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
            'cost' => $this->cost,
            'service_id' => $this->service_id,
            'slots' => $this->slots,
            'service_mode' => $this->service_mode,
            'service' => $this->service,
            'servicemode' => $this->servicemode,
        ];
    }
}
