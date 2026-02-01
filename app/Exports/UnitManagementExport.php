<?php
namespace App\Exports;

use App\Models\Company;
use App\Models\ServiceMaster;
use App\Models\UnitManagement;
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
    protected $invoice_frequency = [
        1 => 'daily',
        2 => 'monthly',
        3 => 'bi_monthly',
        4 => 'quarterly',
        5 => 'half_yearly',
        6 => 'yearly',
    ];
    // protected $invoice_frequency = [
    //     0 => ['select', 'select_rent_mode'],
    //     1 => ['daily', 'per day'],
    //     2 => ['monthly', 'per month'],
    //     3 => ['bi_monthly', 'every two months', 'bimonthly'],
    //     4 => ['quarterly', 'every 3 months'],
    //     5 => ['half_yearly', 'semi annual', 'semi-annual'],
    //     6 => ['yearly', 'annually', 'per year'],
    // ];
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
                optional($unit->unit_management_main)->name ?? '',                                   // Unit
                '',
                '',
                '',
                '',
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

            // =========================
            // Invoice Frequency (O = 15)
            // =========================
            $invoiceColumn  = 'O';
            $lastRow        = $sheet->getHighestRow();
            $frequencies    = array_values($this->invoice_frequency);
            $frequencyString = implode(',', $frequencies);

            for ($row = 2; $row <= $lastRow; $row++) {
                $sheet->getCell($invoiceColumn . $row)
                    ->getDataValidation()
                    ->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
                    ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP)
                    ->setAllowBlank(true)
                    ->setShowInputMessage(true)
                    ->setShowErrorMessage(true)
                    ->setShowDropDown(true)
                    ->setErrorTitle('Invalid input')
                    ->setError('Value is not in list')
                    ->setFormula1('"' . $frequencyString . '"');
            }

            // =========================
            // Service Frequency (T = 20)
            // =========================
            $serviceColumn  = 'T';
            $servicesString = implode(',', $this->services);

            for ($row = 2; $row <= $lastRow; $row++) {
                $sheet->getCell($serviceColumn . $row)
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
