<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
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
            'surname' => $this->surname,
            'email' => $this->email,
            'phone' => $this->phone,
            'city' => $this->city,
            'photo' => $this->photo,
            'role' => $this->role,
            'available_amount' => $this->available_amount,
            'current_amount' => $this->current_amount,
            'followers_count' => $this->followers_count,
            'promocode' => $this->promocode,
            'subscription' => [
                'active' => $this->has_subscription,
                'info' => new Subscription($this->subscription),
                'has_some' => $this->subscriptions->isNotEmpty()
            ],
            'inviter' => [
                'id' => $this->inviter->id,
                'name' => $this->inviter->name,
                'surname' => $this->inviter->surname
            ],
            'created_at' => $this->created_at->toDateString()
        ];
    }
}
