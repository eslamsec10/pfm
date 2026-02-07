<?php

namespace App\Http\Controllers\property_transactions;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use App\Http\Controllers\Controller;

class PropertyTransactionSettingsController extends Controller
{
    public function agreementIndex()
    {
        $agreement_prefix    = optional(BusinessSetting::whereType('agreement_prefix')->first())->value;
        $agreement_digits    = optional(BusinessSetting::whereType('agreement_digits')->first())->value;
        $agreement_date_Data = optional(BusinessSetting::whereType('agreement_date')->first())->value;
        $agreement_color = optional(BusinessSetting::whereType('agreement_color')->first())->value;
        $agreement_date = ($agreement_date_Data != null) ? Carbon::createFromFormat('Y-m-d', $agreement_date_Data)->format('Y-m-d') : '';
        $agreement_expire_date  = optional(BusinessSetting::whereType('agreement_expire_date')->first())->value;
        $notifications = optional(BusinessSetting::whereType('agreement_notifications')->first())->value;
        $notifications = $notifications ? json_decode($notifications, true) : [];
        $data = [
            "agreement_digits"      => $agreement_digits,
            "agreement_prefix"      => $agreement_prefix,
            "agreement_date"      => $agreement_date,
            "agreement_expire_date"      => $agreement_expire_date,
            'agreement_color'           => $agreement_color,
            'notifications'             => $notifications,
        ];
        return view('admin-views.settings.agreement_settings', $data);
    }
    public function agreementUpdate(Request $request)
    {
        BusinessSetting::updateOrInsert(['type' => 'agreement_prefix'], [
            'value' => $request['agreement_prefix']
        ]);

        BusinessSetting::updateOrInsert(['type' => 'agreement_digits'], [
            'value' => $request['agreement_digits']
        ]);
        BusinessSetting::updateOrInsert(['type' => 'agreement_color'], [
            'value' => $request['agreement_color']
        ]);
        BusinessSetting::updateOrInsert(['type' => 'agreement_notifications'], [
            'value' => json_encode($request->notifications ?? [])
        ]);


        BusinessSetting::updateOrInsert(['type' => 'agreement_expire_date'], [
            'value' => $request['agreement_expire_date']
        ]);

        if ($request['agreement_date']) {
            $start_date = Carbon::createFromFormat('d/m/Y', $request['agreement_date'])->format('Y-m-d');
        }

        BusinessSetting::updateOrInsert(['type' => 'agreement_date'], [
            'value' => $start_date
        ]);
        return redirect()->back()->with('success', ui_change('setting_updated'));
    }
    public function bookingIndex()
    {
        $booking_prefix    = optional(BusinessSetting::whereType('booking_prefix')->first())->value;
        $booking_digits    = optional(BusinessSetting::whereType('booking_digits')->first())->value;
        $booking_date_Data = optional(BusinessSetting::whereType('booking_date')->first())->value;
        $booking_color = optional(BusinessSetting::whereType('booking_color')->first())->value;
        $booking_date = ($booking_date_Data != null) ? Carbon::createFromFormat('Y-m-d', $booking_date_Data)->format('Y-m-d') : '';
        $booking_expire_date  = optional(BusinessSetting::whereType('booking_expire_date')->first())->value;
        $notifications = optional(BusinessSetting::whereType('booking_notifications')->first())->value;
        $notifications = $notifications ? json_decode($notifications, true) : [];
        $data = [
            "booking_digits"            => $booking_digits,
            "booking_prefix"            => $booking_prefix,
            "booking_date"              => $booking_date,
            "booking_expire_date"       => $booking_expire_date,
            "booking_color"             => $booking_color,
            "notifications"             => $notifications,
        ];
        return view('admin-views.settings.booking_settings', $data);
    }
    public function bookingUpdate(Request $request)
    {

        BusinessSetting::updateOrInsert(['type' => 'booking_prefix'], [
            'value' => $request['booking_prefix']
        ]);

        BusinessSetting::updateOrInsert(['type' => 'booking_expire_date'], [
            'value' => $request['booking_expire_date']
        ]);

        BusinessSetting::updateOrInsert(['type' => 'booking_digits'], [
            'value' => $request['booking_digits']
        ]);
        BusinessSetting::updateOrInsert(['type' => 'booking_color'], [
            'value' => $request['booking_color']
        ]);
        if ($request['booking_date']) {
            $start_date = Carbon::createFromFormat('d/m/Y', $request['booking_date'])->format('Y-m-d');
        }

        BusinessSetting::updateOrInsert(['type' => 'booking_date'], [
            'value' => $start_date
        ]);
        BusinessSetting::updateOrInsert(['type' => 'booking_notifications'], [
            'value' => json_encode($request->notifications ?? [])
        ]);

        return redirect()->back()->with('success', ui_change('setting_updated'));
    }
    public function enquiryIndex()
    {
        $enquiry_prefix    = optional(BusinessSetting::whereType('enquiry_prefix')->first())->value;
        $enquiry_digits    = optional(BusinessSetting::whereType('enquiry_digits')->first())->value;
        $enquiry_date_Data = optional(BusinessSetting::whereType('enquiry_date')->first())->value;
        $enquiry_color = optional(BusinessSetting::whereType('enquiry_color')->first())->value;
        $enquiry_expire_date  = optional(BusinessSetting::whereType('enquiry_expire_date')->first())->value;
        $enquiry_date = ($enquiry_date_Data != null) ? Carbon::createFromFormat('Y-m-d', $enquiry_date_Data)->format('Y-m-d') : '';
         $notifications = optional(BusinessSetting::whereType('enquiry_notifications')->first())->value;
        $notifications = $notifications ? json_decode($notifications, true) : [];
        $data = [
            "enquiry_digits"      => $enquiry_digits,
            "enquiry_prefix"      => $enquiry_prefix,
            "enquiry_date"      => $enquiry_date,
            "enquiry_expire_date"      => $enquiry_expire_date,
            "enquiry_color"     => $enquiry_color,
            "notifications"     => $notifications,
        ];
        return view('admin-views.settings.enquiry_settings', $data);
    }
    public function enquiryUpdate(Request $request)
    {

        BusinessSetting::updateOrInsert(['type' => 'enquiry_prefix'], [
            'value' => $request['enquiry_prefix']
        ]);
        BusinessSetting::updateOrInsert(['type' => 'enquiry_expire_date'], [
            'value' => $request['enquiry_expire_date']
        ]);

        BusinessSetting::updateOrInsert(['type' => 'enquiry_digits'], [
            'value' => $request['enquiry_digits']
        ]);
        BusinessSetting::updateOrInsert(['type' => 'enquiry_color'], [
            'value' => $request['enquiry_color']
        ]);
        if ($request['enquiry_date']) {
            $start_date = Carbon::createFromFormat('d/m/Y', $request['enquiry_date'])->format('Y-m-d');
        }

        BusinessSetting::updateOrInsert(['type' => 'enquiry_date'], [
            'value' => $start_date
        ]);
         BusinessSetting::updateOrInsert(['type' => 'enquiry_notifications'], [
            'value' => json_encode($request->notifications ?? [])
        ]);
        return redirect()->back()->with('success', ui_change('setting_updated'));
    }
    public function proposalIndex()
    {
        $proposal_prefix    = optional(BusinessSetting::whereType('proposal_prefix')->first())->value;
        $proposal_digits    = optional(BusinessSetting::whereType('proposal_digits')->first())->value;
        $proposal_date = optional(BusinessSetting::whereType('proposal_date')->first())->value;
        $proposal_color = optional(BusinessSetting::whereType('proposal_color')->first())->value;
        $proposal_date = Carbon::createFromFormat('Y-m-d', $proposal_date)->format('Y-m-d');
        $proposal_expire_date  = optional(BusinessSetting::whereType('proposal_expire_date')->first())->value;
         $notifications = optional(BusinessSetting::whereType('proposal_notifications')->first())->value;
        $notifications = $notifications ? json_decode($notifications, true) : [];
        $data = [
            "proposal_digits"      => $proposal_digits,
            "proposal_prefix"      => $proposal_prefix,
            "proposal_date"      => $proposal_date,
            "proposal_expire_date"      => $proposal_expire_date,
            'proposal_color'            => $proposal_color,
            'notifications'             => $notifications,
        ];
        return view('admin-views.settings.proposal_settings', $data);
    }
    public function proposalUpdate(Request $request)
    {

        BusinessSetting::updateOrInsert(['type' => 'proposal_prefix'], [
            'value' => $request['proposal_prefix']
        ]);
        BusinessSetting::updateOrInsert(['type' => 'proposal_expire_date'], [
            'value' => $request['proposal_expire_date']
        ]);

        BusinessSetting::updateOrInsert(['type' => 'proposal_digits'], [
            'value' => $request['proposal_digits']
        ]);
        BusinessSetting::updateOrInsert(['type' => 'proposal_color'], [
            'value' => $request['proposal_color']
        ]);
        if ($request['proposal_date']) {
            $start_date = Carbon::createFromFormat('d/m/Y', $request['proposal_date'])->format('Y-m-d');
        }

        BusinessSetting::updateOrInsert(['type' => 'proposal_date'], [
            'value' => $start_date
        ]);
         BusinessSetting::updateOrInsert(['type' => 'proposal_notifications'], [
            'value' => json_encode($request->notifications ?? [])
        ]);
        return redirect()->back()->with('success', ui_change('setting_updated'));
    }


    public function investmentIndex()
    {
        $investment_prefix    = optional(BusinessSetting::whereType('investment_prefix')->first())->value;
        $investment_digits    = optional(BusinessSetting::whereType('investment_digits')->first())->value;
        $data = [
            "investment_digits"      => $investment_digits,
            "investment_prefix"      => $investment_prefix,
        ];
        return view('admin-views.settings.investment_settings', $data);
    }
    public function investmentUpdate(Request $request)
    {

        BusinessSetting::updateOrInsert(['type' => 'investment_prefix'], [
            'value' => $request['investment_prefix']
        ]);

        BusinessSetting::updateOrInsert(['type' => 'investment_digits'], [
            'value' => $request['investment_digits']
        ]);


        return redirect()->back()->with('success', ui_change('settings_updated'));
    }
}
