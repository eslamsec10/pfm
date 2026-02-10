<?php

namespace App\Http\Controllers\Room_Reservation\Meetings;

use App\Models\User;
use App\Models\MeetingRoom;
use Illuminate\Http\Request;
use App\Models\general\Groups;
use App\Models\BlockManagement;
use App\Models\FloorManagement;
use App\Models\PropertyManagement;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\hierarchy\CostCenter;
use App\Models\hierarchy\MainLedger;
use App\Models\hierarchy\CostCenterCategory;

class MeetingRoomController extends Controller
{
    public function index(Request $request)
    {
        $ids = $request->bulk_ids;

        $search      = $request['search'];
        $query_param = $search ? ['search' => $request['search']] : '';
        $meeting_rooms   = MeetingRoom::when($request['search'], function ($q) use ($request) {
            $key = explode(' ', $request['search']);
            foreach ($key as $value) {
                $q->Where('name', 'like', "%{$value}%")
                    ->orWhere('id', $value);
            }
        })
            ->latest()->paginate()->appends($query_param);

        $data = [
            'main'   => $meeting_rooms,
            'search' => $search,
            'route'  => 'meeting_room',

        ];
        return view("admin-views.room_reservation.meetings.index", $data);
    }

    public function create()
    {
        $property =  PropertyManagement::forUser()->get();
        $blocks =  BlockManagement::get();
        $floors =  FloorManagement::get();
        $data = [
            "property" => $property,
            "floors" => $floors,
            "blocks" => $blocks,
        ];

        return view("admin-views.room_reservation.meetings.create")->with($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'capacity'   => 'required|integer|min:1',
            'location'   => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'property'   => 'required|integer',
            'block'      => 'required|integer',
            'floor'      => 'required|integer',
            'rent_amount' => 'nullable|numeric|min:0',
        ]);
        DB::beginTransaction();

        try {
            // create meeting room
            $room = MeetingRoom::create([
                'name'                   => $request->name,
                'capacity'               => $request->capacity,
                'location'               => $request->location,
                'description'            => $request->description,
                'is_available'           => true,
                'property_management_id' => $request->property,
                'block_management_id'    => $request->block,
                'floor_management_id'    => $request->floor,
                'rent_amount'            => $request->rent_amount,
            ]);
            if ( $room) {
               
            $group = (new Groups())
                ->setConnection('tenant')
                ->where('property_id', $request->property)
                ->first();
            $company =  (new Company())->setConnection('tenant')->first();
            /** ================= LEDGER ================= */
            (new MainLedger())
                ->setConnection('tenant')
                ->create([
                    'code'      => $room->name,
                    'name'      =>
                    $room->property_unit_management->code . '-' .
                        $room->block_unit_management->block->code . '-' .
                        $room->floor_unit_management->floor_management_main->name . '-' .
                        $room->name,

                    'currency'  => $company->currency_code,
                    'country_id' =>
                    $room->property_unit_management
                        ?->country_master
                        ?->country
                        ?->id ?? $company->countryid,

                    'group_id'            => $group?->id,
                    'main_id'             => $room->id,
                    'is_taxable'          => $group->is_taxable ?? 0,
                    'vat_applicable_from' => $group->vat_applicable_from ?? null,
                    'tax_rate'            => $group->tax_rate ?? 0,
                    'tax_applicable'      => $group->tax_applicable ?? 0,
                    'status'              => 'active',
                ]);

            /** ================= COST CENTER ================= */
            $propertyCost = (new CostCenterCategory())
                ->setConnection('tenant')
                ->where('main_id', $room->property_management_id)
                ->where('main_type', 'property')
                ->first();

            (new CostCenter())
                ->setConnection('tenant')
                ->create([
                    'name' =>
                    $room->property_unit_management?->name . '-' .
                        $room->unit_management_main?->name . '-' .
                        $room->block_unit_management?->block?->name . '-' .
                        $room->floor_unit_management?->floor_management_main?->name,

                    'main_id'   => $room->id,
                    'main_type' => 'meeting_room',
                    'cost_center_category_id' => $propertyCost?->id,
                    'status'    => 'active',
                ]);
                }
            DB::commit();
            return redirect()
                ->route('meeting_room.list')
                ->withSuccess('Meeting room created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withErrors('Failed to create meeting room: ' . $e->getMessage());
        }
    }
    public function edit($id)
    {
        $room = MeetingRoom::findOrFail($id);

        $property = PropertyManagement::forUser()->get();
        $blocks   = BlockManagement::get();
        $floors   = FloorManagement::get();

        return view('admin-views.room_reservation.meetings.edit', [
            'room'     => $room,
            'property' => $property,
            'blocks'   => $blocks,
            'floors'   => $floors,
        ]);
    }
    public function update(Request $request, $id)
    {
        $room = MeetingRoom::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255',
            'capacity'    => 'required|integer|min:1',
            'location'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'property'    => 'required|integer',
            'block'       => 'required|integer',
            'floor'       => 'required|integer',
            'rent_amount' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            /** ================= UPDATE ROOM ================= */
            $room->update([
                'name'                   => $request->name,
                'capacity'               => $request->capacity,
                'location'               => $request->location,
                'description'            => $request->description,
                'property_management_id' => $request->property,
                'block_management_id'    => $request->block,
                'floor_management_id'    => $request->floor,
                'rent_amount'            => $request->rent_amount,
            ]);
            $company =  (new Company())->setConnection('tenant')->first();
            $group = (new Groups())
                ->setConnection('tenant')
                ->where('property_id', $request->property)
                ->first();

         

            /** ================= UPDATE LEDGER ================= */
            $ledger = (new MainLedger())
                ->setConnection('tenant')
                ->where('id', $room->ledger_id) 
                ->first();

            if ($ledger) {
                $ledger->update([
                    'code' => $room->name,
                    'name' =>
                    $room->property_unit_management?->code . '-' .
                        $room->block_unit_management?->block?->code . '-' .
                        $room->floor_unit_management?->floor_management_main?->name . '-' .
                        $room->name,

                    'currency'            => $company->currency_code,
                    'group_id'            => $group?->id,
                    'is_taxable'          => $group->is_taxable ?? 0,
                    'vat_applicable_from' => $group->vat_applicable_from ?? null,
                    'tax_rate'            => $group->tax_rate ?? 0,
                    'tax_applicable'      => $group->tax_applicable ?? 0,
                ]);
            }

            /** ================= UPDATE COST CENTER ================= */
            $costCenter = (new CostCenter())
                ->setConnection('tenant')
                ->where('main_id', $room->id)
                ->where('main_type', 'meeting_room')
                ->first();

            if ($costCenter) {
                $costCenter->update([
                    'name' =>
                    $room->property_unit_management->name . '-' .
                        $room->unit_management_main->name . '-' .
                        $room->block_unit_management->block->name . '-' .
                        $room->floor_unit_management->floor_management_main->name,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('meeting_room.list')
                ->withSuccess('Meeting room updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->withErrors('Failed to update meeting room: ' . $e->getMessage());
        }
    }


    public function delete(Request $request)
    {
        $room = MeetingRoom::find($request->id);
        if (!$room) {
            return redirect()->back()->with('error', 'Meeting room not found.');
        }
        $room->delete();
        return redirect()->back()->with('success', 'Meeting room deleted successfully.');
    }
}
