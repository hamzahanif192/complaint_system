<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintTracking extends Model
{
    protected $fillable = [
        'complaint_id', 'action_type', 'comment', 'performed_by'
    ];

    public function complaint()
    {
        return $this->belongsTo(ComplaintModel::class, 'complaint_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
