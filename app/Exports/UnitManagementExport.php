<?php

namespace App\Exports;

use App\Models\Company;
use App\Models\ServiceMaster;
use App\Models\UnitManagement;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class UnitManagementExport implements FromCollection, WithHeadings, WithEvents
// class UnitManagementExport implements FromCollection, WithHeadings

{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $services;
    protected $currency;
    protected $unitLedgers = [];
    protected $service_ledger;
    protected $invoice_frequency = [
        1 => 'daily',
        2 => 'monthly',
        3 => 'bi_monthly',
        4 => 'quarterly',
        5 => 'half_yearly',
        6 => 'yearly',
    ];

    public function __construct()
    {
        $this->services = ServiceMaster::pluck('name')->toArray();
        $this->currency = Company::first()->currency_code ?? 'SAR';
    }
    public function collection()
    {
        return UnitManagement::with(
            'property_unit_management:id,name,code',
            'block_unit_management:id,block_id',
            'block_unit_management.block:id,name,code',
            'floor_unit_management:id,floor_id',
            'floor_unit_management.floor_management_main:id,name,code',
            'unit_management_main:id,name,code',
            'unit_ledger:id,unit_management_id,name,group_id,property_management_id'
        )->get()->map(function ($unit) {

            $ledgerName = $unit->unit_ledger
                ? $unit->unit_ledger?->name
                : ' '; 
            return [
                '',
                '',
                '',

                optional($unit->property_unit_management)->name ?? '', // Property Name
                optional($unit->property_unit_management)->code ?? '', // Prop Code

                optional(optional($unit->block_unit_management)->block)->name ?? '', // Block Name
                optional(optional($unit->block_unit_management)->block)->code ?? '', // Block Code

                optional(optional($unit->floor_unit_management)->floor_management_main)->name ?? '', // Floor Name
                optional(optional($unit->floor_unit_management)->floor_management_main)->code ?? '', // Floor Code
                optional($unit->unit_management_main)->name ?? '',                                   // Unit
                '',
                '',
                '',
                $ledgerName,
                '',
                '',
                '',
                $this->currency,
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Date',
            'Lease Agreement No',
            'Tenant Name',
            'Property Name',
            'Prop Code',
            'Block Name',
            'Block Code',
            'Floor Name',
            'Floor Code',
            'Unit',
            'Description',
            'Lease Start Date',
            'Lease End Date',
            'Rental Income Ledger',
            'Invoice Frequency', //Frequency
            'Rent Start Date',
            'Rent End Date',
            'Currency',
            'Rent per Month',
            'Service Type',
            'Service Frequency',
            'Service Start Date',
            'Service End Date',
            'Service Amount in BD (Exlusive VAT)',
            'Security Deposit',
            'Lease Break Date',
            'Notice Period',
            // 'Unit Description',
            // 'Unit Type',
            // 'Unit Condition',
            // 'View',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet   = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // =========================
                // Invoice Frequency (O)
                // =========================
                $invoiceColumn   = 'O';
                $frequencies     = array_values($this->invoice_frequency);
                $frequencyString = implode(',', $frequencies);

                for ($row = 2; $row <= $lastRow; $row++) {
                    $sheet->getCell($invoiceColumn . $row)
                        ->getDataValidation()
                        ->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
                        ->setAllowBlank(true)
                        ->setShowDropDown(true)
                        ->setFormula1('"' . $frequencyString . '"');
                }

                // =========================
                // Service Type (S) -> services
                // =========================
                $serviceTypeColumn = 'T';
                $servicesString    = implode(',', $this->services);

                for ($row = 2; $row <= $lastRow; $row++) {
                    $sheet->getCell($serviceTypeColumn . $row)
                        ->getDataValidation()
                        ->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
                        ->setAllowBlank(true)
                        ->setShowDropDown(true)
                        ->setFormula1('"' . $servicesString . '"');
                }

                // =========================
                // Service Frequency (U)
                // =========================
                $serviceFreqColumn = 'U';

                for ($row = 2; $row <= $lastRow; $row++) {
                    $sheet->getCell($serviceFreqColumn . $row)
                        ->getDataValidation()
                        ->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
                        ->setAllowBlank(true)
                        ->setShowDropDown(true)
                        ->setFormula1('"' . $frequencyString . '"');
                }
            },
        ];
    }
}
