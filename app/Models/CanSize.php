<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CanSize extends Model
{
    use HasFactory;

    protected $primaryKey = 'size';
    public $incrementing = false; // ENUM type, no auto-increment
    protected $fillable = ['size', 'price_per_can'];
    public $timestamps = false;
}
