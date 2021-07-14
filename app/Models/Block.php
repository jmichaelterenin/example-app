<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $fillable = [
        'starts_at',
        'length',
        'block_type',
        'clinic_id',
        'patient_id'
    ];
}