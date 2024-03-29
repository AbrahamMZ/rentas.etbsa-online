<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaseIncomes extends Model
{
    use HasFactory;
    protected $table = 'lease_incomes';

    protected $fillable = [
        'contract_lease',
        'reference',
        'term_lease',
        'term_in_days',
        'amount',
        'daily_fee',
        'balance',
        'start_date',
        'end_date',
        'total_income',
        'machinery_id',
    ];

    public function machinery()
    {
        return $this->belongsTo(Machinery::class, 'machinery_id');
    }

    public function leaseFees()
    {
        return $this->hasMany(LeaseFees::class, 'lease_id', 'id');
    }
}