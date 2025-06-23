<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ukamuaji extends Model
{
    use HasFactory;

    protected $table = 'ukamuaji'; 
    protected $primaryKey = 'uk_id';       
    public $incrementing = true;            

    protected $fillable = [
        'cust_ali_id',
        'user_id',
        'tarehe',
        'cleaned_kg',         
        'crude_oil',          
        'mashudu_kg',         
        'initial_units',
        'final_units',
        'unit_used',          
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
        
        if (!is_null($value) && $value > 0) {
            return $value;
        }
        
        if ($this->initial_units !== null && $this->final_units !== null) {
            return max(0, $this->initial_units - $this->final_units);
        }
        return 0.00; 
    }
}