<?php

namespace App\Observers;

use App\Models\BookingUnits;

class BookingUnitsObserver
{
    /**
     * Handle the BookingUnits "created" event.
     */
    public function created(BookingUnits $bookingUnits): void
    {
        //
    }

    /**
     * Handle the BookingUnits "updated" event.
     */
    public function updated(BookingUnits $bookingUnits): void
    {
        //
    }

    /**
     * Handle the BookingUnits "deleted" event.
     */
    public function deleted(BookingUnits $bookingUnits): void
    {
        //
    }

    /**
     * Handle the BookingUnits "restored" event.
     */
    public function restored(BookingUnits $bookingUnits): void
    {
        //
    }

    /**
     * Handle the BookingUnits "force deleted" event.
     */
    public function forceDeleted(BookingUnits $bookingUnits): void
    {
        //
    }
}
