<?php

namespace App\Observers;

use App\Models\ProposalUnits;

class ProposalUnitsObserver
{
    /**
     * Handle the ProposalUnits "created" event.
     */
    public function created(ProposalUnits $proposalUnits): void
    {
        //
    }

    /**
     * Handle the ProposalUnits "updated" event.
     */
    public function updated(ProposalUnits $proposalUnits): void
    {
        //
    }

    /**
     * Handle the ProposalUnits "deleted" event.
     */
    public function deleted(ProposalUnits $proposalUnits): void
    {
        //
    }

    /**
     * Handle the ProposalUnits "restored" event.
     */
    public function restored(ProposalUnits $proposalUnits): void
    {
        //
    }

    /**
     * Handle the ProposalUnits "force deleted" event.
     */
    public function forceDeleted(ProposalUnits $proposalUnits): void
    {
        //
    }
}
