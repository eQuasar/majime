<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProcessAppointmentResource extends JsonResource
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
        'appointment_id' => $this->appointment_id,
        'user_id' => $this->user_id,
        'start_process_date' => $this->start_process_date,
        'process_start_time' => $this->process_start_time,
        'end_process_date' => $this->end_process_date,
        'process_end_time' => $this->process_end_time,
        'startprocess_image_files' => $this->startprocess_image_files,
        'endprocess_image_files' => $this->endprocess_image_files,
        'payment_method' => $this->payment_method,
        ];
    }
}
