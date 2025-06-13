<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Uzalishaji extends Model
{
    use HasFactory;
   
    protected $table = 'uzalishaji';

    protected $primaryKey = 'uzalishaji_id';

    protected $fillable = [
        'tarehe',
        'alizeti_kgm',
        'mafuta_machafu',
        'mashudu',
        'created_by',
        'updated_by',
        'alizeti_id',
        'initial_unit',   
        'final_unit', 
    ];

    
    public function alizeti()
    {
        return $this->belongsTo(Alizeti::class, 'alizeti_id');
    }

    protected $hidden = [
        'created_at', 'updated_at',
    ];

    
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
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
