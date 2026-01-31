<?php

namespace App\Exports;

use App\Models\ServiceMaster;
use App\Models\UnitManagement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class UnitManagementExport implements FromCollection, WithHeadings, WithEvents
// class UnitManagementExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $services;

    public function __construct()
    {
        $this->services = ServiceMaster::pluck('name')->toArray();
    }
    public function collection()
    {
        return UnitManagement::with(
            'property_unit_management:id,name,code',
            'block_unit_management:id,block_id',
            'block_unit_management.block:id,name,code',
            'floor_unit_management:id,floor_id',
            'floor_unit_management.floor_management_main:id,name,code',
            'unit_management_main:id,name,code'
        )->get()->map(function ($unit) {
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
                optional($unit->unit_management_main)->name ?? '', // Unit
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
                $sheet = $event->sheet->getDelegate();
 
                $column = 'T';
                $lastRow = $sheet->getHighestRow();
 
                $servicesString = implode(',', $this->services);
 
                for ($row = 2; $row <= $lastRow; $row++) {
                    $sheet->getCell($column . $row)
                        ->getDataValidation()
                        ->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
                        ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP)
                        ->setAllowBlank(true)
                        ->setShowInputMessage(true)
                        ->setShowErrorMessage(true)
                        ->setShowDropDown(true)
                        ->setErrorTitle('Invalid input')
                        ->setError('Value is not in list')
                        ->setFormula1('"' . $servicesString . '"');  
                }
            },
        ];
    }
}
