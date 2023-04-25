<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class MachineryServiceExpenses extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'machinery_service_expenses';

    protected $fillable = [
        'machinery_id',
        'status_id',
        'reference',
        'name',
        'description',
        'amount',
        'applied_date',
    ];

    public function machinery()
    {
        return $this->belongsTo(Machinery::class, 'machinery_id');
    }


}