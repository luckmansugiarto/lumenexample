<?php

namespace App\Http\Requests;

use Pearl\RequestValidate\RequestAbstract;

class TeachingSessionRequest extends RequestAbstract
{
    private $dateFormat = 'Y-m-d H:i:s';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'end_date' => ['required', 'date_format:' . $this->dateFormat],
            'name' => ['required', 'string', 'max:150'],
            'start_date' => ['required', 'date_format:' . $this->dateFormat]
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'date_format' => ':attribute is not a valid date with format yyyy-mm-dd hh:ii:ss',
            'name.max' => 'Session name should not exceed 150 characters in length',
            'required' => ':attribute is required'
        ];
    }
}
