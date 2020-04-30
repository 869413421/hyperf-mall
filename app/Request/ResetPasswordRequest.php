<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class ResetPasswordRequest extends FormRequest
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
        $rules = [
            'email' => 'required_without:phone|email|exists:users,email',
            'phone' => 'required_without:email|exists:users,phone',
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'email.required' => '请填写电话或手机号码',
            'phone.required' => '请填写电话或手机号码',
            'email.exists' => '邮箱不存在',
            'phone.exists' => '电话号码不存在'
        ];
    }
}
