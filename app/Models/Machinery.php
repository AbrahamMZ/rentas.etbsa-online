<?php

namespace App\Models;

use App\Pivots\MachineryExpense;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Machinery extends Model
{
    use SoftDeletes;

    protected $table = 'machineries';


    protected $fillable = [
        'category_id',
        'status_id',
        'name',
        'equipment_serial',
        'economic_serial',
        'engine_serial',
        'slug',
        'description',
        'cost_price',
        'invoice',
        'current_price',
        'sale_price',
        'monthly_lease_base_amount',
        'percent_depreciation',
        'months_depreciation',
        'acquisition_date',
        'purchase_date',
        'sale_date',
    ];

    protected $appends = [
        'total_expenses_amount',
        'total_service_expenses_amount',
        'total_cost_equipment'
    ];
    public function images()
    {
        return $this->hasMany(MachineryImage::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // public function fixesCosts()
    // {
    //     return $this->belongsToMany(FixesCosts::class, 'machinery_fixes_costs', 'machinery_id')
    //         ->withPivot('amount')
    //         ->withTimestamps();
    // }
    public function expenses()
    {
        return $this->belongsToMany(
            ExpenseCatalog::class,
            'machinery_expense_pivot_table',
            'machinery_id',
            'id'
        )
            ->using(MachineryExpense::class)
            ->withPivot(
                'reference',
                'folio',
                'amount',
                'applied_date',
            )
            ->as('expense')
            ->withTimestamps();
    }

    public function servicesExpenses()
    {
        return $this->hasMany(MachineryServiceExpenses::class, 'machinery_id');
    }
    public function leaseIncomes()
    {
        return $this->hasMany(LeaseIncomes::class, 'machinery_id');
    }

    public function scopeOrderByName($query)
    {
        $query->orderBy('id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('equipment_serial', 'like', '%' . $search . '%')
                ->orWhere('name', 'like', '%' . $search . '%');
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }

    /**
     * Get the total Expenses Amount.
     *
     * @return double
     */
    public function getTotalExpensesAmountAttribute()
    {
        return MachineryExpense::where('machinery_id', $this->id)->sum('amount');
    }

    /**
     * Get the total Service Expenses Amount.
     *
     * @return integer
     */
    public function getTotalServiceExpensesAmountAttribute()
    {
        return doubleval($this->servicesExpenses->sum('amount'));
    }
    /**
     * Get the total Cost's Equipmenet Amount.
     *
     * @return double
     */
    public function getTotalCostEquipmentAttribute()
    {
        return doubleval($this->cost_price) +
            doubleval($this->total_expenses_amount) +
            doubleval($this->total_service_expenses_amount);
    }

    public function getMonthsUsedAttribute()
    {
        $date = Carbon::parse($this->acquisition_date);
        return $date->diffInMonths(Carbon::now());
    }

    public function getMonthlyDepreciationAttribute()
    {
        return ($this->cost_price * $this->percent_depreciation) / 12;
    }
}