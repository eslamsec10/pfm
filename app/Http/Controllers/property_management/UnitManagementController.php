<?php

namespace App\Http\Controllers\property_management;

use App\Models\Unit;
use App\Models\User;
use App\Models\View;
use App\Models\UnitType;
use App\Models\UnitParking;
use App\Models\RoomFacility;
use App\Models\UserSettings;
use Illuminate\Http\Request;
use App\Models\UnitCondition;
use App\Models\general\Groups;
use App\Models\UnitManagement;
use App\Models\BlockManagement;
use App\Models\FloorManagement;
use App\Models\UnitDescription;
use App\Models\PropertyManagement;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\hierarchy\CostCenter;
use App\Models\hierarchy\MainLedger;
use App\Models\hierarchy\CostCenterCategory;

class UnitManagementController extends Controller
{
    public function index(Request $request)
    {
        // $this->authorize('unit_management');
        $ids = $request->bulk_ids;
        $search = $request['search'];
        $query_param = $search ? ['search' => $request['search']] : '';

        $userId =  auth()->id();
        $settings = UserSettings::where('user_id', $userId)->first();
        $buildingIds = $settings?->building_ids ?? [];
        if (is_string($buildingIds)) {
            $buildingIds = json_decode($buildingIds, true) ?? [];
        }
        $unit_management = (new UnitManagement())->setConnection('tenant')->whereIn('property_management_id', $buildingIds)->join('units', 'unit_management.unit_id', '=', 'units.id')->when($request['search'], function ($q) use ($request) {
            $key = explode(' ', $request['search']);
            foreach ($key as $value) {
                $q->Where('units.name', 'like', "%{$value}%")
                    ->orWhere('unit_management.id', $value);
            }
        })
            ->select('unit_management.*', 'units.name as block_name')
            ->latest()->paginate()->appends($query_param);

        $data = [
            'unit_management' => $unit_management,
            'search' => $search,
        ];
        return view("admin-views.property_management.unit_management.unit_management_list", $data);
    }

    public function create_new()
    {
        // $this->authorize('create_unit_management');

        $property = (new PropertyManagement())->setConnection('tenant')->forUser()->get();
        $blocks = (new BlockManagement())->setConnection('tenant')->get();
        $floors = (new FloorManagement())->setConnection('tenant')->get();
        $units = (new Unit())->setConnection('tenant')->get();
        $unit_types = (new UnitType())->setConnection('tenant')->get();
        $unit_conditions = (new UnitCondition())->setConnection('tenant')->get();
        $unit_descriptions = (new UnitDescription())->setConnection('tenant')->get();
        $views = (new View())->setConnection('tenant')->get();
        $unit_parkings = (new UnitParking())->setConnection('tenant')->get();
        $facilities = RoomFacility::select('id', 'name')->get();
        $data = [
            "property" => $property,
            "floors" => $floors,
            'units' => $units,
            "blocks" => $blocks,
            "unit_types" => $unit_types,
            "unit_conditions" => $unit_conditions,
            "unit_descriptions" => $unit_descriptions,
            "unit_parkings" => $unit_parkings,
            "views" => $views,
            "facilities" => $facilities,
        ];

        return view("admin-views.property_management.unit_management.create_new")->with($data);
    }
    public function create()
    {
        // $this->authorize('create_unit_management');

        $property = (new PropertyManagement())->setConnection('tenant')->forUser()->get();
        $blocks = (new BlockManagement())->setConnection('tenant')->get();
        $floors = (new FloorManagement())->setConnection('tenant')->get();
        $units = (new Unit())->setConnection('tenant')->get();
        $unit_types = (new UnitType())->setConnection('tenant')->get();
        $unit_conditions = (new UnitCondition())->setConnection('tenant')->get();
        $unit_descriptions = (new UnitDescription())->setConnection('tenant')->get();
        $views = (new View())->setConnection('tenant')->get();
        $unit_parkings = (new UnitParking())->setConnection('tenant')->get();
        $data = [
            "property" => $property,
            "floors" => $floors,
            'units' => $units,
            "blocks" => $blocks,
            "unit_types" => $unit_types,
            "unit_conditions" => $unit_conditions,
            "unit_descriptions" => $unit_descriptions,
            "unit_parkings" => $unit_parkings,
            "views" => $views,
        ];

        return view("admin-views.property_management.unit_management.create")->with($data);
    }
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            /** ================= VALIDATION ================= */
            $request->validate([
                'property' => 'required',
                'block'    => 'required',
                'floor'    => 'required',
                'units'    => 'required|array|min:1',
            ]);

