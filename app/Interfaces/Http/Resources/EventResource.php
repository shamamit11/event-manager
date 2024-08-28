<?php

namespace App\Interfaces\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'description' => $this->resource->description ?? null,
            'start' => $this->resource->dateRange ? $this->resource->dateRange->getStart()->format('Y-m-d\TH:i:s') : null,
            'end' => $this->resource->dateRange ? $this->resource->dateRange->getEnd()->format('Y-m-d\TH:i:s') : null,
            'recurring_pattern' => $this->resource->recurringPattern ? [
                'frequency' => $this->resource->recurringPattern->getFrequency(),
                'repeat_until' => $this->resource->recurringPattern->getRepeatUntil()->format('Y-m-d\TH:i:s'),
            ] : null,
            'parent_id' => $this->resource->parentId ?? null,
        ];
    }
}
