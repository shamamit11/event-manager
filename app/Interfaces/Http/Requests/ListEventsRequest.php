<?php

namespace App\Interfaces\Http\Requests;

class ListEventsRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'start' => 'required|date_format:Y-m-d\TH:i:s',
            'end' => 'required|date_format:Y-m-d\TH:i:s|after:start',
            'per_page' => 'nullable|integer|min:1',
            'page' => 'nullable|integer|min:1'
        ];
    }
}
