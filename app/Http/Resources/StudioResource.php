<?php


namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class StudioResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'steps' => StepResource::collection($this->whenLoaded('steps')),
        ];
    }
}