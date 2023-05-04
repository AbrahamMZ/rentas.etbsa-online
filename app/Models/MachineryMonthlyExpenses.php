<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class MachineryMonthlyExpenses extends Model
{
    use HasFactory;


    protected $table = 'machinery_monthly_expenses';

    protected $fillable = [
        'monthly_expense_types_id',
        'machinery_id',
        'description',
        'base_cost_type',
        'base_cost_amount',
        'percent',
        'amount',
    ];

    public function machinery()
    {
        return $this->belongsTo(Machinery::class, 'machinery_id');
    }

    public function expenseType()
    {
        return $this->belongsTo(ExpenseCatalog::class, 'monthly_expense_types_id');
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