            /** ================= HELPERS ================= */
            $fk = fn($v) => ($v && $v != 0) ? $v : null;

            $company = auth()->user()
                ?? (new User())->setConnection('tenant')->first();

            /** ================= GET UNITS ================= */
            $units = (new Unit())
                ->setConnection('tenant')
                ->whereIn('id', $request->units)
                ->get();

            if ($units->isEmpty()) {
                return redirect()->back()->with('error', 'No units selected');
            }

            /** ================= FLOOR DATA ================= */
            $floor_manage = (new FloorManagement())
                ->setConnection('tenant')
                ->select('id', 'long_status')
                ->findOrFail($request->floor);

            /** ================= GROUP ================= */
            $group = (new Groups())
                ->setConnection('tenant')
                ->where('property_id', $request->property)
                ->first();

            foreach ($units as $unit) {

                $unit_management = (new UnitManagement())
                    ->setConnection('tenant')
                    ->create([
                        'property_management_id' => $request->property,
                        'block_management_id'    => $request->block,
                        'floor_management_id'    => $request->floor,
                        'unit_id'                => $unit->id,
                        'adults'                 => $request->adults[$unit->id] ?? 1,
                        'children'               => $request->children[$unit->id] ?? 1,
                        'unit_description_id' =>
                        $fk($request->unit_description[$unit->id] ?? null),

                        'unit_type_id' =>
                        $fk($request->unit_type[$unit->id] ?? null),

                        'unit_condition_id' =>
                        $fk($request->unit_condition[$unit->id] ?? null),

                        'unit_parking_id' =>
                        $fk($request->unit_parking[$unit->id] ?? null),

                        'view_id' =>
                        $fk($request->unit_view[$unit->id] ?? null),

                        'long_status' => $floor_manage->long_status,
                    ]);
                if ($request->unit_facilities[$unit->id] ?? false) {
                    $unit_management->facilities()->sync($request->unit_facilities[$unit->id]);
                }

                /** ================= LEDGER ================= */
                // (new MainLedger())
                //     ->setConnection('tenant')
                //     ->create([
                //         'code'      => $unit->name,
                //         'name'      =>
                //         $unit_management->property_unit_management->code . '-' .
                //             $unit_management->block_unit_management->block->code . '-' .
                //             $unit_management->floor_unit_management->floor_management_main->name . '-' .
                //             $unit_management->unit_management_main->name,

                //         'currency'  => $company->currency_code,
                //         'country_id' =>
                //         $unit_management->property_unit_management
                //             ?->country_master
                //             ?->country
                //             ?->id,

                //         'group_id'            => $group?->id,
                //         'main_id'             => $unit_management->id,
                //         'is_taxable'          => $group->is_taxable ?? 0,
                //         'vat_applicable_from' => $group->vat_applicable_from ?? null,
                //         'tax_rate'            => $group->tax_rate ?? 0,
                //         'tax_applicable'      => $group->tax_applicable ?? 0,
                //         'status'              => 'active',
                //     ]);

                // /** ================= COST CENTER ================= */
                // $propertyCost = (new CostCenterCategory())
                //     ->setConnection('tenant')
                //     ->where('main_id', $unit_management->property_management_id)
                //     ->where('main_type', 'property')
                //     ->first();

                // (new CostCenter())
                //     ->setConnection('tenant')
                //     ->create([
                //         'name' =>
                //         $unit_management->property_unit_management->name . '-' .
                //             $unit_management->unit_management_main->name . '-' .
                //             $unit_management->block_unit_management->block->name . '-' .
                //             $unit_management->floor_unit_management->floor_management_main->name,

                //         'main_id'   => $unit_management->id,
                //         'main_type' => 'unit',
                //         'cost_center_category_id' => $propertyCost?->id,
                //         'status'    => 'active',
                //     ]);
            }

