<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MeetingRoomBooking extends Model
{
    use HasFactory;

      public function meetingRoom()
    {
        return $this->belongsTo(MeetingRoom::class);
    }
 
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
 
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}
