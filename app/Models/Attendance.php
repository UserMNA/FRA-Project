<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id', 
        'name', 
        'folder_name',
        'label', 
        'title', 
        'confidence', 
        'scanned_at'
    ];
}