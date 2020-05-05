<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawalRequestsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_details->id,
            'account_name' => $this->user_details->name,
            'account_no' => $this->user_details->phone_number,
            'amount' => $this->amount,
            'status' => $this->status,
            'trx_id' => $this->trx_id,
            'payment_method' => $this->payment_method,
            'date_time' => date('F j, Y', strtotime($this->approved_at)),
            'additional_note' => $this->additional_note
        ];
    }
}
