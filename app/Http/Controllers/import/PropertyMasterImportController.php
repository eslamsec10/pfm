<?php

namespace App\Http\Controllers\import;

use App\Http\Controllers\Controller;
use App\Imports\PropertyMasterTemplate;
use App\Models\Block;
use App\Models\BlockManagement;
use App\Models\Floor;
use App\Models\FloorManagement;
use App\Models\PropertyManagement;
use App\Models\PropertyType;
use App\Models\Unit;
use App\Models\UnitCondition;
use App\Models\UnitDescription;
use App\Models\UnitManagement;
use App\Models\UnitType;
use App\Models\View;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class PropertyMasterImportController extends Controller
{
    public function import_page(Request $request)
    {
        $instructions = '
        <p>1. ' . ui_change('Download_the_file_format_and_fill_the_correct_Data.') . '</p>
        <p>2. ' . ui_change('you_can_download_the_example_file_to_understand_how_the_data_must_be_filled.') . '</p>
        <p>3. ' . ui_change('once_you_have_downloaded_and_filled_the_format_file') . ', ' . ui_change('upload_it_in_the_form_below_and_submit.') . '</p>

    ';
        //     <p>4. ' . ui_change('after_uploading_products_you_need_to_edit_them_and_set_product_images_and_choices.') . '</p>
        // <p>5. ' . ui_change('you_can_get_brand_and_category_id_from_their_list_please_input_the_right_ids.') . '</p>
        // <p>6. ' . ui_change('you_can_upload_your_product_images_in_product_folder_from_gallery_and_copy_image_path.') . '</p>
        $data = [
            'instructions' => $instructions,
            'file'         => 'property',
            'file_name'         => 'property_headers_only',
        ];

        return view('import_excel.upload_page', $data);
    }
    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        $data = Excel::toCollection(null, $request->file('file'))->first();

        session([
            'property_import_preview' => $data
        ]);

        return view('import_excel.property_preview', [
            'data' => $data
        ]);
    }

    // public function preview(Request $request)
    // {
    //     // dd($request->all());
    //     $request->validate([
    //         'file' => 'required',
    //     ]);
    //     // dd($request->file('file'));
    //     $path = $request->file('file')->getRealPath();

    //     $data = Excel::toCollection(null, $request->file('file'))->first();

    //     return view('import_excel.property_preview', compact('data'));
    // }
    //     public function confirm_property_master(Request $request)
    // {
    //     $rows = $request->input('rows', []);

    //     ImportPropertyJob::dispatch($rows);

    //     return redirect()
    //         ->route('property_management.index')
    //         ->with('success', ui_change('import_request_received_processing_in_background'));
    // }
    public function confirm_property_master(Request $request)
    {
        // $rows = $request->input('rows', []);
        $rows = session('property_import_preview');
        $headers = $rows->first()->toArray();


        if (!$rows) {
            return back()->withErrors('No preview data found');
        }
        foreach ($rows->skip(1) as $row) {

            $rowArray = $row->toArray();
            $assocRow = array_combine($headers, $rowArray);
            $normalizedRow = $this->normalizeRow($assocRow);

            if (empty(array_filter($normalizedRow))) {
                continue;
            }
             $dateColumns = [
                    'installation_date_1',
                    'installation_date', 
                ];

                foreach ($dateColumns as $col) {
                    if (isset($normalizedRow[$col]) && $normalizedRow[$col] !== '') {
                        $normalizedRow[$col] = normalizeDate($normalizedRow[$col]);
                    }
                }
            /*
    |--------------------------------------------------------------------------
    | Property Type (unique by name)
    |--------------------------------------------------------------------------
    */
            $propertyType = null;
            if (!empty($normalizedRow['property_type'])) {
                $propertyType = PropertyType::firstOrCreate(
                    ['name' => $normalizedRow['property_type']],
                    ['code' => $normalizedRow['property_type']]
                );
            }

            /*
    |--------------------------------------------------------------------------
    | Property (unique by name)
    |--------------------------------------------------------------------------
    */
            $property = PropertyManagement::firstOrCreate(
                ['name' => $normalizedRow['property_name']],
                ['code' => $normalizedRow['property_code'] ?? null]
            );

            if ($propertyType) {
                $property->property_types()->syncWithoutDetaching([$propertyType->id]);
            }

            /*
    |--------------------------------------------------------------------------
    | Block (unique by name)
    |--------------------------------------------------------------------------
    */
            $blockName = Block::firstOrCreate(
                ['name' => $normalizedRow['block_name']],
                ['code' => $normalizedRow['block_code'] ?? null]
            );

            $block = BlockManagement::firstOrCreate([
                'block_id'               => $blockName->id,
                'property_management_id' => $property->id,
            ]);

            /*
    |--------------------------------------------------------------------------
    | Floor (unique by name)
    |--------------------------------------------------------------------------
    */
            $floorName = Floor::firstOrCreate(
                ['name' => $normalizedRow['floor_name']],
                ['code' => $normalizedRow['floor_code'] ?? null]
            );

            $floor = FloorManagement::firstOrCreate([
                'floor_id'               => $floorName->id,
                'block_management_id'    => $block->id,
                'property_management_id' => $property->id,
            ]);

            /*
    |--------------------------------------------------------------------------
    | Unit (unique by name)
    |--------------------------------------------------------------------------
    */
            $unitBaseName = $normalizedRow['unit_name'] ?? 'Unit';
            $unitNo       = $normalizedRow['unit_no'] ?? null;

            $unitName = Unit::firstOrCreate(
                ['name' => $unitBaseName],
                ['code' => $unitNo ?? $unitBaseName]
            );

            /*
    |--------------------------------------------------------------------------
    | Unit Type
    |--------------------------------------------------------------------------
    */
    $unit_type = !empty($normalizedRow['unit_type'])
                ? UnitType::firstOrCreate(
                    ['name' => $normalizedRow['unit_type']],
                    ['code' => $normalizedRow['unit_type']]
                )
                : null;
            /*
    |--------------------------------------------------------------------------
    | Unit Description
    |--------------------------------------------------------------------------
    */
            $unit_description = !empty($normalizedRow['description'])
                ? UnitDescription::firstOrCreate(
                    ['name' => $normalizedRow['description']],
                    ['code' => $normalizedRow['description']]
                )
                : null;

            /*
    |--------------------------------------------------------------------------
    | Unit Condition
    |--------------------------------------------------------------------------
    */
            $unit_condition = !empty($normalizedRow['unit_condition'])
                ? UnitCondition::firstOrCreate(
                    ['name' => $normalizedRow['unit_condition']],
                    ['code' => $normalizedRow['unit_condition']]
                )
                : null;

            /*
    |--------------------------------------------------------------------------
    | View
    |--------------------------------------------------------------------------
    */
            $view = !empty($normalizedRow['view'])
                ? View::firstOrCreate(
                    ['name' => $normalizedRow['view']],
                    ['code' => $normalizedRow['view']]
                )
                : null;

            /*
    |--------------------------------------------------------------------------
    | Unit Management
    |--------------------------------------------------------------------------
    */  
            UnitManagement::firstOrCreate(
                [
                    'unit_id'                => $unitName->id,
                    'property_management_id' => $property->id,
                    'block_management_id'    => $block->id,
                    'floor_management_id'    => $floor->id,
                ],
                [
                    'unit_description_id'   => $unit_description->id ?? null,
                    'unit_type_id'          => $unit_type->id ?? null,
                    'unit_condition_id'     => $unit_condition->id ?? null,
                    'area'            => $normalizedRow['total_area'] ?? null, 
                    'area_unit'            => $normalizedRow['total_area_measurement_unit'] ?? null, 
                    'area_inside'            => $normalizedRow['area_inside'] ?? null, 
                    'area_inside_unit'            => $normalizedRow['area_inside_measurement_unit'] ?? null, 
                    'area_terrace'            => $normalizedRow['area_terrace'] ?? null, 
                    'area_terrace_unit'            => $normalizedRow['area_terrace_measurement_unit'] ?? null, 
                    'rate'            => $normalizedRow['rate'] ?? null, 
                    'rate_unit'            => $normalizedRow['rate_measurement_unit'] ?? null, 
                    'rent_amount_per_month'            => $normalizedRow['rent_amount_per_month'] ?? null, 
                    'security_deposit_amount'            => $normalizedRow['security_deposit_amount'] ?? null, 
                    'municipality_nos'            => $normalizedRow['municipality_nos'] ?? null, 
                    'installation_date'            => $normalizedRow['installation_date'] ?? null, 
                    'electricity_meter_no'            => $normalizedRow['electricity_meter_no'] ?? null, 
                    'installation_date_1'            => $normalizedRow['installation_date1'] ?? null, 
                    'water_meter_no'            => $normalizedRow['water_meter_no'] ?? null,  
                    'electricity_ac_no'            => $normalizedRow['electricity_ac_no'] ?? null, 
                    
                ]
            );
        }

        // foreach ($rows->skip(1) as $row) {
        //     $rowArray = $row->toArray();

        //     $assocRow = array_combine($headers, $rowArray);

        //     $normalizedRow = $this->normalizeRow($assocRow);

        //     if (empty(array_filter($normalizedRow))) {
        //         continue;
        //     }

        //     if ($normalizedRow['property_type']) {
        //         $propertyType = PropertyType::firstOrCreate([
        //             'name' => $normalizedRow['property_type'] ?? null,
        //             'code' => $normalizedRow['property_type'] ?? null,
        //         ]);
        //     }

        //     $property = PropertyManagement::firstOrCreate(
        //         ['name' => $normalizedRow['property_name'] ?? null],
        //         ['code' => $normalizedRow['property_code'] ?? null]
        //     );

        //     if ($property && $propertyType) {
        //         $property->property_types()->syncWithoutDetaching([$propertyType->id]);
        //     }

        //     // ---------------------
        //     // Block
        //     // ---------------------
        //     $blockName = Block::firstOrCreate([
        //         'name' => $normalizedRow['block_name'],
        //         'code' => $normalizedRow['block_code'] ?? null,
        //     ]);

        //     $block = BlockManagement::firstOrCreate([
        //         'block_id'               => $blockName->id,
        //         'property_management_id' => $property->id,
        //     ]);

        //     // ---------------------
        //     // Floor
        //     // ---------------------
        //     $floorName = Floor::firstOrCreate([
        //         'name' => $normalizedRow['floor_name'] ?? null,
        //         'code' => $normalizedRow['floor_code'] ?? null,
        //     ]);

        //     $floor = FloorManagement::firstOrCreate([
        //         'floor_id'               => $floorName->id,
        //         'block_management_id'    => $block->id,
        //         'property_management_id' => $property->id,
        //     ]);

        //     // ---------------------
        //     // Units
        //     // ---------------------
        //     $totalUnits = ! empty($normalizedRow['total_no_of_units'])
        //         ? (int) $normalizedRow['total_no_of_units']
        //         : 1;

        //     // ---------------------
        //     // Units
        //     // ---------------------
        //     $unitBaseName = $normalizedRow['unit_name'] ?? 'Unit';
        //     $unitFullName = $unitBaseName;

        //     $unitNo = $normalizedRow['unit_no'] ?? null;

        //     $unitName = Unit::firstOrCreate([
        //         'name' => $unitFullName,
        //         'code' => $unitNo ?? $unitFullName,
        //     ]);

        //     // Unit Description
        //     $unit_description = ! empty($normalizedRow['unit_type'])
        //         ? UnitDescription::firstOrCreate([
        //             'name' => $normalizedRow['unit_type'],
        //             'code' => $normalizedRow['unit_type'],
        //         ])
        //         : null;

        //     // Unit Condition
        //     $unit_condition = ! empty($normalizedRow['unit_condition'])
        //         ? UnitCondition::firstOrCreate([
        //             'name' => $normalizedRow['unit_condition'],
        //             'code' => $normalizedRow['unit_condition'],
        //         ])
        //         : null;

        //     // View
        //     $view = ! empty($normalizedRow['view'])
        //         ? View::firstOrCreate([
        //             'name' => $normalizedRow['view'],
        //             'code' => $normalizedRow['view'],
        //         ])
        //         : null;

        //     UnitManagement::firstOrCreate(
        //         [
        //             'unit_id'                => $unitName->id,
        //             'property_management_id' => $property->id,
        //             'block_management_id'    => $block->id,
        //             'floor_management_id'    => $floor->id,
        //         ],
        //         [
        //             'unit_description_id' => $unit_description->id ?? null,
        //             'unit_condition_id'   => $unit_condition->id ?? null,
        //             'view_id'             => $view->id ?? null,
        //         ]
        //     );
        // }

        return redirect()->route('property_management.index')->with('success', ui_change('imported_successfully'));
    }
    //     private function normalizeRow(array $row): array
    // {
    //     return [
    //         'property_name' => $row['Property Name'] ?? null,
    //         'property_code' => $row['Property Code'] ?? null,
    //         'property_type' => $row['Property Type'] ?? null,
    //         'ownership_type'=> $row['Type of Ownership'] ?? null,
    //         'land_lord'     => $row['Land Lord Name'] ?? null,
    //         'block_name'    => $row['Block Name'] ?? null,
    //         'block_code'    => $row['Block Code'] ?? null,
    //         'floor_name'    => $row['Floor Name'] ?? null,
    //         'floor_code'    => $row['Floor Code'] ?? null,
    //         'unit_name'     => $row['Unit Name'] ?? null,
    //         'rent_amount'   => $row['Rent (Amount per month)'] ?? null,
    //     ];
    // }

    //     public function confirm_property_master(Request $request)
    // {
    //     $rows = $request->input('rows', []);

    //     foreach ($rows as $row) {
    //         // نظف المفاتيح
    //         $normalizedRow = $this->normalizeRow($row);

    //         // ---------------------
    //         // Property Type
    //         // ---------------------
    //         $propertyType = PropertyType::firstOrCreate([
    //             'name' => $normalizedRow['property_type'] ?? null,
    //             'code' => $normalizedRow['property_type'] ?? null,
    //         ]);

    //         // ---------------------
    //         // Property
    //         // ---------------------
    //         $property = PropertyManagement::firstOrCreate(
    //             ['name' => $normalizedRow['property_name'] ?? null],
    //             ['code' => $normalizedRow['property_code'] ?? null]
    //         );

    //         if ($property && $propertyType) {
    //             $property->property_types()->syncWithoutDetaching([$propertyType->id]);
    //         }

    //         // ---------------------
    //         // Block
    //         // ---------------------
    //         $blockName = Block::firstOrCreate([
    //             'name' => $normalizedRow['block_name'] ?? null,
    //             'code' => $normalizedRow['block_code'] ?? null,
    //         ]);

    //         $block = BlockManagement::firstOrCreate([
    //             'block_id'               => $blockName->id,
    //             'property_management_id' => $property->id,
    //         ]);

    //         // ---------------------
    //         // Floor
    //         // ---------------------
    //         $floorName = Floor::firstOrCreate([
    //             'name' => $normalizedRow['floor_name'] ?? null,
    //             'code' => $normalizedRow['floor_code'] ?? null,
    //         ]);

    //         $floor = FloorManagement::firstOrCreate([
    //             'floor_id'               => $floorName->id,
    //             'block_management_id'    => $block->id,
    //             'property_management_id' => $property->id,
    //         ]);

    //         // ---------------------
    //         // Unit
    //         // ---------------------
    //         $unitName = null;
    //         if (!empty($normalizedRow['unit_no'])) {
    //             $unitName = Unit::firstOrCreate([
    //                 'name' => $normalizedRow['unit_no'],
    //                 'code' => $normalizedRow['unit_no'],
    //             ]);
    //         } elseif (!empty($normalizedRow['unit_name'])) {
    //             $unitName = Unit::firstOrCreate([
    //                 'name' => $normalizedRow['unit_name'],
    //                 'code' => $normalizedRow['unit_name'],
    //             ]);
    //         }

    //         // Unit Description
    //         $unit_description = !empty($normalizedRow['unit_type'])
    //             ? UnitDescription::firstOrCreate([
    //                 'name' => $normalizedRow['unit_type'],
    //                 'code' => $normalizedRow['unit_type']
    //             ])
    //             : null;

    //         // Unit Condition
    //         $unit_condition = !empty($normalizedRow['unit_condition'])
    //             ? UnitCondition::firstOrCreate([
    //                 'name' => $normalizedRow['unit_condition'],
    //                 'code' => $normalizedRow['unit_condition']
    //             ])
    //             : null;

    //         // View
    //         $view = !empty($normalizedRow['view'])
    //             ? View::firstOrCreate([
    //                 'name' => $normalizedRow['view'],
    //                 'code' => $normalizedRow['view']
    //             ])
    //             : null;

    //         if ($unitName) {
    //             $unit = UnitManagement::firstOrCreate(
    //                 [
    //                     'unit_id'                => $unitName->id,
    //                     'property_management_id' => $property->id,
    //                     'block_management_id'    => $block->id,
    //                     'floor_management_id'    => $floor->id,
    //                 ],
    //                 [
    //                     'unit_description_id' => $unit_description->id ?? null,
    //                     'unit_condition_id'   => $unit_condition->id ?? null,
    //                     'view_id'             => $view->id ?? null,
    //                 ]
    //             );
    //         }
    //     }

    //         return redirect()->route('property_management.index')->with('success', ui_change('imported_successfully'));
    // }

    private function normalizeRow(array $row): array
    {
        $normalized = [];

        foreach ($row as $key => $value) {
            if (is_int($key)) {
                continue;
            }

            $normalizedKey = strtolower(trim($key));
            $normalizedKey = str_replace([' ', '-'], '_', $normalizedKey);
            $normalizedKey = preg_replace('/[^a-z0-9_]/', '', $normalizedKey);

            $normalized[$normalizedKey] = $value;
        }
        return $normalized;
    }

    // public function confirm_property_master(Request $request)
    // {
    //     $rows = $request->input('rows', []);

    //     foreach ($rows as $row) {
    //         // ✅ Property Type
    //         $propertyType = PropertyType::firstOrCreate([
    //             'name' => $row['property_type'],
    //         ]);

    //         // ✅ Property
    //         $property = PropertyManagement::firstOrCreate(
    //             ['name' => $row['property_name'] ?? null],
    //             ['code' => $row['property_code'] ?? null]
    //         );

    //         if ($property && $propertyType) {
    //             $property->property_types()->sync($propertyType);
    //         }

    //         // ✅ Block
    //         $blockName = Block::firstOrCreate([
    //             'name' => $row['block_name'] ?? null,
    //             'code' => $row['block_code'] ?? null,
    //         ]);

    //         $block = BlockManagement::firstOrCreate(
    //             [
    //                 'block_id'               => $blockName->id,
    //                 'property_management_id' => $property->id,
    //             ],
    //             []
    //         );

    //         // ✅ Floor
    //         $floorName = Floor::firstOrCreate([
    //             'name' => $row['floor_name'] ?? null,
    //             'code' => $row['floor_code'] ?? null,
    //         ]);

    //         $floor = FloorManagement::firstOrCreate(
    //             [
    //                 'floor_id'               => $floorName->id,
    //                 'block_management_id'    => $block->id,
    //                 'property_management_id' => $property->id,
    //             ],
    //             []
    //         );

    //         // ✅ Unit
    //         $unitName = null;
    //         if (! empty($row['unit_no'])) {
    //             $unitName = Unit::firstOrCreate([
    //                 'name'    => $row['unit_no'],
    //                 'code'    => $row['unit_no'],
    //                 'unit_no' => $row['unit_no'],
    //             ]);
    //         } elseif (! empty($row['unit_name'])) {
    //             $unitName = Unit::firstOrCreate([
    //                 'name' => $row['unit_name'],
    //                 'code' => $row['unit_name'],
    //             ]);
    //         }

    //         // ✅ Unit Description
    //         $unit_description = ! empty($row['unit_type'])
    //         ? UnitDescription::firstOrCreate(['name' => $row['unit_type']])
    //         : null;

    //         // ✅ Unit Condition
    //         $unit_condition = ! empty($row['unit_condition'])
    //         ? UnitCondition::firstOrCreate(['name' => $row['unit_condition']])
    //         : null;

    //         // ✅ View
    //         $view = ! empty($row['view'])
    //         ? View::firstOrCreate(['name' => $row['view'], 'code' => $row['view']])
    //         : null;

    //         if ($unitName) {
    //             $unit = UnitManagement::updateOrCreate(
    //                 [
    //                     'unit_id'                => $unitName->id,
    //                     'property_management_id' => $property->id,
    //                     'block_management_id'    => $block->id,
    //                     'floor_management_id'    => $floor->id,
    //                 ],
    //                 [
    //                     'unit_description_id' => $unit_description->id ?? null,
    //                     'unit_condition_id'   => $unit_condition->id ?? null,
    //                     'view_id'             => $view->id ?? null,
    //                 ]
    //             );
    //         }
    //     }

    //     return redirect()->route('property.index')
    //         ->with('success', ui_change('imported_successfully'));
    // }

    // public function confirm_property_master(Request $request){
    //     $rows = $request->input('rows', []);
    //     foreach ($rows as $row) {
    //         $normalizedRow = $this->normalizeRow($row);
    //         Log::info($normalizedRow );
    //     }
    // }

    // private function normalizeRow(array $row): array
    // {
    //     $normalized = [];

    //     foreach ($row as $key => $value) {
    //         $normalizedKey = strtolower(trim($key));
    //         $normalizedKey = str_replace([' ', '-'], '_', $normalizedKey);
    //         $normalizedKey = preg_replace('/[^a-z0-9_]/', '', $normalizedKey);

    //         $normalized[$normalizedKey] = $value;
    //     }
    //     return $normalized;
    // }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls',
        ]);
        DB::beginTransaction();
        try {

            Excel::import(new PropertyMasterTemplate, $request->file('file'));
            DB::commit();
            return back()->with('success', ui_change('imported_successfully'));
        } catch (Exception $ex) {
            DB::rollBack();
            return back()->with('error', $ex->getMessage());
        }
    }
}
 

