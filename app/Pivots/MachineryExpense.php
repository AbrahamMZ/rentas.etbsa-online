<?php

namespace App\Pivots;

use App\Models\ExpenseCatalog;
use App\Models\Machinery;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MachineryExpense extends Pivot
{
    protected $table = 'machinery_expense_pivot_table';
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;
    public $timestamps = true;


    protected $fillable = [
        'machinery_id',
        'expense_id',
        'name',
        'reference',
        'amount',
        'charge_date',
    ];

    public function machinery()
    {
        return $this->belongsTo(Machinery::class, 'machinery_id');
    }

    public function expense()
    {
        return $this->belongsTo(ExpenseCatalog::class, 'expense_id');
    }
}