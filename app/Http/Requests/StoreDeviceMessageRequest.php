<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreDeviceMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->tokenCan('device:write') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'device_name' => ['required', 'string', 'max:100'],
            'payload' => ['required', 'array'],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $payload = $this->input('payload');

                if (is_array($payload) && array_is_list($payload)) {
                    $validator->errors()->add('payload', 'The payload must be a JSON object.');
                }

                if (is_array($payload) && strlen(json_encode($payload, JSON_THROW_ON_ERROR)) > 65536) {
                    $validator->errors()->add('payload', 'The payload must not exceed 64 KB.');
                }
            },
        ];
    }
}
