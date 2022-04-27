<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LineItemsResource extends JsonResource
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
            'line_item_id' => $this->line_item_id,
            'name' => $this->name,
            'product_id' => $this->product_id,
            'variation_id' => $this->variation_id,
            'quantity' => $this->quantity,
            'tax_class' => $this->tax_class,
            'subtotal' => $this->subtotal,
            'subtotal_tax' => $this->subtotal_tax,
            'total' => $this->total,
            'total_tax' => $this->total_tax,
            'sku' => $this->sku,
            'price' => $this->price,
            'parent_name' => $this->parent_name,
        ];
    }
}
