<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class DepositAccountsResource extends JsonResource
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
            'number' => $this->account_number,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'date_time' => date('F j, Y', strtotime($this->created_at))
        ];
    }
}
