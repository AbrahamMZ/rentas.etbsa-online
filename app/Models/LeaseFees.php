<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaseFees extends Model
{
    use HasFactory;

    protected $table = 'lease_fees';

    protected $fillable = [
        'folio',
        'amount_income',
        'payment_date',
        'note',
        'lease_id',
    ];

    public function leaseIncome()
    {
        return $this->belongsTo(LeaseIncomes::class, 'lease_id');
    }
}
