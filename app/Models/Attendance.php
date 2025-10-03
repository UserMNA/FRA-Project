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

    public function employee()
    {
        // Link this model's 'employee_id' column to the 'employee_id' column on the Employee model
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}