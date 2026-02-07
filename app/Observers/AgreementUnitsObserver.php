<?php

namespace App\Observers;

use App\Models\AgreementUnits;

class AgreementUnitsObserver
{
    /**
     * Handle the AgreementUnits "created" event.
     */
    public function created(AgreementUnits $agreementUnits): void
    {
        //
    }

    /**
     * Handle the AgreementUnits "updated" event.
     */
    public function updated(AgreementUnits $agreementUnits): void
    {
        //
    }

    /**
     * Handle the AgreementUnits "deleted" event.
     */
    public function deleted(AgreementUnits $agreementUnits): void
    {
        //
    }

    /**
     * Handle the AgreementUnits "restored" event.
     */
    public function restored(AgreementUnits $agreementUnits): void
    {
        //
    }

    /**
     * Handle the AgreementUnits "force deleted" event.
     */
    public function forceDeleted(AgreementUnits $agreementUnits): void
    {
        //
    }
}
