<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class DepositRequestsResource extends JsonResource
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
            'date_time' => $this->created_at,
            'account_name' => $this->user()->name,
            'account_no' => $this->user()->phone_number,
            'amount' => $this->amount,
            'status' => $this->status,
            'trx_id' => $this->trx_id
        ];
    }
}