            DB::commit();

            return redirect()
                ->route('unit_management.index')
                ->with('success', ui_change('added_successfully'));
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // public function store(Request $request)
    // {
    //     DB::beginTransaction();

    //     try {

    //         /** ================= VALIDATION ================= */
    //         $request->validate([
    //             'property'      => 'required',
    //             'block'         => 'required',
    //             'floor'         => 'required',
    //             'start_up_unit' => 'required',
    //             'unit_count'    => 'required',
    //         ]);

    //         /** ================= SELECT UNITS ================= */
    //         if (
    //             $request->unit_description_mode === 'default' &&
    //             $request->unit_type_mode === 'default' &&
    //             $request->view_mode === 'default'
    //         ) {
    //             // DEFAULT MODE
    //             $selectedUnits = (new Unit())
    //                 ->setConnection('tenant')
    //                 ->whereBetween('id', [
    //                     $request->start_up_unit,
    //                     $request->start_up_unit + $request->unit_count - 1
    //                 ])
    //                 ->get();
    //         } else {
    //             // RANGE MODE
    //             $selectedUnitIds = collect()
    //                 ->merge($request->unit_start_unit_description ?? [])
    //                 ->merge($request->unit_end_unit_description ?? [])
    //                 ->merge($request->unit_start_view ?? [])
    //                 ->merge($request->unit_end_view ?? [])
    //                 ->unique()
    //                 ->values()
    //                 ->toArray();

    //             $selectedUnits = (new Unit())
    //                 ->setConnection('tenant')
    //                 ->whereIn('id', $selectedUnitIds)
    //                 ->get();
    //         }

    //         if ($selectedUnits->isEmpty()) {
    //             return redirect()->back()->with('error', 'No units selected');
    //         }

    //         /** ================= HELPERS ================= */
    //         $fk = fn($v) => ($v && $v != 0) ? $v : null;

    //         $company = auth()->user()
    //             ?? (new User())->setConnection('tenant')->first();

    //         /** ================= PREPARE DATA ================= */
    //         $unit_description = [];
    //         $unit_type        = [];
    //         $unit_condition   = [];
    //         $unit_parking     = [];
    //         $view             = [];

    //         /** -------- UNIT DESCRIPTION -------- */
    //         if ($request->unit_description_mode === 'range') {
    //             foreach ($request->unit_start_unit_description as $i => $unitId) {
    //                 $unit_description[(int)$unitId] =
    //                     $fk($request->unit_description[$i] ?? null);
    //             }
    //         } else {
    //             foreach ($selectedUnits as $unit) {
    //                 $unit_description[$unit->id] =
    //                     $fk($request->unit_description[0] ?? null);
    //             }
    //         }

    //         /** -------- UNIT TYPE -------- */
    //         foreach ($selectedUnits as $unit) {
    //             $unit_type[$unit->id] =
    //                 $fk($request->unit_type[0] ?? null);
    //         }

    //         /** -------- UNIT CONDITION -------- */
    //         foreach ($selectedUnits as $unit) {
    //             $unit_condition[$unit->id] =
    //                 $fk($request->unit_condition[0] ?? null);
    //         }

    //         /** -------- UNIT PARKING -------- */
    //         foreach ($selectedUnits as $unit) {
    //             $unit_parking[$unit->id] =
    //                 $fk($request->unit_parking[0] ?? null);
    //         }

    //         /** -------- VIEW -------- */
    //         if ($request->view_mode === 'range') {
    //             foreach ($request->unit_start_view as $i => $unitId) {
    //                 $view[(int)$unitId] =
    //                     $fk($request->view[$i] ?? null);
    //             }
    //         } else {
    //             foreach ($selectedUnits as $unit) {
    //                 $view[$unit->id] =
    //                     $fk($request->view[0] ?? null);
    //             }
    //         }

    //         /** ================= STORE ================= */
    //         foreach ($selectedUnits as $unit) {

