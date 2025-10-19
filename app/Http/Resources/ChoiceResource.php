<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;


class ChoiceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'slug' => $this->slug,
            'token' => $this->token,
            'negative_token' => $this->negative_token,
            'weight' => $this->weight,
            'icon_url' => $this->icon_url,
            'gender_scope' => $this->gender_scope,
            'is_default' => (bool)$this->is_default,
            'order' => $this->order,
        ];
    }
}