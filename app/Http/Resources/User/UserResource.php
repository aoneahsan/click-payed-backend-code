<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'phone_numer' => $this->phone_number,
            'profile_img' => $this->profile_img,
            'role' => $this->role,
            'tokken' => $this->getTokken(),
            'referal_code' => $this->referer_code
        ];
    }
}
