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
        'name',
        'description',
        'amount',
        'description',
        'work_start_date',
        'work_end_date',
        'staatus_id'
    ];

    public function machinery()
    {
        return $this->belongsTo(Machinery::class, 'machinery_id');
    }


}