<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filtering extends Model
{
    use HasFactory;

    protected $table = 'filtering';    
    protected $primaryKey = 'filter_id';  
    public $incrementing = true;           

    protected $fillable = [
        'cust_ali_id',
        'user_id',
        'tarehe',
        'crude_oil',         
        'refined_oil',       
        'lami_kg',           
        'ugido_kg',          
        'initial_units',
        'final_units',
        'unit_used',         
        'cost_used',         
    ];

    
    public function custAlizeti()
    {
        return $this->belongsTo(CustAlizeti::class, 'cust_ali_id', 'cust_ali_id');
    }

    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    
    public function getUnitUsedAttribute($value)
    {
        
        if (!is_null($value)) { 
            return $value;
        }
        
        if ($this->initial_units !== null && $this->final_units !== null) {
            return max(0, $this->initial_units - $this->final_units);
        }
        return 0.00; 
    }
}