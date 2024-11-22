<?php

namespace Honed\Table\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TableActionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return array_merge([
            'table' => ['required', 'string'],
            'name' => ['required', 'string'],
            'type' => ['required', 'in:bulk,inline'],
        ], match ($this->input('type')) {
            'bulk' => [
                'only' => ['sometimes', 'array'],
                'except' => ['sometimes', 'array'],
                'all' => ['required', 'boolean'],
                'only.*' => ['sometimes', 'string', 'integer'], 
                'except.*' => ['sometimes', 'string', 'integer'],
            ],
            default => [
                'id' => ['required', 'string', 'integer'],
            ],
        });
    }
}
