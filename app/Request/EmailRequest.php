<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class EmailRequest extends FormRequest
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

        return $this->getUserEmailRules();
    }

    private function getUserEmailRules(): array
    {
        switch ($this->getMethod())
        {
            case'GET':
                return [
                    'token' => 'required|string|max:16',
                    'userId' => 'required|exists:users,id'
                ];
                break;

            case 'POST':
                return [
                    'email' => 'required|email|exists:users',
                ];
                break;
        }
    }
}
