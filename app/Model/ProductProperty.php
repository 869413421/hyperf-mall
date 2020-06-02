<?php

declare (strict_types=1);

namespace App\Model;

/**
 */
class ProductProperty extends ModelBase implements ModelInterface
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_properties';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id', 'name', 'value'
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}