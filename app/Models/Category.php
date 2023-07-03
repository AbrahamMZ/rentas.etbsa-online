<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'parent_id'
    ];

    protected $appends = ['breadcrumbs_category'];

    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function childCategories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function allParentCategories()
    {
        return $this->parentCategory()->with('allParentCategories');
    }

    public function scopeOrderByName($query)
    {
        $query->orderBy('id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('name', 'like', '%' . $search . '%');
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }

    public function getBreadcrumbsCategoryAttribute()
    {
        $categories = [];
        $parent = $this->parentCategory;

        while ($parent) {
            $categories[] = [
                'id' => $parent->id,
                'text' => $parent->name,
            ];
            $parent = $parent->parentCategory;
        }
        array_unshift($categories, [
            'id' => $this->id,
            'text' => $this->name,
        ]);

        $this->unsetRelation('parentCategory');

        return array_reverse($categories);
    }

    public function allChildCategories()
    {
        return $this->childCategories()->with('allChildCategories');
    }

    static function getTreeCategories()
    {
        return Category::with('allChildCategories')
            ->whereNull('parent_id')->get(['id', 'name', 'parent_id'])->map(function ($category) {
                return Category::getCategoryArray($category);
            });
    }
    static function getCategoryArray($category)
    {
        $categoryArray = [
            'name' => $category->name,
            'id' => $category->id,
            'children' => $category->allChildCategories->map(function ($childCategory) {
                return Category::getCategoryArray($childCategory);
            }),
        ];

        return $categoryArray;
    }

}