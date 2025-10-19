<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;


class SectionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'input_type' => $this->input_type, // chips|text|textarea|upload
            'selection_mode' => $this->selection_mode, // single|multiple
            'min_select' => $this->min_select,
            'max_select' => $this->max_select,
            'is_required' => (bool)$this->is_required,
            'gender_scope' => $this->gender_scope,
            'group' => $this->group?->only(['id', 'name', 'code', 'exclusive_sections']),
            'choices' => ChoiceResource::collection($this->whenLoaded('choices')),
        ];
    }
}