    //             $floor_manage = (new FloorManagement())
    //                 ->setConnection('tenant')
    //                 ->select('id', 'long_status')
    //                 ->find($request->floor);

    //             $unit_management = (new UnitManagement())
    //                 ->setConnection('tenant')
    //                 ->create([
    //                     'property_management_id' => $request->property,
    //                     'block_management_id'    => $request->block,
    //                     'floor_management_id'    => $request->floor,
    //                     'unit_id'                => $unit->id,
    //                     'unit_description_id'    => $unit_description[$unit->id],
    //                     'unit_type_id'           => $unit_type[$unit->id],
    //                     'unit_condition_id'      => $unit_condition[$unit->id],
    //                     'unit_parking_id'        => $unit_parking[$unit->id],
    //                     'view_id'                => $view[$unit->id],
    //                     'long_status'            => $floor_manage->long_status,
    //                 ]);

    //             /** -------- LEDGER -------- */
    //             $group = (new Groups())
    //                 ->setConnection('tenant')
    //                 ->where('property_id', $request->property)
    //                 ->first();

    //             (new MainLedger())
    //                 ->setConnection('tenant')
    //                 ->create([
    //                     'code'                => $unit->name,
    //                     'name'                =>
    //                     $unit_management->property_unit_management->code . '-' .
    //                         $unit_management->block_unit_management->block->code . '-' .
    //                         $unit_management->floor_unit_management->floor_management_main->name . '-' .
    //                         $unit_management->unit_management_main->name,
    //                     'currency'            => $company->currency_code,
    //                     'country_id'          =>
    //                     $unit_management->property_unit_management?->country_master?->country?->id,
    //                     'group_id'            => $group->id,
    //                     'main_id'             => $unit_management->id,
    //                     'is_taxable'          => $group->is_taxable ?? 0,
    //                     'vat_applicable_from' => $group->vat_applicable_from,
    //                     'tax_rate'            => $group->tax_rate ?? 0,
    //                     'tax_applicable'      => $group->tax_applicable ?? 0,
    //                     'status'              => 'active',
    //                 ]);

    //             /** -------- COST CENTER -------- */
    //             $property = (new CostCenterCategory())
    //                 ->setConnection('tenant')
    //                 ->where('main_id', $unit_management->property_management_id)
    //                 ->where('main_type', 'property')
    //                 ->first();

    //             (new CostCenter())
    //                 ->setConnection('tenant')
    //                 ->create([
    //                     'name' =>
    //                     $unit_management->property_unit_management->name . '-' .
    //                         $unit_management->unit_management_main->name . '-' .
    //                         $unit_management->block_unit_management->block->name . '-' .
    //                         $unit_management->floor_unit_management->floor_management_main->name,
    //                     'main_id'              => $unit_management->id,
    //                     'main_type'            => 'unit',
    //                     'cost_center_category_id' => $property->id,
    //                     'status'               => 'active',
    //                 ]);
    //         }

    //         DB::commit();

    //         return redirect()
    //             ->route('unit_management.index')
    //             ->with('success', ui_change('added_successfully'));
    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         return redirect()->back()->with('error', $e->getMessage());
    //     }
    // }


    // public function store(Request $request)
    // {
    //     $unitCount  = DB::connection('tenant')->table('companies')->value('units_count');
    //     $unit_management_count      = DB::connection('tenant')->table('unit_management')->count();
    //     // dd($request->all());
    //     DB::beginTransaction();
    //     try {
    //         $request->validate([
    //             'property'                      => 'required',
    //             'block'                         => 'required',
    //             'floor'                         => 'required',
    //             'start_up_unit'                 => 'required',
    //             'unit_count'                    => 'required',
    //         ]);

    //         if ($unitCount <= $unit_management_count || $unitCount <= $request->unit_count) {
    //             return redirect()->back()->with("error", __('general.you_have_reached_the_maximum_limit'));
    //         }
    //         $units = (new Unit())->setConnection('tenant')->get();
    //         $company = auth()->user() ?? (new User())->setConnection('tenant')->first();
    //         $unit_count = $request->unit_count;
    //         $start_up_unit = $request->start_up_unit;
    //         $selected_units = (new Unit())->setConnection('tenant')->where('id', '>=', $start_up_unit)->take($unit_count)->get();

