<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Uchekechaji extends Model
{
    use HasFactory;    
    protected $table = 'uchekechaji';
    protected $primaryKey = 'uchek_id';

    protected $fillable = [
        'tarehe',
        'alizeti_id',
        'user_id',
        'uncleaned_amount',
        'makapi_amount',
        'cleaned_amount',
        'initial_unit',      
        'final_unit',        
    ];

    
    public function alizeti()
    {        
        return $this->belongsTo(Alizeti::class, 'alizeti_id', 'ali_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getUnitsUsedAttribute(): float
    {
        
        $initial = (float) $this->initial_unit;
        $final = (float) $this->final_unit;

        if ($initial < $final) {
            return 0.00;
        }

        return round($initial - $final, 2);
    }

    
    protected $appends = ['units_used'];
    
}