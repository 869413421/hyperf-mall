<?php

declare(strict_types=1);

namespace App\Request\Center\Admin;

use Hyperf\Validation\Request\FormRequest;
use Hyperf\Validation\Rule;

class AdminRequest extends FormRequest
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
        if ($this->path() == 'center/admin/role')
        {
            return [
                'id' => 'required|exists:users',
                'role_id' => 'required|exists:roles,id'
            ];
        }

        if ($this->path() == 'center/admin/status' || $this->path() == 'center/admin/password')
        {
            return [
                'id' => 'required|exists:users',
            ];
        }

        switch ($this->getMethod())
        {
            case 'POST':
                $rules = [
                    'user_name' => 'required|unique:users',
                    'password' => 'required|alpha_dash|min:6',
                    'email' => 'required_without:phone|email|unique:users',
                    'phone' => 'required_without:email|unique:users',
                    'real_name' => 'nullable|string|max:50',
                    'sex' => 'nullable|integer|max:2',
                    'avatar' => 'nullable|url'
                ];
                return $rules;
                break;
            case 'PATCH':
                $rules = [
                    'id' => 'required|exists:users',
                    'user_name' => [
                        'nullable',
                        Rule::unique('users')->ignore($this->input('id')),
                    ],
                    'email' => [
                        'nullable',
                        'email',
                        Rule::unique('users')->ignore($this->input('id')),
                    ],
                    'password' => 'required|alpha_dash|min:6',
                    'phone' => [
                        'nullable',
                        Rule::unique('users')->ignore($this->input('id')),
                    ],
                    'real_name' => 'nullable|string|max:50',
                    'sex' => 'nullable|integer|max:2',
                    'avatar' => 'nullable|url'
                ];
                return $rules;
                break;
            case 'DELETE':
                return [
                    'id' => 'required|exists:users',
                ];

        }
    }

    public function messages(): array
    {
        return [
            'user_name.required' => '请填写用户名',
            'user_name.unique' => '用户名已经存在',
            'user_password' => '请填写密码',
            'email.required' => '请填写邮箱',
            'email.unique' => '邮箱已经存在'
        ];
    }
}