    //         $unit_description = [];
    //         $unit_type = [];
    //         $unit_condition = [];
    //         $unit_parking = [];
    //         $view = [];
    //         if ($request->unit_description_mode == 'range') {
    //             for ($i = 0; $i < count($request->unit_start_unit_description); $i++) {
    //                 $start = $request->unit_start_unit_description[$i];
    //                 $end = $request->unit_end_unit_description[$i];
    //                 $type = $request->unit_description[$i];
    //                 for ($j = $start; $j <= $end; $j++) {
    //                     $unit_description[$j] = $type;
    //                 }
    //             }
    //         } else {
    //             foreach ($selected_units as $unit_selected) {
    //                 $unit_description[$unit_selected->id] = $request->unit_description[0];
    //             }
    //         }
    //         if ($request->unit_type_mode == 'range') {
    //             for ($i = 0; $i < count($request->unit_start_unit_type); $i++) {
    //                 $start = $request->unit_start_unit_type[$i];
    //                 $end = $request->unit_end_unit_type[$i];
    //                 $type = $request->unit_type[$i];
    //                 for ($j = $start; $j <= $end; $j++) {
    //                     $unit_type[$j] = $type;
    //                 }
    //             }
    //         } else {
    //             foreach ($selected_units as $unit_selected) {
    //                 $unit_type[$unit_selected->id] = $request->unit_type[0];
    //             }
    //         }
    //         if ($request->unit_condition_mode == 'range') {
    //             for ($i = 0; $i < count($request->unit_start_unit_condition); $i++) {
    //                 $start = $request->unit_start_unit_condition[$i];
    //                 $end = $request->unit_end_unit_condition[$i];
    //                 $type = $request->unit_condition[$i];
    //                 for ($j = $start; $j <= $end; $j++) {
    //                     $unit_condition[$j] = $type;
    //                 }
    //             }
    //         } else {
    //             foreach ($selected_units as $unit_selected) {
    //                 $unit_condition[$unit_selected->id] = $request->unit_condition[0];
    //             }
    //         }
    //         if ($request->unit_parking_mode == 'range') {
    //             for ($i = 0; $i < count($request->unit_start_unit_parking); $i++) {
    //                 $start = $request->unit_start_unit_parking[$i];
    //                 $end = $request->unit_end_unit_parking[$i];
    //                 $type = $request->unit_parking[$i];
    //                 for ($j = $start; $j <= $end; $j++) {
    //                     $unit_parking[$j] = $type;
    //                 }
    //             }
    //         } else {
    //             foreach ($selected_units as $unit_selected) {
    //                 $unit_parking[$unit_selected->id] = $request->unit_parking[0];
    //             }
    //         }
    //         if ($request->view_mode == 'range') {
    //             for ($i = 0; $i < count($request->unit_start_view); $i++) {
    //                 $start = $request->unit_start_view[$i];
    //                 $end = $request->unit_end_view[$i];
    //                 $type = $request->view[$i];
    //                 for ($j = $start; $j <= $end; $j++) {
    //                     $view[$j] = $type;
    //                 }
    //             }
    //         } else {
    //             foreach ($selected_units as $unit_selected) {
    //                 $view[$unit_selected->id] = $request->view[0];
    //             }
    //         }
    //         foreach ($selected_units as $selected_unit) {
    //             $unit_management = (new UnitManagement())->setConnection('tenant')->create([
    //                 'property_management_id'                      => $request->property,
    //                 'block_management_id'                         => $request->block,
    //                 'floor_management_id'                         => $request->floor,
    //                 'unit_id'                       => $selected_unit->id,
    //                 'unit_description_id'           => ($unit_description[$selected_unit->id] != 0) ? $unit_description[$selected_unit->id] : null,
    //                 'unit_type_id'                  => ($unit_type[$selected_unit->id] != 0) ? $unit_type[$selected_unit->id] : null,
    //                 'unit_condition_id'             => ($unit_condition[$selected_unit->id] != 0) ? $unit_condition[$selected_unit->id] : null,
    //                 'unit_parking_id'               => ($unit_parking[$selected_unit->id] != 0) ? $unit_parking[$selected_unit->id] : null,
    //                 'view_id'                       => ($view[$selected_unit->id] != 0) ? $view[$selected_unit->id] : null,
    //             ]);
    //             $group = (new Groups())->setConnection('tenant')->where('property_id', $request->property)->first();
    //             $ledger = (new MainLedger())->setConnection('tenant')->create([
    //                 'code'                   => $selected_unit->name,
    //                 'name'                   => $unit_management->property_unit_management->code . '-' . $unit_management->block_unit_management->block->code . '-' .
    //                     $unit_management->floor_unit_management->floor_management_main->name . '-' .  $unit_management->unit_management_main->name,
    //                 'currency'               => $company->currency_code,
    //                 'country_id'             => $unit_management->property_unit_management?->country_master?->country?->id,
    //                 'group_id'               => $group->id,
    //                 'main_id'                => $unit_management->id,
    //                 'is_taxable'             => $group->is_taxable ?: 0,
    //                 'vat_applicable_from'    => $group->vat_applicable_from ?? null,
    //                 'tax_rate'               => $group->tax_rate ?: 0,
    //                 'tax_applicable'         => $group->tax_applicable ?: 0,
    //                 'status'                 => 'active',
    //             ]);
    //             $property = (new CostCenterCategory())->setConnection('tenant')->where('main_id', $unit_management->property_management_id)->where('main_type', 'property')->first();
    //             $unit_cost = (new CostCenter())->setConnection('tenant')->create([

