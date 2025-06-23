<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers'; 
    protected $primaryKey = 'cust_id'; 
    public $incrementing = true; 

    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
    ];

    
    public function custAlizeti()
    {
        return $this->hasMany(CustAlizeti::class, 'cust_id', 'cust_id');
    }

    
}