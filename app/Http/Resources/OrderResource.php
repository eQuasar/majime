<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\BillingResource;
use App\Http\Resources\LineItemsResource;

class OrderResource extends JsonResource
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
      'id' => (int) $this->id,
      'vid' => $this->vid,
      'status' => $this->status,
      'currency' => $this->currency,
      'prices_include_tax' => $this->prices_include_tax,
      'discount_total' => $this->discount_total,
      'date_created_gmt'=>$this->date_created_gmt,
      'discount_tax' => $this->discount_tax,
      'shipping_total' => $this->shipping_total,
      'shipping_tax' => $this->shipping_tax,
      'cart_tax' => $this->cart_tax,
      'total' => $this->total,
      'total_tax' => $this->total_tax,
      'customer_id' => $this->customer_id,
      'payment_method' => $this->payment_method,
      'payment_method_title' => $this->payment_method_title,
      'billing_info' => new BillingResource($this->id),
      // 'order_info' => new LineItemsResource($this->whenLoaded($this->id)),
      ];
    }
}
