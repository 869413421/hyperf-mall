<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class TokenRequest extends FormRequest
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
                    'phone' => 'required_without:email',
                    'email' => 'required_without:phone|email',
                    'password' => 'required',
                ];

                return $rules;
                break;
        }
    }

}
