<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class CategoryRequest extends FormRequest
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
                return [
                    'name' => 'required|string|between:2,10',
                    'parent_id' => 'nullable|exists:categories,id'
                ];
                break;
            case 'PATCH':
                return [
                    'name' => 'nullable|string|between:2,10',
                    'parent_id' => 'nullable|exists:categories,id'
                ];
        }
    }
}
