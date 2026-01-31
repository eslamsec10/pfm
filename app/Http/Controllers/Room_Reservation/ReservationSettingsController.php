<?php

namespace App\Http\Controllers\Room_Reservation;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\ReservationSettings;
use App\Http\Controllers\Controller;

class ReservationSettingsController extends Controller
{
     public function room_reservation(){
        $renewal_reminder    = optional(ReservationSettings::where('setting_name', 'renewal_reminder')->first())->setting_value;
        $booked_color    = optional(ReservationSettings::where('setting_name', 'booked_color')->first())->setting_value;
        $show_agreement    = optional(ReservationSettings::where('setting_name', 'show_agreement')->first())->setting_value;
        // $room_reservation_digits    = optional(ReservationSettings::whereType('room_reservation_digits')->first())->value ;
        // $room_reservation_date_Data = optional(ReservationSettings::whereType('room_reservation_date')->first())->value  ;
        // $room_reservation_date = ($room_reservation_date_Data != null) ? Carbon::createFromFormat('Y-m-d', $room_reservation_date_Data)->format('Y-m-d') : '';
        // $room_reservation_expire_date  = optional(ReservationSettings::whereType('room_reservation_expire_date')->first())->value  ;

        $data = [ 
            'renewal_reminder' => $renewal_reminder,
            'booked_color'      => $booked_color,
            'show_agreement'      => $show_agreement,
            ];
        return view('admin-views.settings.room_reservation_settings' ,$data);
    }
    public function room_reservation_update(Request $request){

        ReservationSettings::updateOrInsert(['setting_name' => 'renewal_reminder'], [
            'setting_value' => $request['renewal_reminder']
        ]);
        ReservationSettings::updateOrInsert(['setting_name' => 'booked_color'], [
            'setting_value' => $request['booked_color']
        ]);
        ReservationSettings::updateOrInsert(['setting_name' => 'show_agreement'], [
            'setting_value' => $request['show_agreement']
        ]);

     
        return redirect()->back()->with('success',ui_change('setting_updated'));
    }
}
