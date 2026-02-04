<?php
namespace App\Http\Controllers\import;

use App\Exports\UnitManagementExport;
use App\Http\Controllers\Controller;
use App\Imports\ContractTemplate;
use App\Models\Agreement;
use App\Models\AgreementDetails;
use App\Models\AgreementUnits;
use App\Models\AgreementUnitsService;
use App\Models\Block;
use App\Models\BlockManagement;
use App\Models\CountryMaster;
use App\Models\Floor;
use App\Models\FloorManagement;
use App\Models\PropertyManagement;
use App\Models\ServiceMaster;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\UnitManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ContractImportController extends Controller
{
    protected array $headerMap = [
        'Date'                                => 'date',
        'Lease Agreement No'                  => 'lease_agreement_no',
        'Tenant Name'                         => 'tenant_name',
        'Property Name'                       => 'property_name',
        'Prop Code'                           => 'prop_code',
        'Block Name'                          => 'block_name',
        'Block Code'                          => 'block_code',
        'Floor Name'                          => 'floor_name',
        'Floor Code'                          => 'floor_code',
        'Unit'                                => 'unit',
        'Description'                         => 'description',
        'Lease Start Date'                    => 'lease_start_date',
        'Lease End Date'                      => 'lease_end_date',
        'Rental Income Ledger'                => 'rental_income_ledger',
        'Invoice Frequency'                   => 'invoice_frequency',
        'Rent Start Date'                     => 'rent_start_date',
        'Rent End Date'                       => 'rent_end_date',
        'Currency'                            => 'currency',
        'Rent per Month'                      => 'rent_per_month',
        'Service Frequency'                   => 'service_frequency',
        'Service Start Date'                  => 'service_start_date',
        'Service End Date'                    => 'service_end_date',
        'Service Amount in BD (Exlusive VAT)' => 'service_amount_in_bd_exlusive_vat',
        'Security Deposit'                    => 'security_deposit',
        'Lease Break Date'                    => 'lease_break_date',
        'Notice Period'                       => 'notice_period',
    ];
    protected array $requiredFields = [
        'lease_agreement_no',
        'tenant_name',
        'property_name',
        'block_name',
        'floor_name',
        'unit',
        'lease_start_date',
        'lease_end_date',
        'rent_start_date',
        'rent_end_date',
        'currency',
        'rent_per_month',
    ];

    public function import_page(Request $request)
    {
 
        $instructions = '
        <p>1. ' . ui_change('Download_the_file_format_and_fill_the_correct_Data.') . '</p>
        <p>2. ' . ui_change('you_can_download_the_example_file_to_understand_how_the_data_must_be_filled.') . '</p>
        <p>3. ' . ui_change('once_you_have_downloaded_and_filled_the_format_file') . ', ' . ui_change('upload_it_in_the_form_below_and_submit.') . '</p>

    ';
        //      <p>4. ' . ui_change('after_uploading_products_you_need_to_edit_them_and_set_product_images_and_choices.') . '</p>
        //     <p>5. ' . ui_change('you_can_get_brand_and_category_id_from_their_list_please_input_the_right_ids.') . '</p>
        //     <p>6. ' . ui_change('you_can_upload_your_product_images_in_product_folder_from_gallery_and_copy_image_path.') . '</p>
        $data = [
            'instructions' => $instructions,
            'file'         => 'agreement',
            'file_name'    => 'agreement',
        ];

        return view('import_excel.upload_page', $data);
    }
    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        $collection = Excel::toCollection(null, $request->file('file'))->first();

        $excelHeaders = $collection->first()->toArray();

        $mappedHeaders = collect($excelHeaders)->map(
            fn($header) => $this->headerMap[$header] ?? null
        )->toArray();

        $validRows   = collect();
        $skippedRows = collect();

        foreach ($collection->skip(1) as $index => $row) {

            $rowData = array_combine($mappedHeaders, $row->toArray());

            $rowData = array_filter(
                $rowData,
                fn($key) => ! is_null($key),
                ARRAY_FILTER_USE_KEY
            );

            $missing = collect($this->requiredFields)
                ->filter(fn($field) => empty($rowData[$field]))
                ->values();

            if ($missing->isNotEmpty()) {
                $skippedRows->push([
                    'row'     => $index + 2,
                    'missing' => $missing,
                    'data'    => $rowData,
                ]);
                continue;
            }

            $validRows->push($rowData);
        }

        session([
            'agreement_import_preview' => $validRows,
            'agreement_import_skipped' => $skippedRows,
        ]);
        // dd($validRows);
        return view(
            'import_excel.agreement_preview',
            compact('validRows', 'skippedRows')
        );
    }

    // public function preview(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|mimes:xlsx,csv',
    //     ]);

    //     $data = Excel::toCollection(null, $request->file('file'))->first();
    //     dd($data);
    //     session(['agreement_import_preview' => $data]);

    //     return view('import_excel.agreement_preview', compact('data'));
    // }
    public function confirm_agreement(Request $request)
    {
        $data = session('agreement_import_preview');

        if (! $data) {
            return redirect()->back()->with('error', 'No data found in session.');
        }

        DB::beginTransaction();
        try {
            foreach ($data as $rowIndex => $normalizedRow) {
                if (! is_array($normalizedRow)) {
                    continue;
                }
            
                $dateColumns = [
                    'date',
                    'lease_start_date',
                    'lease_end_date',
                    'rent_start_date',
                    'rent_end_date',
                    'service_start_date',
                    'service_end_date',
                    'lease_break_date',
                ];

                foreach ($dateColumns as $col) {
                    if (isset($normalizedRow[$col]) && $normalizedRow[$col] !== '') {
                        $normalizedRow[$col] = normalizeDate($normalizedRow[$col]);
                    }
                }
                // dd($normalizedRow);
                // $dateColumns = [
                //     'date',
                //     'lease_start_date',
                //     'lease_end_date',
                //     'rent_start_date',
                //     'rent_end_date',
                // ];

                // foreach ($dateColumns as $col) {
                //     if (!empty($normalizedRow[$col]) && is_numeric($normalizedRow[$col])) {
                //         try {
                //             $phpDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(
                //                 $normalizedRow[$col]
                //             );
                //             $normalizedRow[$col] = $phpDate->format('Y-m-d');
                //         } catch (\Exception $e) {}
                //     }
                // } 
                $validator = Validator::make($normalizedRow, [
                    'lease_agreement_no' => 'required',
                    'tenant_name'        => 'required',
                    'property_name'      => 'required',
                    'block_name'         => 'required',
                    'floor_name'         => 'required',
                    'unit'               => 'required',
                    'lease_start_date'   => 'required|date',
                    'lease_end_date'     => 'required|date',
                    'invoice_frequency'  => 'required',
                    'rent_start_date'    => 'required|date',
                    'rent_end_date'      => 'required|date',
                    'currency'           => 'required',
                    'rent_per_month'     => 'required|numeric',
                ]);

                if ($validator->fails()) {
                    continue;
                }
                // dd($normalizedRow);
                // try {
                //     foreach ($data->skip(1) as $rowIndex => $row) {

                //         // $normalizedRow = $this->normalizeRow(array_combine($data[0]->toArray(), $row->toArray()));

                //         $normalizedRow = $this->normalizeRow(array_combine($data[0]->toArray(), $row->toArray()));
                // dd($normalizedRow);
                // $dateColumns = ['lease_start_date', 'lease_end_date', 'rent_start_date', 'rent_end_date', 'date'];

                // foreach ($dateColumns as $col) {
                //     if (! empty($normalizedRow[$col]) && is_numeric($normalizedRow[$col])) {
                //         try {
                //             $phpDate             = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($normalizedRow[$col]);
                //             $normalizedRow[$col] = $phpDate->format('Y-m-d'); // أو 'Y-m-d H:i:s' لو عايز الوقت
                //         } catch (\Exception $e) {
                //             // لو فشل التحويل، سيترك القيمة كما هي وسيظهر خطأ Validation لاحقًا
                //         }
                //     }
                // }
                // $validator = Validator::make($normalizedRow, [
                //     'lease_agreement_no'  => 'required',
                //     'tenant_name'         => 'required',
                //     'property_name'       => 'required',
                //     'block_name'          => 'required',
                //     'floor_name'          => 'required',
                //     'unit'                => 'required',
                //     'lease_start_date'    => 'required|date',
                //     'lease_end_date'      => 'required|date',
                //     'invoicing_frequency' => 'required', //Frequency
                //     'rent_start_date'     => 'required|date',
                //     'rent_end_date'       => 'required|date',
                //     'currency'            => 'required',
                //     'rent_per_month'      => 'required|numeric',
                // ]);

                // if ($validator->fails()) {
                //     return redirect()->route('import_contract')
                //         ->withErrors($validator)
                //         ->withInput();
                // }
 
                $country = CountryMaster::select('id', 'country_id', 'country_code')->first();
 
                $tenant = Tenant::firstOrCreate(
                    ['name' => trim($normalizedRow['tenant_name'])],
                    ['type' => 'individual', 'country_id' => $country->id]
                ); 
                $property = PropertyManagement::firstOrCreate(
                    ['name' => $normalizedRow['property_name']],
                    ['code' => $normalizedRow['prop_code'] ?? null]
                );
 
                $blockName = Block::firstOrCreate(
                    ['name' => $normalizedRow['block_name'], 'code' => $normalizedRow['block_code'] ?? null]
                );
                $block = BlockManagement::firstOrCreate([
                    'block_id'               => $blockName->id,
                    'property_management_id' => $property->id,
                ]);
 
                $floorName = Floor::firstOrCreate(
                    ['name' => $normalizedRow['floor_name'], 'code' => $normalizedRow['floor_code'] ?? null]
                );
                $floor = FloorManagement::firstOrCreate([
                    'floor_id'               => $floorName->id,
                    'block_management_id'    => $block->id,
                    'property_management_id' => $property->id,
                ]);
 
                $unit = Unit::firstOrCreate(
                    ['unit_no' => $normalizedRow['unit']],
                    ['name' => $normalizedRow['unit'], 'code' => $normalizedRow['unit']]
                );

                $unit_management = UnitManagement::firstOrCreate([
                    'unit_id'                => $unit->id,
                    'property_management_id' => $property->id,
                    'block_management_id'    => $block->id,
                    'floor_management_id'    => $floor->id,
                ]);
 
                $agreement = Agreement::updateOrCreate(
                    ['agreement_no' => $normalizedRow['lease_agreement_no']],
                    [
                        'agreement_no'   => $normalizedRow['lease_agreement_no'],
                        'agreement_date' => $normalizedRow['date'] ?? now(),
                        'tenant_id'      => $tenant->id,
                        'status'         => 'pending',
                        'country_id'     => $country->id,
                        'booking_status' => 'agreement',
                    ]
                );
 
                AgreementDetails::updateOrCreate(
                    ['agreement_id' => $agreement->id],
                    [
                        'period_from' => $normalizedRow['lease_start_date'],
                        'period_to'   => $normalizedRow['lease_end_date'],
                    ]
                );
 
                // $rentModes = [
                //     0 => ['select', 'select_rent_mode'],
                //     1 => ['daily', 'per day'],
                //     2 => ['monthly', 'per month'],
                //     3 => ['bi_monthly', 'every two months', 'bimonthly'],
                //     4 => ['quarterly', 'every 3 months'],
                //     5 => ['half_yearly', 'semi annual', 'semi-annual'],
                //     6 => ['yearly', 'annually', 'per year'],
                // ];

                // $excelValue     = strtolower(trim($normalizedRow['invoice_frequency'])); //Frequency
                // // $excelValue     = strtolower(trim($normalizedRow['invoicing_frequency'])); //Frequency
                // $paymentModeKey = collect($rentModes)->search(
                //     fn($synonyms) => in_array($excelValue, array_map('strtolower', $synonyms))
                // ) ?: 0;
                $rentModes = [
    0 => ['select', 'select_rent_mode'],
    1 => ['daily', 'per day'],
    2 => ['monthly', 'per month'],
    3 => ['bi_monthly', 'every two months', 'bimonthly'],
    4 => ['quarterly', 'every 3 months', 'quarter'],
    5 => ['half_yearly', 'semi annual', 'semi-annual', 'half yearly'],
    6 => ['yearly', 'annually', 'per year', 'year'],
];

                $excelValue = strtolower(trim($normalizedRow['invoice_frequency'] ?? ''));  
$paymentModeKey = collect($rentModes)->search(function($synonyms) use ($excelValue) {
    return in_array($excelValue, array_map('strtolower', $synonyms));
});

// لو ما اتعرفش، خلي default 0
$paymentModeKey = $paymentModeKey ?? 0;

                $agreementUnit = AgreementUnits::updateOrCreate(
                    [
                        'agreement_id' => $agreement->id,
                        'unit_id'      => $unit->id,
                    ],
                    [
                        'property_id'           => $property->id,
                        'commencement_date'     => $normalizedRow['rent_start_date'],
                        'expiry_date'           => $normalizedRow['rent_end_date'],
                        'payment_mode'          => $paymentModeKey,
                        'rent_amount'           => $normalizedRow['rent_per_month'],
                        'rent_mode'             => $paymentModeKey,
                        'total_net_rent_amount' => $normalizedRow['rent_per_month'],
                    ]
                );

                if (! empty($normalizedRow['service_start_date'])) {
                    $chargeMode = ServiceMaster::first();
                    AgreementUnitsService::updateOrCreate(
                        ['agreement_unit_id' => $agreementUnit->id],
                        [
                            'amount'            => $normalizedRow['service_amount_in_bd_exlusive_vat'],
                            'vat'               => 0,
                            'total'             => $normalizedRow['service_amount_in_bd_exlusive_vat'],
                            'other_charge_type' => $chargeMode->id,
                        ]
                    );
                }

                signed($agreement->id);
            }

            DB::commit();

            session()->forget('agreement_import_preview');

            return redirect()->route('agreement.index')->with('success', ui_change('imported_successfully'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('import_contract')
                ->with('error', $th->getMessage());
        }
    }

    // public function preview(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required',
    //     ]);
    //     $path = $request->file('file')->getRealPath();

    //     $data = Excel::toCollection(null, $request->file('file'))->first();

    //     return view('import_excel.agreement_preview', compact('data'));
    // }
    // public function confirm_agreement(Request $request)
    // {
    //     $data = session('agreement_import_preview');

    //     if (!$data) {
    //         return redirect()->back()->with('error', 'No data found in session.');
    //     }
    //     $rows = $request->input('rows', []);
    //     foreach ($rows as $row) {
    //         $normalizedRow = $this->normalizeRow($row);

    //         $validator = Validator::make($normalizedRow, [
    //             'lease_agreement_no' => 'required',
    //             'tenant_name'        => 'required',
    //             'property_name'      => 'required',
    //             'block_name'         => 'required',
    //             'floor_name'         => 'required',
    //             'unit'               => 'required',
    //             'lease_start_date'   => 'required',
    //             'lease_end_date'     => 'required',
    //             'invoicing_frequncy' => 'required',
    //             'rent_start_date'    => 'required',
    //             'rent_end_date'      => 'required',
    //             'currency'           => 'required',
    //             'rent_per_month'     => 'required',
    //         ]);

    //         if ($validator->fails()) {
    //             return redirect()->route('import_contract')
    //                 ->withErrors($validator)
    //                 ->withInput();
    //         }
    //     }
    //     DB::beginTransaction();

    //     try {
    //         foreach ($rows as $row) {
    //             $normalizedRow = $this->normalizeRow($row);

    //             $country = CountryMaster::select('id', 'country_id', 'country_code')->first();

    //             $tenant = Tenant::firstOrCreate(
    //                 ['name' => trim($normalizedRow['tenant_name'])],
    //                 ['type' => 'individual', 'country_id' => $country->id]
    //             );

    //             $property = PropertyManagement::firstOrCreate(
    //                 ['name' => $normalizedRow['property_name']],
    //                 ['code' => $normalizedRow['prop_code'] ?? null]
    //             );

    //             $blockName = Block::firstOrCreate(
    //                 ['name' => $normalizedRow['block_name'], 'code' => $normalizedRow['block_code'] ?? null]
    //             );
    //             $block = BlockManagement::firstOrCreate([
    //                 'block_id'               => $blockName->id,
    //                 'property_management_id' => $property->id,
    //             ]);
    //             $floorName = Floor::firstOrCreate(
    //                 ['name' => $normalizedRow['floor_name'], 'code' => $normalizedRow['floor_code'] ?? null]
    //             );
    //             $floor = FloorManagement::firstOrCreate([
    //                 'floor_id'               => $floorName->id,
    //                 'block_management_id'    => $block->id,
    //                 'property_management_id' => $property->id,
    //             ]);
    //             $unit = Unit::firstOrCreate(
    //                 ['unit_no' => $normalizedRow['unit']],
    //                 ['name' => $normalizedRow['unit'], 'code' => $normalizedRow['unit']]
    //             );

    //             $unit_management = UnitManagement::firstOrCreate([
    //                 'unit_id'                => $unit->id,
    //                 'property_management_id' => $property->id,
    //                 'block_management_id'    => $block->id,
    //                 'floor_management_id'    => $floor->id,
    //             ]);

    //             $agreement = Agreement::updateOrCreate(
    //                 ['agreement_no' => $normalizedRow['lease_agreement_no']],
    //                 [
    //                     'agreement_no'   => $normalizedRow['lease_agreement_no'],
    //                     'agreement_date' => $normalizedRow['date'] ?? Carbon::now(),
    //                     'tenant_id'      => $tenant->id,
    //                     'status'         => 'pending',
    //                     'country_id'     => $country->id,
    //                     'booking_status' => 'agreement',
    //                 ]
    //             );
    //             AgreementDetails::updateOrCreate(
    //                 ['agreement_id' => $agreement->id],
    //                 [
    //                     'period_from' => $normalizedRow['lease_start_date'],
    //                     'period_to'   => $normalizedRow['lease_end_date'],
    //                 ]
    //             );
    //             $rentModes = [
    //                 0 => ['select', 'select_rent_mode'],
    //                 1 => ['daily', 'per day'],
    //                 2 => ['monthly', 'per month'],
    //                 3 => ['bi_monthly', 'every two months', 'bimonthly'],
    //                 4 => ['quarterly', 'every 3 months'],
    //                 5 => ['half_yearly', 'semi annual', 'semi-annual'],
    //                 6 => ['yearly', 'annually', 'per year'],
    //             ];
    //             $excelValue     = strtolower(trim($normalizedRow['invoicing_frequncy']));
    //             $paymentModeKey = collect($rentModes)->search(
    //                 fn($synonyms) =>
    //                 in_array($excelValue, array_map('strtolower', $synonyms))
    //             ) ?: 0;

    //             $agreementUnit = AgreementUnits::updateOrCreate(
    //                 [
    //                     'agreement_id' => $agreement->id,
    //                     'unit_id'      => $unit->id,
    //                 ],
    //                 [
    //                     'property_id'           => $property->id,
    //                     'commencement_date'     => $normalizedRow['rent_start_date'],
    //                     'expiry_date'           => $normalizedRow['rent_end_date'],
    //                     'payment_mode'          => $paymentModeKey,
    //                     'rent_amount'           => $normalizedRow['rent_per_month'],
    //                     'rent_mode'             => $paymentModeKey,
    //                     'total_net_rent_amount' => $normalizedRow['rent_per_month'],
    //                 ]
    //             );
    //             if (! empty($normalizedRow['service_start_date'])) {
    //                 $chargeMode = ServiceMaster::first();
    //                 AgreementUnitsService::updateOrCreate(
    //                     [
    //                         'agreement_unit_id' => $agreementUnit->id,
    //                     ],
    //                     [
    //                         // 'service_frequency' => $normalizedRow['service_frequency'] ?? null,
    //                         // 'start_date'        => $normalizedRow['service_start_date'] ?? null,
    //                         // 'end_date'          => $normalizedRow['service_end_date'] ?? null,
    //                         'amount'            => $normalizedRow['service_amount_in_bd_exlusive_vat'],
    //                         'vat'               => 0,
    //                         'total'             => $normalizedRow['service_amount_in_bd_exlusive_vat'],
    //                         'other_charge_type' => $chargeMode->id,
    //                     ]
    //                 );
    //             }
    //             signed($agreement->id);
    //         }

    //         DB::commit();
    //         return redirect()->route('agreement.index')->with('success', ui_change('imported_successfully'));
    //     } catch (Throwable $th) {
    //         DB::rollBack();
    //         return redirect()->route('import_contract')
    //             ->with('error', $th->getMessage())
    //             ->withInput();
    //     }
    // }
    public function export_master()
    {
        return Excel::download(new UnitManagementExport, 'units.xlsx');
    }

    private function normalizeRow(array $row): array
    {
        $normalized = [];

        foreach ($row as $key => $value) {
            $normalizedKey = strtolower(trim($key));
            $normalizedKey = str_replace([' ', '-'], '_', $normalizedKey);
            $normalizedKey = preg_replace('/[^a-z0-9_]/', '', $normalizedKey);

            $normalized[$normalizedKey] = $value;
        }

        return $normalized;
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls',
        ]);

        Excel::import(new ContractTemplate, $request->file('file'));

        return back()->with('success', ui_change('imported_successfully'));
    }
}
