<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $primaryKey = 'loan_id';

    protected $fillable = [
        'lender_name',
        'loan_date',
        'amount',
        'outstanding_balance',
        'due_date',
        'interest_rate',
        'notes',
        'original_loan_amount',
        'loan_status',
        'is_confirmed',
    ];

    public function loanPayments() 
    {
        return $this->hasMany(LoanPayment::class, 'loan_id', 'loan_id');
    }
}
