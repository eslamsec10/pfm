<?php

namespace App\Http\Controllers\property_management;

use App\Http\Controllers\Controller;
use App\Models\BlockManagement;
use App\Models\FloorManagement; 
use App\Models\PropertyManagement;
use App\Models\DailyPriceList;
use App\Models\UnitManagement;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DailyRentListController extends Controller
{
    public function index(Request $request)
    { 
        $ids = $request->bulk_ids; 
          if ($request->bulk_action_btn === 'delete'  && is_array($ids) && count($ids)) { 
            $rentUpdated = DailyPriceList::whereIn('id', $ids)->delete();
            return back()->with('success', ui_change('deleted successfully'));
        }
       

        $search      = $request['search'];
        $query_param = $search ? ['search' => $request['search']] : '';

        $daily_price_list = DailyPriceList::when($request['search'], function ($q) use ($request) {
            $key = explode(' ', $request['search']);
            foreach ($key as $value) {
                $q->Where('enquiry_no', 'like', "%{$value}%")
                    ->orWhere('id', $value);
            }
        })->with(
            'unit_management:id,unit_id,property_management_id,block_management_id,floor_management_id',
            'unit_management.unit_management_main:id,name,code',
            'unit_management.property_unit_management:id,name,code',
            'unit_management.block_unit_management:id,block_id',
            'unit_management.block_unit_management.block:id,name,code',
            'unit_management.floor_unit_management.floor_management_main:id,name,code',
            'unit_management.floor_unit_management:id,floor_id'
        )
            ->latest()
            ->orderBy('created_at', 'desc')
            ->paginate()
            ->appends($query_param); 

        $data = [
            'daily_price_list' => $daily_price_list,
            'search'          => $search,
        ];

        return view("admin-views.property_management.daily_price_list.daily_price_list", $data);
    }

    public function create()
    {
        $property_managements = PropertyManagement::forUser()->select('id', 'name', 'code')->get();

        $data = [
            'property_managements' => $property_managements,
        ];

        return view("admin-views.property_management.daily_price_list.create_daily_price", $data);
    }

    public function edit($id)
    {
        $unit_daily            = DailyPriceList::findOrFail($id);
        $property_managements = PropertyManagement::forUser()->select('id', 'name', 'code')->get();

        $data = [
            'property_managements' => $property_managements,
            'unit_daily'            => $unit_daily,
        ];

        return view("admin-views.property_management.daily_price_list.edit_daily_price", $data);
    }

    public function store(Request $request)
    { 
        $request->validate([
            'daily_price'     => 'required',
            'property'        => 'required', 
            'applicable_date' => 'required',
        ]); 
        try {
            if ($request->applicable_date) {
                $applicable_date = Carbon::createFromFormat('d/m/Y', $request->applicable_date)->format('Y-m-d');
            }

            foreach ($request->units as $unit_id) {
                $unit_management = (new UnitManagement())->setConnection('tenant')->select('property_management_id' , 'block_management_id','floor_management_id','id')->where('id', $unit_id)->first();
                DailyPriceList::create([
                    'property_id'         => $unit_management->property_management_id,
                    'block_management_id' => $unit_management->block_management_id,
                    'floor_management_id' => $unit_management->floor_management_id,
                    'applicable_date'     => $applicable_date,
                    'unit_management_id'  => $unit_id,
                    'rent_amount'         => $request->daily_price[$unit_id],
                ]);
            }
            return to_route('daily_price.index')->with('success', ui_change('added_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    public function update(Request $request, $id)
    {
        $unit_rent = DailyPriceList::findOrFail($id);

        $request->validate([
            'daily_price'     => 'required',
            'property'        => 'required',
            'block'           => 'required',
            'floor'           => 'required',
            'units'            => 'required',
            'applicable_date' => 'required',
        ]);
        try {
            if ($request->applicable_date) {
                $applicable_date = Carbon::createFromFormat('d/m/Y', $request->applicable_date)->format('Y-m-d');
            }


            $unit_rent->update([
                'property_id'         => $request->property,
                'block_management_id' => $request->block,
                'floor_management_id' => $request->floor,
                'applicable_date'     => $applicable_date,
                'unit_management_id'  => $request->units,
                'rent_amount'         => $request->daily_price,
            ]);

            return to_route('daily_price.index')->with('success', ui_change('updated_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    public function get_blocks_by_property_id_for_daily($id)
    {
        $property = PropertyManagement::findOrFail($id);
        $blocks   = BlockManagement::where('property_management_id', $property->id)->with('block')->get();
        return json_encode($blocks);
    }
    public function get_floors_by_block_id_for_daily($id)
    {
        $blocks = BlockManagement::findOrFail($id);
        $floors = FloorManagement::where('block_management_id', $blocks->id)->select('id', 'floor_id')->with('floor_management_main:id,name,code')->get();
        return json_encode($floors);
    }

    public function get_units_by_floor_id_for_daily($id)
    {
        $floor = FloorManagement::findOrFail($id);
        $units = UnitManagement::where('floor_management_id', $floor->id)->select('id', 'unit_id')->with('unit_management_main:id,name,code')->get();
        return json_encode($units);
    }
    public function delete(Request $request)
    {
        $rent = DailyPriceList::findOrFail($request->id);
        $rent->delete();
        return redirect()->route('daily_price.index')->with('success', ui_change('deleted_successfully'));
    }

    public function getUnits($propertyId)
    {
        $units = UnitManagement::where('property_management_id', $propertyId)
            ->select(['id', 'unit_condition_id', 'unit_id', 'block_management_id', 'floor_management_id', 'unit_description_id', 'unit_type_id', 'view_id'])
            ->with('unit_management_main:id,name,code')
            ->get();

        return response()->json(['units' => $units]);
    }
    public function getBlocks($propertyId)
    {
        $blocks = BlockManagement::where('property_management_id', $propertyId)->get(['id', 'name']);
        return response()->json(['blocks' => $blocks]);
    }

    public function getFloors($propertyId, $blockId = null)
    {
        $query = FloorManagement::where('property_management_id', $propertyId);
        if ($blockId) {
            $query->where('block_management_id', $blockId);
        }
        $floors = $query->get(['id', 'name']);
        return response()->json(['floors' => $floors]);
    }

    public function getUnitsFiltered(Request $request)
    {
        if($request->property_id == -1){
            $units = UnitManagement::when($request->block_id, fn($q) => $q->where('block_management_id', $request->block_id))
            ->when($request->floor_id, fn($q) => $q->where('floor_management_id', $request->floor_id))
             ->with('unit_management_main:id,name,code','property_unit_management:id,name,code','block_unit_management:id,block_id','block_unit_management.block:id,name,code','floor_unit_management.floor_management_main:id,name,code','floor_unit_management:id,floor_id',
            'unit_type:id,name,code',
            'unit_condition:id,name,code',
            'unit_description:id,name,code',
            'view:id,name,code')
            ->get(); 
            return response()->json(['units' => $units]);
        }
        $units = UnitManagement::where('property_management_id', $request->property_id)
            ->when($request->block_id, fn($q) => $q->where('block_management_id', $request->block_id))
            ->when($request->floor_id, fn($q) => $q->where('floor_management_id', $request->floor_id))
            ->with('unit_management_main:id,name,code','property_unit_management:id,name,code','block_unit_management:id,block_id','block_unit_management.block:id,name,code','floor_unit_management.floor_management_main:id,name,code','floor_unit_management:id,floor_id',
            'unit_type:id,name,code',
            'unit_condition:id,name,code',
            'unit_description:id,name,code',
            'view:id,name,code')
            ->get();

        return response()->json(['units' => $units]);
    }

     
}
