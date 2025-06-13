<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Uchujaji extends Model
{
    use HasFactory;

    protected $table = 'uchujaji';
    protected $primaryKey = 'uchujaji_id';

    protected $fillable = [
        'alizeti_id',
        'created_by',
        'updated_by',
        'tarehe',
        'mafuta_machafu',
        'mafuta_masafi',
        'ugido',
        'batch_no',
        'lami',
        'initial_unit',   
        'final_unit',     
    ];
    public function alizeti()
    {
        return $this->belongsTo(Alizeti::class, 'alizeti_id');
    }

   
    public function user()
    {
       return $this->belongsTo(User::class, 'created_by', 'id');
    }

    
    public function users()
    {
       return $this->belongsTo(User::class, 'updated_by', 'id');
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