/*
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportPropertyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    
    public function __construct()
    {
        //
    }

    
    public function handle(): void
    {
        //
    }
}   
  
 [2025-09-14 07:05:51] local.INFO: array (
  'property_name' => 'Zamil Tower',
  'property_code' => '010',
  'type_of_ownership' => 'Owned',
  'property_type' => 'Commercial Building',
  'land_lord_name' => 'Al Zamil Properties',
  'building_no' => '31',
  'road' => NULL,
  'block' => '305',
  'area' => 'Manama Center',
  'city' => 'Manama',
  'total_no_of_blocks' => '3',
  'block_name' => 'Tower A',
  'block_code' => 'TA',
  'total_no_of_floors' => '22',
  'floor_name' => 'Ground',
  'floor_code' => 'GF',
  'total_no_of_units' => '3',
  'unit_name' => 'Batelco Room',
  'description' => NULL,
  'unit_type' => 'Utility Room',
  'creation_date' => NULL,
  'unit_condition' => NULL,
  'view' => NULL,
  'no_of_parkings_foc' => NULL,
  'area_unit_sq_mt__sq_ft' => NULL,
  'area_inside' => NULL,
  'area_terrace' => NULL,
  'rate_per_sq_mt__sq_ft' => NULL,
  'rent_amount_per_month' => NULL,
  'security_deposit_amount' => NULL,
  'municipality_nos' => NULL,
  'installation_date' => NULL,
  'electricity_meter_no' => NULL,
  'water_meter_no' => NULL,
  'electricity_ac_no' => NULL,
  0 => NULL,
  1 => NULL,
  2 => NULL,
  3 => NULL,
  4 => NULL,
)  
[2025-09-14 07:05:51] local.INFO: array (
  'property_name' => 'Zamil Tower',
  'property_code' => '010',
  'type_of_ownership' => 'Owned',
  'property_type' => 'Commercial Building',
  'land_lord_name' => 'Al Zamil Properties',
  'building_no' => '31',
  'road' => NULL,
  'block' => '305',
  'area' => 'Manama Center',
  'city' => 'Manama',
  'total_no_of_blocks' => '3',
  'block_name' => 'Tower A',
  'block_code' => 'TA',
  'total_no_of_floors' => '22',
  'floor_name' => 'Ground',
  'floor_code' => 'GF',
  'total_no_of_units' => '3',
  'unit_name' => 'Showroom 29',
  'description' => NULL,
  'unit_type' => 'Showroom',
  'creation_date' => NULL,
  'unit_condition' => NULL,
  'view' => NULL,
  'no_of_parkings_foc' => NULL,
  'area_unit_sq_mt__sq_ft' => NULL,
  'area_inside' => NULL,
  'area_terrace' => NULL,
  'rate_per_sq_mt__sq_ft' => NULL,
  'rent_amount_per_month' => NULL,
  'security_deposit_amount' => NULL,
  'municipality_nos' => NULL,
  'installation_date' => NULL,
  'electricity_meter_no' => NULL,
  'water_meter_no' => NULL,
  'electricity_ac_no' => NULL,
  0 => NULL,
  1 => NULL,
  2 => NULL,
  3 => NULL,
  4 => NULL,
)  
[2025-09-14 07:05:51] local.INFO: array (
  'property_name' => 'Zamil Tower',
  'property_code' => '010',
  'type_of_ownership' => 'Owned',
  'property_type' => 'Commercial Building',
  'land_lord_name' => 'Al Zamil Properties',
  'building_no' => '31',
  'road' => NULL,
  'block' => '305',
  'area' => 'Manama Center',
  'city' => 'Manama',
  'total_no_of_blocks' => '3',
  'block_name' => 'Tower A',
  'block_code' => 'TA',
  'total_no_of_floors' => '22',
  'floor_name' => 'Ground',
  'floor_code' => 'GF',
  'total_no_of_units' => '3',
  'unit_name' => 'Showroom 33',
  'description' => NULL,
  'unit_type' => 'Showroom',
  'creation_date' => NULL,
  'unit_condition' => NULL,
  'view' => NULL,
  'no_of_parkings_foc' => NULL,
  'area_unit_sq_mt__sq_ft' => NULL,
  'area_inside' => NULL,
  'area_terrace' => NULL,
  'rate_per_sq_mt__sq_ft' => NULL,
  'rent_amount_per_month' => NULL,
  'security_deposit_amount' => NULL,
  'municipality_nos' => NULL,
  'installation_date' => NULL,
  'electricity_meter_no' => NULL,
  'water_meter_no' => NULL,
  'electricity_ac_no' => NULL,
  0 => NULL,
  1 => NULL,
  2 => NULL,
  3 => NULL,
  4 => NULL,
)   */