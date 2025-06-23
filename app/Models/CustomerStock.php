<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerStock extends Model
{
    use HasFactory;

    protected $table = 'customer_stocks';    
    protected $primaryKey = 'stock_id';      
    public $incrementing = true;             

    protected $fillable = [
        'ali_id',           
        'uncleaned_kg',
        'cleaned_kg',
        'crude_oil',
        'refined_oil',
        'mashudu_kg',
        'lami_kg',
        'ugido',
    ];

    
    public function custAlizeti()
    {
        return $this->belongsTo(CustAlizeti::class, 'ali_id', 'cust_ali_id');
    }
}