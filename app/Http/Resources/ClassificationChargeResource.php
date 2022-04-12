<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClassificationChargeResource extends JsonResource
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
            'class_id' => $this->class_id,
            'service_id' => $this->service_id,
            'slots' => $this->slots,
            'service' => $this->service,
            'petclass' => $this->petclass,
        ];
    }
}
