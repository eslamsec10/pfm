<?php

namespace App\Observers;
 
use Carbon\Carbon;
use App\Models\Company;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Log;
use App\Models\EnquiryUnitSearchDetails;
use App\Mail\GeneralNotificationTransactionMail;
use Illuminate\Support\Facades\Mail;



class EnquiryUnitsObserver
{
    /**
     * Handle the EnquiryUnitSearchDetails "created" event.
     */
    public function created(EnquiryUnitSearchDetails $enquiryUnitSearchDetails): void
    {
        $tenant = $enquiryUnitSearchDetails->main_enquiry->tenant ?? null;
        if (!$tenant ) {
            return;
        }

        $notifications = BusinessSetting::where('type', 'enquiry_notifications')->value('value');
        $company = (new Company())->setConnection('tenant')->select('name', 'currency_code', 'decimals')->first();

        $unitName = $enquiryUnitSearchDetails?->unit_management?->unit_management_main->name ?? '-';
        $rent     = number_format($enquiryUnitSearchDetails->rent_amount ?? 0, $company->decimals ?? 2);
        $date = $enquiryUnitSearchDetails->period_from
            ? Carbon::parse($enquiryUnitSearchDetails->period_from)->format('Y-m-d')
            : '-';
        $message  = "Hello Dear " . ($tenant->name ?? $tenant->company_name) . " you have a new enquiry in " . $company->name;

        $notifications = $notifications ? json_decode($notifications, true) : [];

        if (empty($notifications)) {
            return;
        }

        if (in_array('system', $notifications)) {
        }

        if (in_array('email', $notifications)) {
            $table[] = [
                'unit' => $unitName,
                'rent' => $rent . " ($company->currency_code)",
                'date' => $date
            ];

            Mail::to($tenant->email1)->send(
                new GeneralNotificationTransactionMail(
                    'New Enquiry',
                    $message,
                    $tenant,
                    $company,
                    $table
                )
            );
        }

        if (in_array('whatsapp', $notifications)) {

            if (!$tenant->whatsapp_no && !$tenant->contact_no) {
                Log::warning("Tenant has no WhatsApp or contact number: Tenant ID " . $tenant->id);
                return;
            }


            $options['to'] = $tenant->whatsapp_no ?? $tenant->contact_no;
            $options['message'] = $message;

            if (in_array('whatsapp', $notifications)) {



                $table  = "\n-----------------------------\n";
                $table .= "Unit Name | Rent | Start Date\n";
                $table .= "-----------------------------\n";
                $table .= "$unitName | $rent ($company->currency_code ) | $date\n";
                $table .= "-----------------------------\n";

                $message  = "Hello Dear " . ($tenant->name ?? $tenant->company_name) . "\n";
                $message .= "You have a new enquiry in " . $company->name . "\n";
                $message .= $table;
                $options['message'] = $message;
                sendWhatsApp($options);
            }
        }
    }

    /**
     * Handle the EnquiryUnitSearchDetails "updated" event.
     */
    public function updated(EnquiryUnitSearchDetails $enquiryUnitSearchDetails): void
    {
        //
    }

    /**
     * Handle the EnquiryUnitSearchDetails "deleted" event.
     */
    public function deleted(EnquiryUnitSearchDetails $enquiryUnitSearchDetails): void
    {
        //
    }

    /**
     * Handle the EnquiryUnitSearchDetails "restored" event.
     */
    public function restored(EnquiryUnitSearchDetails $enquiryUnitSearchDetails): void
    {
        //
    }

    /**
     * Handle the EnquiryUnitSearchDetails "force deleted" event.
     */
    public function forceDeleted(EnquiryUnitSearchDetails $enquiryUnitSearchDetails): void
    {
        //
    }
}
