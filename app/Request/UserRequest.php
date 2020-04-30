<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class UserRequest extends FormRequest
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
                return $this->getRegisterRules();
                break;
            case 'PATCH':
                return $this->getUpdateRules();
                break;
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

    private function getRegisterRules()
    {
        $rules = [
            'user_name' => 'required|unique:users',
            'password' => 'required|alpha_dash|min:6',
            'email' => 'required_without:phone|email|unique:users',
            'phone' => 'required_without:email|unique:users',
            'code' => 'required_with:phone|string|min:6',
        ];

        return $rules;
    }

    private function getUpdateRules()
    {
        $rules = [
            'real_name' => 'nullable|string|max:50',
            'sex' => 'nullable|integer|max:2',
            'avatar' => 'nullable|url'
        ];

        return $rules;
    }
}
