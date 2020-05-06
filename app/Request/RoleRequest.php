<?php

declare(strict_types=1);

namespace App\Request;

use App\Model\Permission;
use Hyperf\Validation\Request\FormRequest;
use Hyperf\Validation\Rule;

class RoleRequest extends FormRequest
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
                    'name' => 'required|unique:roles',
                    'guard_name' => 'required|string',
                    'description' => 'required|string|min:2|max:30',
                ];
                break;
            case 'PATCH':
                return [
                    'name' => [
                        'nullable',
                        Rule::unique('roles')->ignore($this->route('id'))
                    ],
                    'description' => 'nullable|string|min:2|max:200',

                ];
                break;
            case 'DELETE':
                return [

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
