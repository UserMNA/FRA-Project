<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'employee_id',
        'title',
        'image_path',
    ];

    public function attendanceRecords()
    {
        // We assume the 'attendance' table has a column named 'employee_id'
        // that matches this model's 'employee_id' column.
        return $this->hasMany(Attendance::class, 'employee_id', 'employee_id');
    }
}
    