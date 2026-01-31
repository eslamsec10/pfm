<?php

namespace App\Events;

use App\Models\PropertyManagement;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PropertyManagementCreated
{
    use Dispatchable, SerializesModels;

    public $property;

    public function __construct(PropertyManagement $property)
    {
        $this->property = $property;
    }
}
