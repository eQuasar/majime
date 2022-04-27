<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AreaResource extends JsonResource
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
            'product_id' => $this->product_id,
            'line_item_id' => $this->line_item_id,
            'key' => $this->key,
            'value' => $this->value,
            'display_key' => $this->display_key,
            'display_value' => $this->display_value,
        ];
    }
}
