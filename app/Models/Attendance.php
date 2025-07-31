<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id', 
        'name', 
        'label', 
        'confidence', 
        'scanned_at'
    ];
}