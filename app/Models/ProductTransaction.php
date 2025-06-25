<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTransaction extends Model
{
    use HasFactory;

    protected $table = 'product_transactions';     
    protected $primaryKey = 'trans_id';            
    public $incrementing = true;                   

    protected $fillable = [
        'cust_ali_id',
        'user_id',
        'tarehe',             
        'trans_type',         
        'product_id',         
        'quantity',         
        'amount',             
        'buyer_name',  
        'status',     
    ];

    
    public function custAlizeti()
    {
        return $this->belongsTo(CustAlizeti::class, 'cust_ali_id', 'cust_ali_id');
    }

   
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

   
    public function product()
    {
        
        return $this->belongsTo(Product::class, 'product_id', 'product_id'); 
    }
}