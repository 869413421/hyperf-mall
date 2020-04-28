<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class FavorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        switch ($this->getMethod())
        {
            case 'POST':
                $rules = [
                    'id' => 'required|exists:products',
                ];
                return $rules;
                break;
            case 'DELETE':
                return [
                    'id' => 'required|exists:products',
                ];

        }
    }

    public function messages(): array
    {
        return [

        ];
    }
}
