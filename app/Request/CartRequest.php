<?php

declare(strict_types=1);

namespace App\Request;

use App\Model\ProductSku;
use Hyperf\Validation\Request\FormRequest;

class CartRequest extends FormRequest
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
                    'sku_id' => [
                        'required',
                        function ($attribute, $value, $fail)
                        {
                            $sku = ProductSku::getFirstById($value);
                            if (!$sku)
                            {
                                $fail('商品不存在');
                                return;
                            }

                            if (!$sku->product->on_sale)
                            {
                                $fail('商品没上架');
                                return;
                            }

                            if ($sku->stock === 0)
                            {
                                $fail('没有库存');
                                return;
                            }

                            if ($this->input('amount') > 0 && $sku->stock < $this->input('amount'))
                            {
                                $fail('库存不足');
                                return;
                            }
                        }
                    ],
                    'amount' => 'required|integer'
                ];
                break;
            case 'DELETE':
                return [
                    'sku_id' => 'required|exists:product_skus,id'
                ];
                break;
        }
    }

}
