<?php

namespace App\Http\Controllers\Room_Reservation;

use Carbon\Carbon;
use App\Models\Tenant;
use App\Models\Company;
use App\Models\BookingR;
use App\Models\UnitType;
use App\Models\RentalType;
use App\Models\RoomOption;
use App\Models\BookingRoom;
use App\Models\BookingGuest;
use App\Models\RoomFacility;
use Illuminate\Http\Request;
use App\Models\UnitCondition;
use App\Models\UnitManagement;
use App\Models\BusinessSetting;
use App\Models\UnitDescription;
use App\Models\PropertyManagement;
use Illuminate\Support\Facades\DB;
use App\Models\ReservationSettings;
use App\Http\Controllers\Controller;

class BookingRoomController extends Controller
{
    public function index(Request $request)
    {
        $tenants = Tenant::select('id', 'name', 'company_name')->get();

        $propertyQuery = PropertyManagement::with(
            'blocks_management_child:id,block_id',
            'blocks_management_child.block:id,name',
            'blocks_management_child.floors_management_child:id,floor_id',
            'blocks_management_child.floors_management_child.floor_management_main:id,name',
            'blocks_management_child.floors_management_child.unit_management_child',
            'blocks_management_child.floors_management_child.unit_management_child.unit_management_main'
        )->forUser();

        $unit_descriptions = UnitDescription::select('id', 'name')->get();
        $unit_types = UnitType::select('id', 'name')->get();
        $unit_conditions = UnitCondition::select('id', 'name')->get();
        $unit_facilities = RoomFacility::select('id', 'name')->get();

        $filterUnitDescriptionId = $request->get('unit_description_id', -1);
        $filterUnitTypeId = $request->get('unit_type_id', -1);
        $filterUnitConditionId = $request->get('unit_condition_id', -1);
        $filterUnitFacilityId = $request->get('unit_facility_id', -1);
        $filterAdults = $request->get('adults', null);
        $filterChildren = $request->get('children', null);
        // -------------------------------
        // Apply filters
        // -------------------------------

        $propertyQuery->with(['blocks_management_child.floors_management_child.unit_management_child' => function ($q) use (
            $filterUnitDescriptionId,
            $filterUnitTypeId,
            $filterUnitConditionId,
            $filterUnitFacilityId,
            $filterAdults,
            $filterChildren
        ) {

            if ($filterUnitDescriptionId != -1) {
                $q->where('unit_description_id', $filterUnitDescriptionId);
            }

            if ($filterUnitTypeId != -1) {
                $q->where('unit_type_id', $filterUnitTypeId);
            }

            if ($filterUnitConditionId != -1) {
                $q->where('unit_condition_id', $filterUnitConditionId);
            }

            if ($filterUnitFacilityId != -1) {
                $q->whereHas('facilities', function ($q2) use ($filterUnitFacilityId) {
                    $q2->where('facility_id', $filterUnitFacilityId);
                });
            }

            // Filter by adults
            if ($filterAdults !== null) {
                $q->where('adults', '>=', (int)$filterAdults);
            }

            // Filter by children
            if ($filterChildren !== null) {
                $q->where('children', '>=', (int)$filterChildren);
            }
        }]);


        $property = $propertyQuery->get();
        $agreement_color = BusinessSetting::whereType('agreement_color')->value('value');
        $booking_color   = BusinessSetting::whereType('booking_color')->value('value');
        $proposal_color  = BusinessSetting::whereType('proposal_color')->value('value');
        $enquiry_color   = BusinessSetting::whereType('enquiry_color')->value('value');
        $booked_color    = optional(ReservationSettings::where('setting_name', 'booked_color')->first())->setting_value;
        $show_agreement    = optional(ReservationSettings::where('setting_name', 'show_agreement')->first())->setting_value;

        $data = [
            'property_items' => $property,
            'tenants' => $tenants,
            'unit_descriptions' => $unit_descriptions,
            'unit_types' => $unit_types,
            'unit_conditions' => $unit_conditions,
            'unit_facilities' => $unit_facilities,
            'filterUnitDescriptionId' => $filterUnitDescriptionId,
            'filterUnitTypeId' => $filterUnitTypeId,
            'filterUnitConditionId' => $filterUnitConditionId,
            'filterUnitFacilityId' => $filterUnitFacilityId,
            'filterAdults' => $filterAdults,
            'filterChildren' => $filterChildren,
            'agreement_color'   => $agreement_color,
            'booking_color'   => $booking_color,
            'proposal_color'   => $proposal_color,
            'enquiry_color'   => $enquiry_color,
            'booked_color'   => $booked_color,
            'show_agreement'   => $show_agreement,
        ];
        return view('admin-views.room_reservation.booking_room.book', $data);
    }
    public function check_in_page(Request $request)
    {

        $ids = $request->bulk_ids;
        if ($ids == null) {
            return redirect()->back()->with('error', 'Please Select Unit');
        }
        $tenant = Tenant::select('id', 'name', 'company_name', 'address1' , 'id_number','contact_no')->get();
        $unit_managements = UnitManagement::whereIn('id', $ids)->get();
        $room_options = RoomOption::select('id', 'name')->get();
        $rental_types = RentalType::select('id', 'name')->get();
        $company = (new Company())->setConnection('tenant')->with('levy')->first();
        $data = [
            'unit_managements' => $unit_managements,
            'tenants' => $tenant,
             'company' => $company,
            'room_options'  => $room_options,
            'rental_types'  => $rental_types,
        ];
        return view('admin-views.room_reservation.booking_room.check_in_dir', $data);
    }
    public function create(Request $request)
    {
        $ids = $request->bulk_ids;
        if ($ids == null) {
            return redirect()->back()->with('error', 'Please Select Unit');
        }
        // $company = Company::select('id', 'name', 'decimals')->first();
        $all_units                = UnitManagement::select(
            'id',
            'adults',
            'children',
            'property_management_id',
            'booking_status',
            'view_id',
            'unit_type_id',
            'unit_condition_id',
            'unit_description_id',
            'unit_id',
            'block_management_id',
            'floor_management_id'
        )->whereIn('id', $ids)
            ->with(
                'block_unit_management',
                'property_unit_management',
                'block_unit_management.block',
                'floor_unit_management.floor_management_main',
                'floor_unit_management',
                'unit_management_main',
                'unit_description',
                'unit_type',
                'view',
                'unit_condition'
            )->lazy();

        $tenants = Tenant::select('id', 'name', 'company_name')->get();
        $room_options = RoomOption::select('id', 'name')->get();
        $rental_types = RentalType::select('id', 'name')->get();
        $company = (new Company())->setConnection('tenant')->with('levy')->first();
        $data = [
            'tenants' => $tenants,
            'all_units' => $all_units,
            'company' => $company,
            'room_options'  => $room_options,
            'rental_types'  => $rental_types,
        ];
        return view('admin-views.room_reservation.booking_room.create', $data);
    }
    public function store(Request $request)
    {
        $booking_from = Carbon::createFromFormat('d/m/Y', $request->booking_from)->format('Y-m-d');
        $booking_to   = Carbon::createFromFormat('d/m/Y', $request->booking_to)->format('Y-m-d');
        $booking_date = ($request->booking_date) ? Carbon::createFromFormat('d/m/Y', $request->booking_date)->format('Y-m-d') : Carbon::now()->format('Y-m-d');

        $booking = BookingR::create([
            'tenant_id'         => $request->tenant_id,
            'booking_date'      => $booking_date,
            'booking_from'      => $booking_from,
            'booking_to'        => $booking_to,
            'rental_type_id'    => $request->rental_type_id,
            'summary_total'     => $request->summary_total,
            'summary_discount'  => $request->summary_discount,
            'summary_gross'     => $request->summary_gross,
            'summary_vat'       => $request->summary_vat,
            'summary_levy'      => $request->summary_levy,
            'summary_net'       => $request->summary_net,
            'adults'            => $request->adults ?? 0,
            'children'          => $request->children ?? 0,
        ]);

        foreach ($request->room_ids as $room_id) {

            if (!is_numeric($room_id)) continue;

            BookingRoom::create([
                'booking_id'    => $booking->id,
                'room_id'       => (int)$room_id,
                'days'          => $request->days[$room_id] ?? 0,
                'rent_price'    => $request->rent_price[$room_id] ?? 0,
                'discount_per'  => $request->discount_per[$room_id] ?? 0,
                'discount'      => $request->discount[$room_id] ?? 0,
                'gross'         => $request->gross[$room_id] ?? 0,
                'vat_per'       => $request->vat_per[$room_id] ?? 0,
                'levy'          => $request->levy[$room_id] ?? 0,
                'net_total'     => $request->net_total[$room_id] ?? 0,
            ]);
            UnitManagement::whereId((int)$room_id)->update([
                'booking_status' => 'booked',
            ]);
        }
        return redirect()->route('booking_room.list')->with('success', 'Booking saved successfully.');
    }

