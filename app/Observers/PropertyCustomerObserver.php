<?php

namespace App\Observers;

use App\Models\Company;
use App\Models\PropertyCustomer;
use Exception;
use Illuminate\Support\Facades\Log;

class PropertyCustomerObserver
{
    /**
     * Handle the PropertyCustomer "created" event.
     */
    public function created(PropertyCustomer $propertyCustomer): void
    {
        try {
             
            if (!$propertyCustomer->whatsapp_no && !$propertyCustomer->whatsapp_dail_code) { 
                return;
            }

            $company = (new Company())->setConnection('tenant')->first();
            $message  = "Hello Dear " . ($propertyCustomer->name ?? $propertyCustomer->company_name) . " welcome to " . $company->name;
            $options['to'] = '+'.$propertyCustomer->whatsapp_dail_code . $propertyCustomer->whatsapp_no;
              
            $options['message'] = $message;  
            sendWhatsApp($options);
        } catch (Exception $e) {
            Log::error("Failed to send WhatsApp message for PropertyCustomer ID " . $propertyCustomer->id . ": " . $e->getMessage());
            // Handle the exception, log it, or ignore it based on your needs
        }
    }

    /**
     * Handle the PropertyCustomer "updated" event.
     */
    public function updated(PropertyCustomer $propertyCustomer): void
    {
        //
    }

    /**
     * Handle the PropertyCustomer "deleted" event.
     */
    public function deleted(PropertyCustomer $propertyCustomer): void
    {
        //
    }

    /**
     * Handle the PropertyCustomer "restored" event.
     */
    public function restored(PropertyCustomer $propertyCustomer): void
    {
        //
    }

    /**
     * Handle the PropertyCustomer "force deleted" event.
     */
    public function forceDeleted(PropertyCustomer $propertyCustomer): void
    {
        //
    }
}
