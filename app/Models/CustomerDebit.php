<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDebit extends Model
{
    protected $primaryKey = 'debit_id';

    protected $fillable = [
        'mauzo_id',
        'customer_name',
        'phone',
        'total_amount',
        'amount_paid',
        'balance',
        'debt_status',
    ];

    public function mauzo()
    {
        return $this->belongsTo(Mauzo::class, 'mauzo_id', 'mauzo_id');
    }

    public function payments()
    {
        return $this->hasMany(CustomerDebitPayment::class, 'debit_id', 'debit_id');
    }
}
