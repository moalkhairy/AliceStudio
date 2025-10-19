<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;


class StepResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'subtitle' => $this->subtitle ?? null,
            'order' => $this->order,
            'sections' => SectionResource::collection(
                $this->whenLoaded('stepSections', function () {
                    // pluck the related section, drop nulls, reindex
                    return $this->stepSections->pluck('section')->filter()->values();
                })
            ),
        ];
    }
}