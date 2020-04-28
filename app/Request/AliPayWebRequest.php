<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Di\Annotation\Inject;
use Hyperf\Validation\Request\FormRequest;
use Hyperf\Validation\Rule;
use Phper666\JwtAuth\Jwt;

class AliPayWebRequest extends FormRequest
{
    /**
     * @Inject()
     * @var Jwt
     */
    private $jwt;

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
                    'order_id' => [
                        'required',
                        Rule::exists('orders', 'id')->where('user_id', $this->jwt->getTokenObj()->getClaim('id'))
                    ]
                ];
                break;
        }
    }

}
