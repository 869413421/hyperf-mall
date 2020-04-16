<?php

declare(strict_types=1);

namespace App\Request\Center\Permission;

use Hyperf\Validation\Request\FormRequest;
use Hyperf\Validation\Rule;

class PermissionRequest extends FormRequest
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
                    'parent_id' => 'required|exists:permissions',
                    'url' => 'required|string|unique:permissions',
                    'name' => 'required|string|min:2|max:30|unique:permissions',
                    'display_name' => 'required|string|min:2|max:30',
                    'guard_name' => 'required|alpha|string|min:2|max:30',
                    'sort' => 'required|integer'
                ];
                break;
            case 'PATCH':
                return [
                    'id' => 'required|exists:permissions,id',
                    'parent_id' => 'nullable|exists:permissions',
                    'url' => [
                        'nullable',
                        Rule::unique('permissions')->ignore($this->input('id'))
                    ],
                    'name' => [
                        'nullable',
                        'string',
                        'min:2',
                        'max:30',
                        Rule::unique('permissions')->ignore($this->input('id'))
                    ],
                    'display_name' => 'nullable|string|min:2|max:30',
                    'guard_name' => 'nullable|string|min:2|max:30',
                    'sort' => 'nullable|integer'
                ];
                break;
            case 'DELETE':
                return [
                    'id' => 'required|exists:permissions',
                ];
                break;
        }
    }

    public function messages(): array
    {
        return [

        ];
    }

}
