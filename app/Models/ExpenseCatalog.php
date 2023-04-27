<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Pivots\MachineryExpense;

class ExpenseCatalog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'expense_catalogs';

    protected $fillable = [
        'name'
    ];


    public function machinery()
    {
        return $this->belongsToMany(Machinery::class, 'machinery_expense_pivot_table', 'expense_id','id')
            ->using(MachineryExpense::class)
            ->withPivot(
                'reference',
                'folio',
                'amount',
                'applied_date',
            )
            ->as('expense')
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