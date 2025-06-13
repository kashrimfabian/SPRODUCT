<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products'; 
    protected $primaryKey = 'product_id'; 
    protected $fillable = [
        'name',
        'description',
        'is_active',
        
    ];

    
    public function mauzos()
    {
        return $this->hasMany(Mauzo::class, 'product_id', 'product_id');
    }
}