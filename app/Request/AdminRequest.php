<?php

declare(strict_types=1);

namespace App\Request;

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
        switch ($this->getMethod())
        {
            case 'GET':
                return [
                    'user_name' => 'nullable|between:2,10',
                    'role_id' => 'nullable|exists:roles,id',
                    'sort' => 'nullable|in:DESC,ASC',
                    'status' => 'nullable|in:0,1'
                ];
                break;
            case 'POST':
                $rules = [
                    'user_name' => 'required|unique:users',
                    'password' => 'required|alpha_dash|min:6',
                    'email' => 'required_without:phone|email|unique:users',
                    'phone' => 'required_without:email|unique:users',
                    'real_name' => 'nullable|string|max:50',
                    'sex' => 'nullable|integer|max:2',
                    'avatar' => 'nullable|url',
                    'role_id'=>'nullable|exists:roles,id'
                ];
                return $rules;
                break;
            case 'PATCH':
                $rules = [
                    'user_name' => [
                        'nullable',
                        Rule::unique('users')->ignore($this->route('id')),
                    ],
                    'email' => [
                        'nullable',
                        'email',
                        Rule::unique('users')->ignore($this->route('id')),
                    ],
                    'password' => 'nullable|alpha_dash|min:6',
                    'phone' => [
                        'nullable',
                        Rule::unique('users')->ignore($this->route('id')),
                    ],
                    'real_name' => 'nullable|string|max:50',
                    'sex' => 'nullable|integer|max:2',
                    'avatar' => 'nullable|url',
                    'role_id'=>'nullable|exists:roles,id'
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
            'user_name.required' => '请填写用户名',
            'user_name.unique' => '用户名已经存在',
            'user_password' => '请填写密码',
            'email.required' => '请填写邮箱',
            'email.unique' => '邮箱已经存在'
        ];
    }
}
