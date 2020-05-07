<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class ProductSkuRequest extends FormRequest
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
                    'title' => 'required|string|between:2,50',
                    'description' => 'required|string|between:2,1000',
                    'price' => 'required|numeric',
                    'stock' => 'required|integer|min:0',
                ];
                return $rules;
                break;
            case 'PATCH':
                $rules = [
                    'title' => 'nullable|string|between:2,50',
                    'description' => 'nullable|string|between:2,1000',
                    'price' => 'nullable|numeric',
                    'stock' => 'required|integer|min:0',
                ];
                return $rules;
                break;
            case 'DELETE':
                return [

                ];

        }
    }

    public function messages(): array
    {
        return [

        ];
    }
}