    //                 'name'                   => $unit_management->property_unit_management->name .
    //                     '-' .
    //                     $unit_management->unit_management_main->name .
    //                     '-' .
    //                     $unit_management->block_unit_management->block->name .
    //                     '-' .
    //                     $unit_management->floor_unit_management->floor_management_main->name . '-' . $unit_management->unit_management_main->name,
    //                 'main_id'                => $unit_management->id,
    //                 'main_type'                => 'unit',
    //                 'cost_center_category_id'   => $property->id,
    //                 'status'                 => 'active',
    //             ]);
    //         }
    //         DB::commit();
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return redirect()->back()->with("error", $e->getMessage());
    //     }
    //     return redirect()->route('unit_management.index')->with('success', ui_change('added_successfully'));
    // }

    public function edit($id)
    {
        // $this->authorize('edit_unit_management');
        $selected_unit = (new UnitManagement())->setConnection('tenant')->findOrFail($id);
        $property = (new PropertyManagement())->setConnection('tenant')->forUser()->get();
        $blocks = (new BlockManagement())->setConnection('tenant')->get();
        $floors = (new FloorManagement())->setConnection('tenant')->get();
        $units = (new Unit())->setConnection('tenant')->get();
        $unit_types = (new UnitType())->setConnection('tenant')->get();
        $unit_conditions = (new UnitCondition())->setConnection('tenant')->get();
        $unit_descriptions = (new UnitDescription())->setConnection('tenant')->get();
        $views = (new View())->setConnection('tenant')->get();
        $unit_parkings = (new UnitParking())->setConnection('tenant')->get();
        $facilities = RoomFacility::select('id', 'name')->get();

        $data = [
            "property" => $property,
            "floors" => $floors,
            'units' => $units,
            "blocks" => $blocks,
            "unit_types" => $unit_types,
            "unit_conditions" => $unit_conditions,
            "unit_descriptions" => $unit_descriptions,
            "unit_parkings" => $unit_parkings,
            "views" => $views,
            "selected_unit" => $selected_unit,
            "facilities" => $facilities,
        ];
        return view("admin-views.property_management.unit_management.edit")->with($data);
    }

