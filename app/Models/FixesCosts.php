<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class FixesCosts extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fixes_costs';

    protected $fillable = [
        'name'
    ];


    public function machinery()
    {
        return $this->belongsToMany(Machinery::class, 'machiner_fixes_costs', 'fixes_cost_id')
            ->withPivot('amount')
            ->withTimestamps();
        ;
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

}