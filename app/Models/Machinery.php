<?php

namespace App\Models;

use App\Pivots\MachineryExpense;
use App\Traits\UploadableFile;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Machinery extends Model
{
    use SoftDeletes;
    use UploadableFile;

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
        'value_price',
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
        'total_cost_equipment',
        'current_value'
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

    public function machineryExpenses()
    {
        return $this->hasMany(MachineryExpense::class, 'machinery_id');
    }
    public function machineryMonthlyExpenses()
    {
        return $this->hasMany(MachineryMonthlyExpenses::class, 'machinery_id');
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
        // return MachineryExpense::where('machinery_id', $this->id)->sum('amount');
        return $this->machineryExpenses->sum('amount');
    }
    public function getTotalMonthlyExpensesAmountAttribute()
    {
        // return MachineryExpense::where('machinery_id', $this->id)->sum('amount');
        return $this->machineryMonthlyExpenses->sum('amount');
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
    public function getTotalLeaseIncomesBalanceAttribute()
    {
        return doubleval($this->leaseIncomes->sum('balance'));
    }
    /**
     * Get the total Cost's Equipmenet Amount.
     *
     * @return double
     */
    public function getTotalCostEquipmentAttribute()
    {
        return doubleval($this->cost_price) +
            doubleval($this->total_expenses_amount);
        // doubleval($this->total_service_expenses_amount);
    }

    public function getMonthsUsedAttribute()
    {
        $date = Carbon::parse($this->acquisition_date);
        return $date->diffInMonths(Carbon::now());
    }
    public function getCategoryNameAttribute()
    {
        return $this->category->name;
    }

    public function getMonthlyDepreciationAttribute()
    {
        return ($this->cost_price * $this->percent_depreciation) / 12;
    }

    public function getCurrentSalePriceAttribute()
    {
        if ($this->value_price > 0) {

            $percent_margin_cost = 0.85;
            $percent_margin_value = 0.85;
            $percent_margin_renta = 0.8;
            $limitMonthPercent = 24;
            $MonthsUsed = $this->monthsUsed;

            $TotalExpensesAmount = $this->totalExpensesAmount;
            $TotalMonthlyExpensesAmount = $this->totalMonthlyExpensesAmount;

            $Costo_Real = $this->cost_price + $TotalExpensesAmount;
            $Valor_Actual = $this->value_price + $TotalExpensesAmount;

            $Monto_Renta = $TotalMonthlyExpensesAmount / $percent_margin_renta;
            // $Valor_Real = round($Costo_Real / $percent_margin_cost, 2);
            // $Valor_Comercial = round($Valor_Actual / $percent_margin_value, 2);
            $Valor_Real = $Costo_Real / $percent_margin_cost;
            $Valor_Comercial = $Valor_Actual / $percent_margin_value;

            $Gasto_Mensual_Estimado = $TotalMonthlyExpensesAmount * $MonthsUsed;
            $Valor_Real_Estimado = $Valor_Real - $Gasto_Mensual_Estimado;
            $Valor_Comercial_Estimado = $Valor_Comercial - $Gasto_Mensual_Estimado;

            // $Percent_Valor_Comercial = round($Valor_Comercial_Estimado / $Valor_Actual, 2);
            $Percent_Valor_Comercial = $Valor_Comercial_Estimado / $Valor_Actual;
            if ($limitMonthPercent < $MonthsUsed) {
                $Percent_Valor_Comercial += 0.01;
            }
            $Price_Sales = $Valor_Actual * $Percent_Valor_Comercial;

            return $Price_Sales;
        } else {
            return 0;
        }
    }

    public function getFolderPath()
    {
        return 'rentas/machineries/id_' . $this->id . '/images';
    }


}