    public function update(Request $request, $id)
    {

        try {
            $selected_unit = (new UnitManagement())->setConnection('tenant')->findOrFail($id);
            $unit = (new Unit())->setConnection('tenant')->where('id', $selected_unit->unit_id)->first();
            $selected_unit->update([
                'unit_id'                       => $unit->id,
                'unit_description_id'           => ($request->unit_description != 0) ? $request->unit_description : null,
                'unit_type_id'                  => ($request->unit_type != 0) ? $request->unit_type : null,
                'unit_condition_id'             => ($request->unit_condition != 0) ? $request->unit_condition : null,
                'unit_parking_id'               => ($request->unit_parking != 0) ? $request->unit_parking : null,
                'view_id'                       => ($request->view != 0) ? $request->view : null,
                'long_status'                   => $request->type,
            ]);
            if ($request->has('unit_facilities')) {
                $selected_unit->facilities()->sync($request->unit_facilities);
            } else {
                $selected_unit->facilities()->sync([]);
            }
            return redirect()->route('unit_management.index')->with('success', __('region.added_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function statusUpdate(Request $request)
    {
        $this->authorize('change_unit_management_status');
        $subscription = (new UnitManagement())->setConnection('tenant')->findOrFail($request->id);
        $subscription->update([
            'status' => ($request->status == 1) ? 'active' : 'inactive',
        ]);
        return redirect()->back()->with('success', __('property_master.updated_successfully'));
    }

    public function get_blocks_by_property_id($id)
    {
        $unit_management = (new UnitManagement())->setConnection('tenant')->pluck('block_management_id')->toArray();
        $property = (new PropertyManagement())->setConnection('tenant')->findOrFail($id);
        $blocks = (new BlockManagement())->setConnection('tenant')->where('property_management_id', $property->id)->with('block')->get();
        // $blocks = (new BlockManagement())->setConnection('tenant')->where('property_management_id', $property->id)->whereNotIn('id', $unit_management)->with('block')->get();
        return json_encode($blocks);
    }
    public function get_floors_by_block_id($id)
    {
        $unit_management = (new UnitManagement())->setConnection('tenant')->pluck('floor_management_id')->toArray();
        $block = (new BlockManagement())->setConnection('tenant')->findOrFail($id);
        $floors = (new FloorManagement())->setConnection('tenant')->where('block_management_id', $block->id)
            // ->whereNotIn('id', $unit_management)
            ->with('floor_management_main')->get();
        return json_encode($floors);
    }
    public function get_units_by_floor_id(Request $request, $floor_id, $block_id, $property_id)
    {
        // $unit_management = (new UnitManagement())->setConnection('tenant')->where('property_management_id', $property_id)->where('block_management_id', $block_id)->where('floor_management_id', $floor_id)->pluck('unit_id')->toArray();
        // $units = (new Unit())->setConnection('tenant')->whereNotIn('id', $unit_management)->get();
        // if (isset($request->start_up_unit) && isset($request->unit_count)) {
        //     $start_up_unit = $request->start_up_unit;
        //     $unit_count = (int) $request->unit_count;
        //     $start_unit_number = (int) preg_replace('/\D/', '', $start_up_unit);
        //     $unit_prefix = preg_replace('/\d/', '', $start_up_unit);
        //     $unit_ids = [];
        //     for ($i = 0; $i < $unit_count; $i++) {
        //         $unit_ids[] = $unit_prefix . str_pad($start_unit_number + $i, 3, '0', STR_PAD_LEFT);
        //     }

        //     $units = (new Unit())->setConnection('tenant')->whereIn('id', $unit_ids)->get();
        // }
        $unit_management = (new UnitManagement())
            ->setConnection('tenant')
            ->where('property_management_id', $property_id)
            ->where('block_management_id', $block_id)
            ->where('floor_management_id', $floor_id)
            ->pluck('unit_id')
            ->toArray();
        $units = (new Unit())
            ->setConnection('tenant')
            ->whereNotIn('id', $unit_management)
            ->get();

        return json_encode($units);
    }

     public function reorder(Request $request)
    { 
        $order = $request->input('order');
        if(!$order) {
            return response()->json(['status' => 'error', 'message' => 'No order data received'], 400);
        }

        foreach ($order as $item) {
            UnitManagement::where('id', $item['id'])
                ->update(['position' => $item['position']]);
        }

        return response()->json(['status' => 'success']);
    }
}
