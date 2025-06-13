<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerDebitPayment extends Model
{
    use HasFactory;

    protected $primaryKey = 'debitpay_id';

    protected $fillable = [
        'debit_id',
        'payment_date',
        'amount',
        'notes',
    ];

    public function customerDebit()
    {
        return $this->belongsTo(CustomerDebit::class, 'debit_id');
    }
}
