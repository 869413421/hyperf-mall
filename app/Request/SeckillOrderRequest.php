<?php

declare(strict_types=1);

namespace App\Request;

use App\Model\Order;
use App\Model\Product;
use App\Model\ProductSku;
use Hyperf\Validation\Request\FormRequest;

class SeckillOrderRequest extends FormRequest
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
                    'address.province'      => 'required',
                    'address.city'          => 'required',
                    'address.district'      => 'required',
                    'address.address'       => 'required',
                    'address.zip'           => 'required',
                    'address.contact_name'  => 'required',
                    'address.contact_phone' => 'required',
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

                            if ($sku->product->type !== Product::TYPE_SECKILL)
                            {
                                $fail('商品不支持秒杀');
                                return;
                            }

                            if ($sku->stock === 0)
                            {
                                $fail('没有库存');
                                return;
                            }

                            if ($order = Order::query()
                                // 筛选出当前用户的订单
                                ->where('user_id', authUser()->id)
                                ->whereHas('items', function ($query) use ($value)
                                {
                                    // 筛选出包含当前 SKU 的订单
                                    $query->where('product_sku_id', $value);
                                })
                                ->where(function ($query)
                                {
                                    // 已支付的订单
                                    $query->whereNotNull('paid_at')
                                        // 或者未关闭的订单
                                        ->orWhere('closed', false);
                                })
                                ->first())
                            {
                                if ($order->paid_at)
                                {
                                    return $fail('你已经抢购了该商品');
                                }

                                return $fail('你已经下单了该商品，请到订单页面支付');
                            }
                        }
                    ]
                ];
                break;
        }
    }

}
