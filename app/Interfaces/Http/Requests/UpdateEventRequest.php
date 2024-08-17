<?php

namespace App\Interfaces\Http\Requests;

class UpdateEventRequest extends ApiRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => 'required|integer|exists:events,id',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'start' => 'required|date_format:Y-m-d\TH:i:s',
            'end' => 'required|date_format:Y-m-d\TH:i:s|after:start',
            'recurring_pattern' => 'nullable|array',
            'recurring_pattern.frequency' => [
                'nullable',
                'string',
                'in:daily,weekly,monthly,yearly',
                'required_if:recurring_pattern,true',
            ],
            'recurring_pattern.repeat_until' => [
                'nullable',
                'date_format:Y-m-d\TH:i:s',
                'required_if:recurring_pattern,true',
            ],
        ];
    }
}
