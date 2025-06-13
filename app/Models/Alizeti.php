<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alizeti extends Model
{
    use HasFactory;

    // Specify the table name if it differs from the model name
    protected $table = 'alizeti';

    // Specify the primary key
    protected $primaryKey = 'ali_id';

    // Disable auto-incrementing if necessary (optional)
    public $incrementing = true;

    // Define the attributes that are mass assignable
    protected $fillable = [
        'tarehe',
        'user_id',
        'batch_no',
        'al_kilogram',
        'gunia_total',
        'price_per_kilo',
        'total_price',
 
    ];

    public function uzalishajiz()
    {
        return $this->hasMany(Uzalishaji::class, 'alizeti_id', 'ali_id');
    }
        
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function price()
    {
        return $this->hasOne(Price::class, 'alizeti_id','ali_id');
    }

    public function uchujaji()
    {
        return $this->hasMany(Uchujaji::class, 'alizeti_id','ali_id');
    }

    public function stock()
    {
        return $this->hasOne(Stock::class, 'alizeti_id', 'ali_id');
        
    }

    public function mauzo()
    {
        return $this->hasOne(Mauzo::class, 'alizeti_id','ali_id');
    }


}
