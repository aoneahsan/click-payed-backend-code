<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserTransactionHistoryResource extends JsonResource
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
            'trx_id' => $this->trx_id,
            'amount' => $this->amount,
            'remaining_balance' => $this->remaining_balance,
            'transaction_type' => $this->transaction_type,
            'date_time' => date('l F j, Y', strtotime($this->created_at))
        ];
    }
}
