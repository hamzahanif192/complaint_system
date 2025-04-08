<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintModel extends Model
{
    //
   protected $table = 'complaints';
   protected $primaryKey = "id";
   public function assignedDepartment()
{
    return $this->belongsTo(Department::class, 'assigned_department_id');
}
public function assignedEmployee()
{
    return $this->belongsTo(User::class, 'assigned_employee_id');
}

}