    public function list(Request $request)
    {
        $ids     = $request->bulk_ids;
        $search      = $request['search'];
        $query_param = $search ? ['search' => $request['search']] : '';

        $bookings = (new BookingR())->setConnection('tenant')->when($request['search'], function ($q) use ($request) {
            $key = explode(' ', $request['search']);
            // foreach ($key as $value) {
            //     $q->Where('enquiry_no', 'like', "%{$value}%")
            //         ->orWhere('id', $value);
            // }
        })

            ->latest()
            ->orderBy('created_at', 'desc')
            ->paginate()
            ->appends($query_param);
        $bookings = BookingR::with(['rooms.unit_management:id,unit_id', 'rooms.unit_management.unit_management_main:id,name', 'tenant'])
            ->orderBy('booking_date', 'desc')
            ->paginate(20);

        $data = [
            'search'    => $search,
            'bookings'    => $bookings,
        ];

        return view('admin-views.room_reservation.booking_room.list', $data);
    }

    public function check_in($id)
    {
        $booking_r = BookingR::active()->with('rooms', 'tenant')->findOrFail($id);

        $data = [
            'booking_r' => $booking_r,
        ];
        return view('admin-views.room_reservation.booking_room.check_in', $data);
    }


    public function submitCheckin(Request $request, $bookingId)
    {
        // $booking_r = BookingR::active()->findOrFail($bookingId); 
        $request->validate([
            'checkin_time' => 'required',

            'guests.adults.*.name'      => 'required|string|max:255',
            'guests.adults.*.room_id'  => 'required',
            'guests.adults.*.gender'   => 'required|in:male,female',
            'guests.adults.*.dob'      => 'required',
            'guests.adults.*.age'      => 'required|integer',
            'guests.adults.*.id_type'  => 'required',
            'guests.adults.*.id_file'  => 'required|file|mimes:jpg,jpeg,png,pdf',

            'guests.children.*.name'      => 'required|string|max:255',
            'guests.children.*.room_id'  => 'required',
            'guests.children.*.gender'   => 'required|in:male,female',
            'guests.children.*.dob'      => 'required',
            'guests.children.*.id_type'  => 'required',
            'guests.children.*.id_file'  => 'required|file|mimes:jpg,jpeg,png,pdf',
        ]);
        DB::transaction(function () use ($request, $bookingId) {

            foreach ($request->guests['adults'] ?? [] as $guest) {

                $filePath = $guest['id_file']->store('guests_ids', 'public');
                $dob =  Carbon::createFromFormat('d/m/Y', $guest['dob']);
                $age = $dob->age;
                BookingGuest::create([
                    'booking_id' => $bookingId,
                    'room_id'    => $guest['room_id'],
                    'guest_type' => 'adult',
                    'full_name'  => $guest['name'],
                    'gender'     => $guest['gender'],
                    'dob'        => $dob->format('Y-m-d'),
                    'age'        => $guest['age'],
                    'id_type'    => $guest['id_type'],
                    'id_file'    => $filePath,
                ]);
            }

            foreach ($request->guests['children'] ?? [] as $guest) {

                $filePath = $guest['id_file']->store('guests_ids', 'public');

                $dob =  Carbon::createFromFormat('d/m/Y', $guest['dob']);
                $age = $dob->age;

                BookingGuest::create([
                    'booking_id' => $bookingId,
                    'room_id'    => $guest['room_id'],
                    'guest_type' => 'child',
                    'full_name'  => $guest['name'],
                    'gender'     => $guest['gender'],
                    'dob'        => $dob->format('Y-m-d'),
                    'age'        => $age,
                    'id_type'    => $guest['id_type'],
                    'id_file'    => $filePath,
                ]);
            }
        });

        return redirect()
            ->back()
            ->with('success', 'Guests checked-in successfully');
    }
    public function submitCheckOut($id)
    {
        $booking = BookingR::findOrFail($id);
        if ($booking->status !== 'check_in') {
            return redirect()->back()->with('error', 'This booking cannot be checked out.');
        }

        $booking->status = 'check_out';
        $booking->checked_out_at = now();
        $booking->save();

        return redirect()->route('booking_room.list')->with('success', 'Booking checked out successfully.');
    }

    public function submitCheckinDir(Request $request){
        dd($request->all());
    }
}
