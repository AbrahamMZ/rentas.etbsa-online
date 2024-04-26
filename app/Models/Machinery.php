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
        'hours_work',
        'current_price',
        'sale_price',
        'monthly_lease_base_amount',
        'percent_depreciation',
        'months_depreciation',
        'warranty_date',
        'acquisition_date',
        'purchase_date',
        'sale_date',
        'jdf_amount',
        'jdf_start_date',
        'jdf_end_date',
        'jdf_terms',
    ];

    protected $appends = [
        'total_expenses_amount',
        'total_service_expenses_amount',
        'total_cost_equipment',
        'jdf_info',
        'lease_info'
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
                ->orWhere('economic_serial', 'like', '%' . $search . '%')
                ->orWhere('engine_serial', 'like', '%' . $search . '%')
                ->orWhere('name', 'like', '%' . $search . '%')
                ->orWhereHas('leaseIncomes', function ($query) use ($search) {
                    return $query->where('reference', 'like', '%' . $search . '%');
                });

        })->when($filters['category_ids'] ?? null, function ($query, $category_ids) {
            $query->whereHas('category', function ($query) use ($category_ids) {
                return $query->whereIn('id', $category_ids);
            });
        })
            ->when($filters['trashed'] ?? null, function ($query, $trashed) {
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
        return doubleval($this->cost_price + $this->total_expenses_amount);
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

    public function getOcupancyRateAttribute()
    {
        $sumLeaseTerm = (int) $this->leaseIncomes()->sum('term_lease');
        $max_occupancy = $this->months_used < 1 ? 1 : $this->months_used;
        $occupancy_rate = $sumLeaseTerm / $max_occupancy;
        return $occupancy_rate;
    }

    public function getFolderPath()
    {
        return 'rentas/machineries/id_' . $this->id . '/images';
    }


    public function getJdfInfoAttribute()
    {
        if (!$this->jdf_start_date || !$this->jdf_end_date) {
            return null;
        }
        $startDate = Carbon::create($this->jdf_start_date);
        $endDate = Carbon::create($this->jdf_end_date);
        $currentDay = Carbon::now();
        $payments = $this->machineryExpenses->where('expense_id', 19);
        $monthsJdf = $startDate->diffInMonths($endDate);

        // Verificar si la fecha actual está dentro del periodo de financiamiento
        $isActive = $currentDay->between($startDate, $endDate);
        // Obtener conteo de Pagos Realizados
        $countPayments = $payments->count();
        // Calcular el total de pagos realizados
        $totalPayments = $payments->sum('amount');
        // Calcular la cuota mensual
        $totalAmountJdf = $this->jdf_terms * $this->jdf_amount;

        // Determinar la fecha del próximo pago

        $nextPaymentDate = $currentDay->copy()->addMonthsNoOverflow(1)->day($startDate->day);
        if ($nextPaymentDate->greaterThan($endDate)) {
            $nextPaymentDate = $endDate;
        }

        // Calcular días restantes para el próximo pago
        $daysUntilNextPayment = $currentDay->diffInDays($nextPaymentDate, false);
        // Calcular días de retraso si el pago del mes actual no se ha realizado
        $daysDelay = $daysUntilNextPayment > 0 ? $daysUntilNextPayment : 0;
        // Verificar si el pago del mes actual está pendiente
        $isPaymentPending = $daysDelay > 0 && $daysDelay <= 30;


        // --------------------//
        $paymentDates = $this->machineryExpenses->where('expense_id', 19)->pluck('applied_date')->toArray();

        $scheduledPaymentDatesArray = [];
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $scheduledPaymentDatesArray[] = $currentDate->copy()->format('Y-m-d');
            $currentDate->addMonthNoOverflow();
        }
        $scheduledPaymentDates = collect($scheduledPaymentDatesArray);
        $scheduledPaymentDates->shift();

        $paymentStatuses = collect($scheduledPaymentDates->all())
            ->map(function ($date) use ($paymentDates) {
                $formattedDate = Carbon::parse($date);
                $expense = $this->machineryExpenses->where('expense_id', 19)->first(function ($fee) use ($formattedDate) {
                    return Carbon::parse($fee->applied_date)->format('Y-m') === $formattedDate->format('Y-m');
                });

                $hasPaid = collect($paymentDates)
                    ->map(function ($paymentDate) {
                        return Carbon::parse($paymentDate)->format('Y-m');
                    })
                    ->contains($formattedDate->format('Y-m'));

                return [
                    'date' => $formattedDate->locale('es')->isoFormat('D MMM Y'),
                    'jdf_amount' => $this->jdf_amount ?? 0,
                    'amount_income' => optional($expense)->amount ?? 0,
                    'applied_date' => optional($expense)->applied_date ?? null,
                    'is_paid' => $hasPaid
                ];
            })
            ->toArray();

        // ------------- //
        return [
            'isActive' => $isActive,
            'isPaid' => $totalPayments == $totalAmountJdf,
            'terms' => $startDate->locale('es')->isoFormat('D MMM Y') . '~' . $endDate->locale('es')->isoFormat('D MMM Y'),
            'paymentStatuses' => $paymentStatuses,
            'totalAmountJdf' => $totalAmountJdf,
            'totalPayments' => $totalPayments,
            'amountJDF' => $this->jdf_amount,
            'balance' => $totalAmountJdf - $totalPayments,
            'count_payments' => $countPayments . '/' . $this->jdf_terms,
            'nextPaymentDate' => $nextPaymentDate->locale('es')->isoFormat('Y-MMMM-D'),
            'daysUntilNextPayment' => $daysUntilNextPayment,
            'monthsJdf' => $monthsJdf,
        ];
    }
    public function getLeaseInfoAttribute()
    {

        $lastLease = $this->leaseIncomes->last();

        if (!$lastLease) {
            return;
            // return [
            //     'has_lease' => false,
            //     'next_payment_date' => null,
            //     'paid_installments' => 0
            // ];
        }

        $today = now();
        $startDate = Carbon::parse($lastLease->start_date);
        $endDate = Carbon::parse($lastLease->end_date);
        $monthsLease = $startDate->diffInMonths($endDate);

        // Verificar si la Machinery tiene un Lease vigente
        $isActive = $today->lte(Carbon::parse($lastLease->end_date));

        // Calcular la próxima fecha de pago
        $nextPaymentDate = $today->copy()->addMonthsNoOverflow(1)->startOfMonth()->day($startDate->day);
        if ($nextPaymentDate->greaterThan($endDate)) {
            $nextPaymentDate = $endDate;
        }

        // Calcular cuántas cuotas se han pagado hasta la fecha
        $paidInstallments = $lastLease->leaseFees->count();
        $totalAmountincome = $lastLease->leaseFees->sum('amount_income');

         // Calcular días restantes para el próximo pago
         $daysUntilNextPayment = $today->diffInDays($nextPaymentDate, false);

        // --------------------- //
        $paymentDates = $lastLease->leaseFees->pluck('payment_date')->toArray();

        $scheduledPaymentDatesArray = [];
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $scheduledPaymentDatesArray[] = $currentDate->copy()->format('Y-m-d');
            $currentDate->addMonthNoOverflow();
        }
        $scheduledPaymentDates = collect($scheduledPaymentDatesArray);
        $scheduledPaymentDates->shift();

        $paymentStatuses = collect($scheduledPaymentDates->all())
            ->map(function ($date) use ($paymentDates, $lastLease) {
                $formattedDate = Carbon::parse($date);
                $leaseFee = $lastLease->leaseFees->first(function ($fee) use ($formattedDate) {
                    return Carbon::parse($fee->payment_date)->format('Y-m') === $formattedDate->format('Y-m');
                });

                $isPaid = collect($paymentDates)
                    ->map(function ($paymentDate) {
                        return Carbon::parse($paymentDate)->format('Y-m');
                    })
                    ->contains($formattedDate->format('Y-m'));

                return [
                    'date' => $formattedDate->locale('es')->isoFormat('D MMM Y'),
                    'amount_income' => optional($leaseFee)->amount_income ?? 0,
                    'is_paid' => $isPaid
                ];
            })
            ->toArray();
        // ------ //
        return [
            'isActive' => $isActive,
            'monthsLease' => $monthsLease,
            'terms' => $startDate->locale('es')->isoFormat('D MMM Y') . '~' . $endDate->locale('es')->isoFormat('D MMM Y'),
            'next_payment_date' => $nextPaymentDate->locale('es')->isoFormat('MMMM D, YYYY'),
            'paid_installments' => $paidInstallments,
            'totalAmountincome' => $totalAmountincome,
            'totalAmountLease' => $lastLease->total_income,
            'paymentStatuses' => $paymentStatuses,
            'lease' => $lastLease,
            'daysUntilNextPayment' => $daysUntilNextPayment,
        ];
    }

}
