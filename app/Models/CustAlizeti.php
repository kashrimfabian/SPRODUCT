<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustAlizeti extends Model
{
    use HasFactory;

    protected $table = 'cust_alizeti';    
    protected $primaryKey = 'cust_ali_id'; 
    public $incrementing = true;         

    protected $fillable = [
        'cust_id',          
        'batch_no',         
        'uncleaned_kg',     
        'tarehe',           
        'status',          
    ];

    
    public function customer()
    {
        
        return $this->belongsTo(Customer::class, 'cust_id', 'cust_id');
    }

    public function customerStock()
    {        
        return $this->hasOne(CustomerStock::class, 'ali_id', 'cust_ali_id');
    }

    public function cleaningOperations()
    {
        return $this->hasMany(CustClean::class, 'cust_ali_id', 'cust_ali_id');
    }

    public function ukamuajiOperations()
    {
        return $this->hasMany(Ukamuaji::class, 'cust_ali_id', 'cust_ali_id');
    }

    public function filteringOperations()
    {
        return $this->hasMany(Filtering::class, 'cust_ali_id', 'cust_ali_id');
    }
}