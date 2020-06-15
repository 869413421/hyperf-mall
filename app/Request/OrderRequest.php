<?php

declare(strict_types=1);

namespace App\Request;

use App\Model\ProductSku;
use Hyperf\Validation\Request\FormRequest;
use Hyperf\Validation\Rule;

class OrderRequest extends FormRequest
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
                    'address_id' => [
                        'required',
                        Rule::exists('user_addresses', 'id')->where('user_id', authUser()->id)
                    ],
                    'items' => 'required|array',
                    'items.*.sku_id' => [
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

                            preg_match('/items\.(\d+)\.sku_id/', $attribute, $m);
                            $index = $m[1];
                            // 根据索引找到用户所提交的购买数量
                            $amount = $this->input('items')[$index]['amount'];
                            if ($amount > 0 && $sku->stock < $amount)
                            {
                                $fail("{$sku->title}库存不足");
                                return;
                            }
                        }
                    ],
                    'items.*.amount' => 'required|integer|min:1',
                    'code' => [
                        'nullable',
                        Rule::exists('coupon_codes')
                    ]
                ];
                break;
        }
    }

}
