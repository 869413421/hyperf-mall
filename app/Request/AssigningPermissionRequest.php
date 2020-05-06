<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;
use Hyperf\Validation\Rule;

class AssigningPermissionRequest extends FormRequest
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
        return [
            'ids' => 'required|array',
            'ids.*' => [
                Rule::exists('permissions', 'id')
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'ids.*.exists' => '选项不存在'
        ];
    }
}
