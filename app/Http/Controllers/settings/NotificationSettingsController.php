<?php

namespace App\Http\Controllers\settings;

use App\Http\Controllers\Controller;
use App\Models\NotificationSettings;
use Illuminate\Http\Request;

class NotificationSettingsController extends Controller
{
        public function index(){
        $notification_settings = NotificationSettings::get();
        $data = [
            "notification_settings"      => $notification_settings,
        ];
        return view('admin-views.settings.notification_settings' ,$data);
    }
    public function update(Request $request){

        NotificationSettings::updateOrInsert(['key' => 'authkey'], [
            'value' => $request['authkey']
        ]);

        NotificationSettings::updateOrInsert(['key' => 'appkey'], [
            'value' => $request['appkey']
        ]);
        NotificationSettings::updateOrInsert(['key' => 'email'], [
            'value' => $request['email']
        ]); 
        return redirect()->back()->with('success',ui_change('settings_updated'));
    }
}
