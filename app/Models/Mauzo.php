<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mauzo extends Model
{
    use HasFactory;

    protected $table = 'mauzo';
    protected $primaryKey = 'mauzo_id';

    protected $fillable = [
        'tarehe',        
        'price',
        'discount',        
        'alizeti_id',
        'user_id',
        'prices_id',
        'total_price',
        'sells_type',
        'sale_type',
        'quantity',
        'is_confirmed',
        'payment_status',
        'payment_id',
        'product_id',
        'customer_name', 
        'phone',         
    ];

    
    public function alizeti()
    {
        return $this->belongsTo(Alizeti::class, 'alizeti_id');
    }

    public function price()
    {
        return $this->belongsTo(Price::class, 'prices_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class,'payment_id','payment_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function customerDebit()
    {
        return $this->hasOne(CustomerDebit::class, 'debit_id', 'debit_id');
    }

}
