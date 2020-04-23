<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'profile_img' => $this->profile_img,
            'role' => $this->role,
            'member_since' => "Member Since " . date('l F j, Y', strtotime($this->created_at)),
            'balance' => $this->account->balance,
            'coins' => $this->account->coins,
            'city' => $this->details->city,
            'country' => $this->details->country,
            'referral_code' => null
        ];
    }
}
