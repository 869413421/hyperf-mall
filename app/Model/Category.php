<?php

declare (strict_types=1);

namespace App\Model;

use Hyperf\Database\Model\Events\Creating;
use Hyperf\Database\Model\Events\Deleting;
use Hyperf\Database\Model\Events\Updated;

/**
 * @property int $id
 * @property string $name
 * @property int $parent_id
 * @property int $is_directory
 * @property int $level
 * @property string $path
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Category extends ModelBase implements ModelInterface
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id',
        'is_directory',
        'level',
        'name',
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'parent_id' => 'integer', 'is_directory' => 'integer', 'level' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function creating(Creating $event)
    {
        $this->initParentParams();
    }

    public function updated(Updated $event)
    {
        $this->initParentParams();
    }

    public function deleting(Deleting $event)
    {
        //删除所有下级
        $this->newQuery()->where('path', 'like', "-$this->id%")->delete();
    }

    public function parent()
    {
        return $this->belongsTo(Category::class);
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * 根据父级初始化参数
     */
    public function initParentParams()
    {
        if (is_null($this->parent_id))
        {
            $this->level = 0;
            $this->path = '-';
        }
        else
        {
            $this->level = $this->parent->level + 1;
            $this->path = $this->parent->path . $this->parent_id . '-';
            //更新父级是否有子目录字段
            if ($this->parent->is_directory == 0)
            {
                $this->parent->is_directory = 1;
                $this->parent->save();
            }
        }
    }

    /***
     * 获取所有父级ID
     * @return array
     */
    public function getPathIdsAttribute()
    {
        return array_filter(explode('-', trim($this->path, '-')));
    }

    /**
     * 获取所有的父类
     * @return \Hyperf\Database\Model\Builder[]|\Hyperf\Database\Model\Collection
     */
    public function getAncestorsAttribute()
    {
        return Category::query()
            ->whereIn('id', $this->path_ids)
            ->orderBy('level')
            ->get();
    }

    /***
     * 获取当前类以及父类的所有名称
     * @return mixed
     */
    public function getFullNameAttribute()
    {
        return $this->ancestors
            ->pluck('name')// 取出所有祖先类目的 name 字段作为一个数组
            ->push($this->name)// 将当前类目的 name 字段值加到数组的末尾
            ->implode(' - ');
    }
}