<?php
namespace App\Http\Controllers\facility_transactions;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintCategory;
use App\Models\ComplaintCommentLog;
use App\Models\ComplaintMovement;
use App\Models\Department;
use App\Models\facility\AmcProvider;
use App\Models\facility\Freezing;
use App\Models\facility\Priority;
use App\Models\facility_transactions\ComplaintRegistration;
use App\Models\PropertyManagement;
use App\Models\Schedule;
use App\Models\Tenant;
use App\Models\UnitManagement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComplaintRegisterController extends Controller
{
    public function index(Request $request)
    {
        $ids = $request->bulk_ids;
        // dd($ids);
        if ($request->bulk_action_btn === 'delete' && is_array($ids) && count($ids)) {
            $ComplaintRegistration = (new ComplaintRegistration())->setConnection('tenant')->whereIn('id', $ids)->delete();
            return back()->with('success', __('Delete successfully'));
        }
        if ($request->bulk_action_btn === 'update_department' && is_array($ids) && count($ids)) {
            $data = [];
            if ($request->department_id != -1) {
                $data['department']  = $request->department_id;
                $data['employee_id'] = null;
            }
            if ($request->priority != -1) {
                $data['priority'] = $request->priority;
            }
            if (! empty($data)) {
                (new ComplaintRegistration())->setConnection('tenant')->whereIn('id', $ids)->update($data);
                return back()->with('success', __('general.updated_successfully'));
            }

            return back()->with('error', __('general.not_available'));
        }
        $complaint_registrations = (new ComplaintRegistration())->setConnection('tenant')->query();
        if ($request->report_tenant && $request->report_tenant != -1) {
            $complaint_registrations = $complaint_registrations->where('tenant_id', $request->report_tenant);
        }
        if ($request->report_building && $request->report_building != -1) {
            $complaint_registrations = $complaint_registrations->where('property_management_id', $request->report_building);
        }
        if ($request->report_unit_management && $request->report_unit_management != -1) {
            $complaint_registrations = $complaint_registrations->where('unit_management_id', $request->report_unit_management);
        }
        if ($request->report_complaint_no && $request->report_complaint_no != -1) {
            $complaint_registrations = $complaint_registrations->where('complaint_no', $request->report_complaint_no);
        }
        if ($request->phone_number && $request->phone_number != -1) {
            $complaint_registrations = $complaint_registrations->where('phone_number', $request->phone_number);
        }
        // if ($request->report_unit_management && $request->report_unit_management != -1) {
        //     $complaint_registrations = $complaint_registrations->whereHas('items', function ($query) use ($request) {
        //         $query->where('unit_id', $request->report_unit_management);
        //     });
        // }
        if ($request->start_date && $request->end_date) {
            $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
            $endDate   = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');
            $complaint_registrations->whereBetween('created_at', [$startDate, $endDate]);
        }
        $complaints = $complaint_registrations->with('unit_management', 'unit_management.block_unit_management', 'unit_management.property_unit_management'
            , 'unit_management.floor_unit_management', 'unit_management.unit_management_main', 'MainPriority')->orderBy('created_at', 'desc')->paginate();
        // $complaints = (new ComplaintRegistration())->setConnection('tenant')->with('unit_management' , 'unit_management.block_unit_management' , 'unit_management.property_unit_management'
        // , 'unit_management.floor_unit_management' ,'unit_management.unit_management_main' , 'MainPriority')->orderBy('created_at', 'desc')->paginate();
        $tenants = (new Tenant())->setConnection('tenant')->get();

        $priorities = collect(DB::connection('tenant')->select('SELECT * FROM priorities'));
       
        $departments     = collect(DB::connection('tenant')->select('SELECT * FROM departments')); //;Department::get();
        $all_building    = (new PropertyManagement())->setConnection('tenant')->forUser()->get();
        $unit_management = (new UnitManagement())->setConnection('tenant')->with(['property_unit_management', 'block_unit_management', 'block_unit_management.block',
            'floor_unit_management', 'floor_unit_management.floor_management_main', 'unit_management_main', 'unit_description'])->get();

        $data = [
            'all_building'    => $all_building,
            'unit_management' => $unit_management,
            'tenants'         => $tenants,
            'priorities'      => $priorities,
            'complaints'      => $complaints,
            'all_departments' => $departments,
        ];
        return view('admin-views.facility_transactions.complaint.complaint_list', $data);
    }
    public function OpenComplaint(Request $request)
    {
        $ids = $request->bulk_ids;
        if ($request->bulk_action_btn === 'delete' && is_array($ids) && count($ids)) {
            $ComplaintRegistration = (new ComplaintRegistration())->setConnection('tenant')->where('status', 'open')->whereIn('id', $ids)->delete();
            return back()->with('success', __('general.deleted_successfully'));
        }
        $all_building    = (new PropertyManagement())->setConnection('tenant')->forUser()->get();
        $unit_management = (new UnitManagement())->setConnection('tenant')->with(['property_unit_management', 'block_unit_management', 'block_unit_management.block',
            'floor_unit_management', 'floor_unit_management.floor_management_main', 'unit_management_main', 'unit_description'])->get();

        $complaints = (new ComplaintRegistration())->setConnection('tenant')->where('status', 'open')->with('unit_management', 'unit_management.block_unit_management', 'unit_management.property_unit_management'
            , 'unit_management.floor_unit_management', 'unit_management.unit_management_main', 'MainPriority')->orderBy('created_at', 'desc')->paginate();

        $departments = collect(DB::connection('tenant')->select('SELECT * FROM departments')); //;Department::get();
        $tenants     = collect(DB::connection('tenant')->select('SELECT id , name FROM tenants ORDER BY created_at DESC'));
        $priorities  = collect(DB::connection('tenant')->select('SELECT * FROM priorities'));
        $data        = [
            'tenants'         => $tenants,
            'priorities'      => $priorities,
            'complaints'      => $complaints,
            'all_departments' => $departments,
            'all_building'    => $all_building,
            'unit_management' => $unit_management,

        ];
        return view('admin-views.facility_transactions.complaint.complaint_list', $data);
    }
    public function FreezedComplaintIndex(Request $request)
    {
        $ids = $request->bulk_ids;
        if ($request->bulk_action_btn === 'delete' && is_array($ids) && count($ids)) {
            $ComplaintRegistration = (new ComplaintRegistration())->setConnection('tenant')->where('status', 'freezed')->whereIn('id', $ids)->delete();
            return back()->with('success', __('Delete successfully'));
        }
        $complaints = (new ComplaintRegistration())->setConnection('tenant')->where('status', 'freezed')->with('unit_management', 'unit_management.block_unit_management', 'unit_management.property_unit_management'
            , 'unit_management.floor_unit_management', 'unit_management.unit_management_main', 'MainPriority')->orderBy('created_at', 'desc')->paginate();
        $departments     = collect(DB::connection('tenant')->select('SELECT * FROM departments')); //;Department::get();
        $tenants         = collect(DB::connection('tenant')->select('SELECT id , name FROM tenants ORDER BY created_at DESC'));
        $priorities      = collect(DB::connection('tenant')->select('SELECT * FROM priorities'));
        $all_building    = (new PropertyManagement())->setConnection('tenant')->forUser()->get();
        $unit_management = (new UnitManagement())->setConnection('tenant')->with(['property_unit_management', 'block_unit_management', 'block_unit_management.block',
            'floor_unit_management', 'floor_unit_management.floor_management_main', 'unit_management_main', 'unit_description'])->get();

        $data = [
            'tenants'         => $tenants,
            'priorities'      => $priorities,
            'complaints'      => $complaints,
            'all_departments' => $departments,
            'all_building'    => $all_building,
            'unit_management' => $unit_management,
        ];

        return view('admin-views.facility_transactions.complaint.complaint_list', $data);
    }
    public function ClosedComplaintIndex(Request $request)
    {
        $ids = $request->bulk_ids;
        if ($request->bulk_action_btn === 'delete' && is_array($ids) && count($ids)) {
            $ComplaintRegistration = (new ComplaintRegistration())->setConnection('tenant')->where('status', 'closed')->whereIn('id', $ids)->delete();
            return back()->with('success', __('Delete successfully'));
        }

        $complaints = (new ComplaintRegistration())->setConnection('tenant')->where('status', 'closed')->with('unit_management', 'unit_management.block_unit_management', 'unit_management.property_unit_management'
            , 'unit_management.floor_unit_management', 'unit_management.unit_management_main', 'MainPriority')->orderBy('created_at', 'desc')->paginate();
        $departments     = collect(DB::connection('tenant')->select('SELECT * FROM departments')); //;Department::get();
        $tenants         = collect(DB::connection('tenant')->select('SELECT id , name FROM tenants ORDER BY created_at DESC'));
        $priorities      = collect(DB::connection('tenant')->select('SELECT * FROM priorities'));
        $all_building    = (new PropertyManagement())->setConnection('tenant')->forUser()->get();
        $unit_management = (new UnitManagement())->setConnection('tenant')->with(['property_unit_management', 'block_unit_management', 'block_unit_management.block',
            'floor_unit_management', 'floor_unit_management.floor_management_main', 'unit_management_main', 'unit_description'])->get();

        $data = [
            'tenants'         => $tenants,
            'priorities'      => $priorities,
            'complaints'      => $complaints,
            'all_departments' => $departments,
            'all_building'    => $all_building,
            'unit_management' => $unit_management,
        ];
        return view('admin-views.facility_transactions.complaint.complaint_list', $data);
    }

    public function create()
    {
        $tenants              = (new Tenant())->setConnection('tenant')->get();
        $complaint_categories = (new ComplaintCategory())->setConnection('tenant')->get();
        $sub_complaints       = (new Complaint())->setConnection('tenant')->get();
        $departments          = (new Department())->setConnection('tenant')->get();
        $priorities           = (new Priority())->setConnection('tenant')->get();
        $freezings            = (new Freezing())->setConnection('tenant')->get();
        $amc_provider         = (new AmcProvider())->setConnection('tenant')->select('id', 'name')->get();
        return view('admin-views.facility_transactions.complaint.create', compact('tenants', 'complaint_categories', 'sub_complaints', 'departments', 'priorities', 'freezings', 'amc_provider'));
    }

    public function storeComplaint(Request $request)
    {
        $request->validate([
            "tenant_id"          => "required",
            "complaint"          => "required",
            "complaint_category" => "required",
            "unit_id"            => "required",
            "priority"           => "required",
            "department"         => "required",
            'attachment'         => 'file|mimes:jpg,jpeg,png,webp,pdf|max:8192',
        ], [
            'tenant_id.required'    => "Please Select Tenant",
            'phone_number.required' => "Please Enter Phone Number",
            'unit_id.required'      => "Please Select Unit",
        ]);

        $unit_management = (new UnitManagement())->setConnection('tenant')->where('id', $request->unit_id)->first();
        try {
            $uploadedFile = uploadFile($request, 'complaint');

            $is_created = (new ComplaintRegistration())->setConnection('tenant')->create([
                "unit_management_id"     => $request->unit_id,
                "attachment"             => $uploadedFile ? $uploadedFile['path'] : null,
                "attachment_type"        => $uploadedFile ? $uploadedFile['type'] : null,
                "property_management_id" => $unit_management->property_management_id,
                "block_id"               => $unit_management->block_management_id,
                "floor_id"               => $unit_management->floor_management_id,
                "tenant_id"              => $request->tenant_id ?? null,
                "phone_number"           => $request->phone_number ?? null,
                "complaint_no"           => complaintNo() ?? null,
                "complainer_name"        => $request->complainer_name ?? null,
                "email"                  => $request->email ?? null,
                "complaint"              => $request->complaint ?? null,
                "complaint_category"     => $request->complaint_category ?? null,
                "complaint_comment"      => $request->complaint_comment ?? null,
                "department"             => $request->department ?? null,
                "priority"               => $request->priority ?? null,
                "employee_id"            => ((isset($request->employee_id) && $request->employee_id != -1) ? $request->employee_id : $request->employee_id_amc),
                "employee_type"          => ((isset($request->employee_type)) ? $request->employee_type : null),
                "schedule_date"          => ((isset($request->employee_type) && ((isset($request->employee_id) && $request->employee_id != -1) || (isset($request->employee_id_amc) && $request->employee_id_amc != -1))) ? Carbon::now() : null),
                "company_id"             => auth()->id() ?? 2,
            ]);
            if ($request->hasFile('attachment')) {
                $is_created->attachments()->create([
                    'name'      => $request->file('attachment')->getClientOriginalName(),
                    'file_path' => $uploadedFile['path'],
                    'file_type' => $uploadedFile['type'],
                ]);
            }
            if ($request->hasFile('attachment')) {
                (new ComplaintMovement())->setConnection('tenant')->create([
                    'complaint_id'    => $is_created->id,
                    'date'            => Carbon::createFromFormat('d/m/Y', $request->attachment_date)->format('Y-m-d'),
                    'time'            => $request->attachment_time,
                    'activity'        => ui_change('add_attachment', 'facility_transaction'),
                    'attachment'      => $request->file('attachment')->getClientOriginalName(),
                    'attachment_type' => $uploadedFile['type'],
                    'notes'           => $request->attachment_note,
                    'user_id'         => auth()->id(),
                ]);
            }
            if ((isset($request->employee_id) && $request->employee_id != -1)) {
                (new ComplaintMovement())->setConnection('tenant')->create([
                    'complaint_id'  => $is_created->id,
                    'date'          => Carbon::createFromFormat('d/m/Y', $request->employee_date)->format('Y-m-d'),
                    'time'          => $request->employee_time,
                    'activity'      => ui_change('assigned_to_employee', 'facility_transaction'),
                    // 'attachment'      => $request->file('attachment')->getClientOriginalName(),
                    // 'attachment_type' => $uploadedFile['type'],
                    'notes'         => $request->employee_note,
                    'user_id'       => auth()->id(),
                    'employee_id'   => ((isset($request->employee_id) && $request->employee_id != -1) ? $request->employee_id : null),
                    'department_id' => $request->department ?? null,
                ]);
            }
            return redirect()->route('complaint_registration')->with('success', __('property_master.added_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }

    }
    public function getComplaintDepartments($id)
    {
        $complaint_category = (new ComplaintCategory())->setConnection('tenant')->find($id);
        if ($complaint_category) {
            $department = (new Department())->setConnection('tenant')->where('id', $complaint_category->department_id)->first();
        }
        return json_encode($department);
    }
    public function getComplaintCategories($id)
    {
        $complaint_category = (new ComplaintCategory())->setConnection('tenant')->find($id);
        if ($complaint_category) {
            $compaints = (new Complaint())->setConnection('tenant')->where('complaint_category_id', $id)->get();
        } else {

            $compaints = (new Complaint())->setConnection('tenant')->get();
        }
        return json_encode($compaints);
    }
    // ->where(function ($query) use ($month) {
    //     $query->whereMonth('from', '<=', $month)
    //           ->whereMonth('to', '>=', $month);
    // })
    public function getUnits($id)
    {
        $tenant   = (new Tenant())->setConnection('tenant')->find($id);
        $month    = Carbon::now()->month;
        $unit_ids = (new Schedule())->setConnection('tenant')->where('tenant_id', $id)
            ->distinct()
            ->pluck('unit_id');
        $units = (new UnitManagement())->setConnection('tenant')->whereIn('id', $unit_ids)
            ->with([
                'property_unit_management',
                'block_unit_management.block',
                'block_unit_management',
                'floor_unit_management.floor_management_main',
                'floor_unit_management',
                'unit_management_main',
                'unit_description',
            ])
            ->get();
        return json_encode($units);
    }

    // $units[] = UnitManagement::where('id' , $id)->with(['property' , 'block' , 'floor' ,'unit'])->first();
    public function show_logs($id)
    {

        $complaint = (new ComplaintRegistration())->setConnection('tenant')->with('unit_management', 'unit_management.block_unit_management', 'unit_management.property_unit_management'
            , 'unit_management.floor_unit_management', 'unit_management.unit_management_main', 'MainPriority')->find($id);
        // $unreadComplaintNotification = auth()->user()->unreadNotifications->filter(function ($notification) use($id) {
        //     return isset($notification->data['id']) && $notification->data['id'] == $id && $notification->type === ComplaintNotification::class;
        // });

        // if ($unreadComplaintNotification) {
        //     $unreadComplaintNotification->markAsRead();
        // }
        // $comment_logs = collect(DB::connection('tenant')->select('SELECT * FROM complaint_comment_logs WHERE complaint_registration_id = ?', [$id]));
        // $comment_logs = (new ComplaintCommentLog())->setConnection('tenant')->with('auther')->where('complaint_registration_id', $id)->get();
        $comment_logs = (new ComplaintMovement())->setConnection('tenant')->where('complaint_id', $id)->get();
        $freezing     = (new Freezing())->setConnection('tenant')->get();
        $departments  = (new Department())->setConnection('tenant')->get();
        $data         = [
            'freezing'     => $freezing,
            'complaint'    => $complaint,
            'departments'  => $departments,
            'comment_logs' => $comment_logs,
        ];
        return view('admin-views.facility_transactions.complaint.show_logs', $data);

    }
    public function showComplaint($id)
    {

        $complaint = (new ComplaintRegistration())->setConnection('tenant')->with('unit_management', 'unit_management.block_unit_management', 'unit_management.property_unit_management'
            , 'unit_management.floor_unit_management', 'unit_management.unit_management_main')->find($id);
        // $unreadComplaintNotification = auth()->user()->unreadNotifications->filter(function ($notification) use($id) {
        //     return isset($notification->data['id']) && $notification->data['id'] == $id && $notification->type === ComplaintNotification::class;
        // });

        // if ($unreadComplaintNotification) {
        //     $unreadComplaintNotification->markAsRead();
        // }
        $comment_logs = collect(DB::connection('tenant')->select('SELECT * FROM complaint_comment_logs WHERE complaint_registration_id = ?', [$id]));
        $freezing     = (new Freezing())->setConnection('tenant')->get();
        $departments  = (new Department())->setConnection('tenant')->get();
        $data         = [
            'freezing'     => $freezing,
            'complaint'    => $complaint,
            'departments'  => $departments,
            'comment_logs' => $comment_logs,
        ];
        return view('admin-views.facility_transactions.complaint.show', $data);

    }
    public function editComplaint($id)
    {

        $complaint            = (new ComplaintRegistration())->setConnection('tenant')->findOrFail($id);
        $tenants              = (new Tenant())->setConnection('tenant')->get();
        $complaint_categories = (new ComplaintCategory())->setConnection('tenant')->get();
        $sub_complaints       = (new Complaint())->setConnection('tenant')->get();
        $departments          = (new Department())->setConnection('tenant')->get();
        $priorities           = (new Priority())->setConnection('tenant')->get();
        $main_unit            = (new UnitManagement())->setConnection('tenant')->where('id', $complaint->unit_management_id)
            ->with([
                'property_unit_management',
                'block_unit_management.block',
                'block_unit_management',
                'floor_unit_management.floor_management_main',
                'floor_unit_management',
                'unit_management_main',
                'unit_description',
            ])
            ->first();
        $amc_provider = (new AmcProvider())->setConnection('tenant')->select('id', 'name')->get();
        return view('admin-views.facility_transactions.complaint.edit', compact('main_unit', 'complaint', 'tenants', 'complaint_categories', 'sub_complaints', 'departments', 'priorities', 'amc_provider'));

    }
    public function updateComplaint(Request $request, $id)
    {

        $complaint = (new ComplaintRegistration())->setConnection('tenant')->find($id);
        $request->validate([
            "tenant_id" => "required",
        ], [
            'tenant_id.required'    => "Please Select Tenant",
            'phone_number.required' => "Please Enter Phone Number",
            'unit_id.required'      => "Please Select Unit",
        ]);
        DB::beginTransaction();
        try {
            if (isset($request->unit_id)) {
                $unit_management = (new UnitManagement())->setConnection('tenant')->where('id', $request->unit_id)->first();
            } else {
                $unit_management = (new UnitManagement())->setConnection('tenant')->where('id', $complaint->unit_management_id)->first();

            }
            $data = [
                "unit_management_id"     => $request->unit_id ?? $complaint->unit_management_id,
                "property_management_id" => $unit_management->property_management_id ?? $complaint->property_management_id,
                "block_id"               => $unit_management->block_management_id ?? $complaint->block_id,
                "floor_id"               => $unit_management->floor_management_id ?? $complaint->floor_id,
                "tenant_id"              => $request->tenant_id ?? $complaint->tenant_id,
                "phone_number"           => $request->phone_number ?? $complaint->phone_number,
                "complainer_name"        => $request->complainer_name ?? $complaint->complainer_name,
                "email"                  => $request->email ?? $complaint->email,
                "complaint"              => $request->complaint ?? $complaint->complaint,
                "complaint_category"     => $request->complaint_category ?? $complaint->complaint_category,
                "complaint_comment"      => $request->complaint_comment ?? $complaint->complaint_comment,
                "department"             => $request->department ?? $complaint->department,
                "priority"               => $request->priority ?? $complaint->priority,
                "employee_id"            => ((isset($request->employee_id)) ? $request->employee_id : $request->employee_id_amc),
                "employee_type"          => ((isset($request->employee_type)) ? $request->employee_type : null),
                "company_id"             => auth()->id() ?? $complaint->company_id,
            ];
            if ($request->hasFile('attachment')) {
                if (! empty($complaint->attachment)) {
                    $oldFilePath = public_path($complaint->attachment);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
                $uploadedFile            = uploadFile($request, 'complaint');
                $data["attachment"]      = $uploadedFile['path'];
                $data["attachment_type"] = $uploadedFile['type'];
            }

            if ($request->complaint_comment && $request->complaint_comment !== $complaint->complaint_comment) {
                $data["complaint_comment"] = $request->complaint_comment;
                (new ComplaintCommentLog())->setConnection('tenant')->create([
                    'complaint_registration_id' => $complaint->id,
                    'old_comment'               => $complaint->complaint_comment,
                    'new_comment'               => $request->complaint_comment,
                    'updated_by'                => auth()->id() ?? (new User())->setConnection('tenant')->first(),
                    'updated_at'                => now(),
                ]);
            }
            $complaint->update($data);
            DB::commit();
            return redirect()->route('complaint_registration')->with('success', 'Updated Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with("error", $e->getMessage());
        }

    }
    public function deleteComplaint(Request $request)
    {

        $complaint = (new ComplaintRegistration())->setConnection('tenant')->find($request->id);
        $complaint->delete();
        return redirect()->route('complaint_registration')->with('success', 'Deleted Successfully!');
    }
    public function freezedComplaint(Request $request, $id)
    {
        $complaint = (new ComplaintRegistration())->setConnection('tenant')->find($id);
        (new ComplaintCommentLog())->setConnection('tenant')->create([
            'complaint_registration_id' => $complaint->id,
            'old_comment'               => "old complaint status " . $complaint->status,
            'new_comment'               => "change complaint status to freezed",
            'updated_by'                => auth()->id() ?? (new User())->setConnection('tenant')->first(),
            'updated_at'                => now(),
        ]);
         (new ComplaintMovement())->setConnection('tenant')->create([
                'complaint_id' => $complaint->id,
                'date'         => Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d'),
                'time'         => $request->time,
                'activity'     => ui_change('change_status_to_freezed', 'facility_transaction'), 
                'notes'        => $request->notes,
                'user_id'      => auth()->id(), 
            ]);
        $complaint->update([
            'status'         => 'freezed',
            'freezed_reason' => $request->freezed_reason,
            'freezing_notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Freezed Successfully!');
    }
    public function closedComplaint(Request $request, $id)
    {
        $complaint = (new ComplaintRegistration())->setConnection('tenant')->find($id);
        (new ComplaintCommentLog())->setConnection('tenant')->create([
            'complaint_registration_id' => $complaint->id,
            'old_comment'               => "old complaint status " . $complaint->status,
            'new_comment'               => "change complaint status to closed",
            'updated_by'                => auth()->id() ?? (new User())->setConnection('tenant')->first(),
            'updated_at'                => now(),
        ]);
        $complaint->update([
            'status' => 'closed',
            'notes'  => $request->notes,
            'worker' => $request->department,
        ]);
        return redirect()->back()->with('success', 'Closed Successfully!');
    }

    public function viewPdf($id)
    {
        $complaint = (new ComplaintRegistration())->setConnection('tenant')->findOrFail($id);

        $filePath = public_path($complaint->attachment);
        if (! file_exists($filePath)) {
            return back()->with('error', 'الملف غير موجود');
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function get_employees_departments($id)
    {
        $employees = get_employees_by_department_id($id);

        if ($employees) {
            return response()->json([
                'status'    => 200,
                "employees" => $employees,
            ]);
        } else {
            return response()->json([
                'status'  => 404,
                "message" => "Employees Not Found",
            ]);
        }
    }
    public function assign_to_employee(Request $request)
    {
        $complaint = (new ComplaintRegistration())->setConnection('tenant')->findOrFail($request->complaint_id_to_assign);
        $complaint->update([
            'employee_id' => $request->employee_id,
        ]);
        (new ComplaintCommentLog())->setConnection('tenant')->create([
            'complaint_registration_id' => $complaint->id,
            'old_comment'               => "assing Complaint To ",
            'new_comment'               => $complaint->employee->name,
            'updated_by'                => auth()->id() ?? (new User())->setConnection('tenant')->first(),
            'updated_at'                => now(),
        ]);
        if ((isset($request->employee_id) && $request->employee_id != -1)) {
            (new ComplaintMovement())->setConnection('tenant')->create([
                'complaint_id' => $complaint->id,
                'date'         => Carbon::createFromFormat('d/m/Y', $request->employee_date)->format('Y-m-d'),
                'time'         => $request->employee_time,
                'activity'     => ui_change('assigned_to_employee', 'facility_transaction'),
                // 'attachment'      => $request->file('attachment')->getClientOriginalName(),
                // 'attachment_type' => $uploadedFile['type'],
                'notes'        => $request->employee_note,
                'user_id'      => auth()->id(),
                'employee_id'  => $request->employee_id,
            ]);
        }
        return redirect()->route('complaint_registration')->with('success', 'Updated Successfully!');
    }
    public function assign_to_department(Request $request)
    {
        // dd($request->all());
        $complaint = (new ComplaintRegistration())->setConnection('tenant')->findOrFail($request->complaint_id);
        // $complaint->update([
        //     'department' => $request->department_id,
        //     'priority'   => $request->priority,
        // ]);
        // (new ComplaintCommentLog())->setConnection('tenant')->create([
        //     'complaint_registration_id' => $complaint->id,
        //     'old_comment'               => "assing Complaint To Department",
        //     'new_comment'               => $complaint->MainDepartment->name,
        //     'updated_by'                => auth()->id() ?? (new User())->setConnection('tenant')->first(),
        //     'updated_at'                => now(),
        // ]);
        $uploadedFile = uploadFile($request, 'complaint');
        // dd($request ,$uploadedFile  ) ;
        if ($request->hasFile('attachment')) {
            $complaint->attachments()->create([
                'name'      => $request->file('attachment')->getClientOriginalName(),
                'file_path' => $uploadedFile['path'],
                'file_type' => $uploadedFile['type'],
            ]);
        }
        if ($request->hasFile('attachment')) {
            (new ComplaintMovement())->setConnection('tenant')->create([
                'complaint_id'    => $complaint->id,
                'date'            => Carbon::createFromFormat('d/m/Y', $request->attachment_date)->format('Y-m-d'),
                'time'            => $request->attachment_time,
                'activity'        => ui_change('add_attachment', 'facility_transaction'),
                'attachment'      => $request->file('attachment')->getClientOriginalName(),
                'attachment_type' => $uploadedFile['type'],
                'notes'           => $request->attachment_note,
                'user_id'         => auth()->id(),
            ]);
        }
        return redirect()->route('complaint_registration')->with('success', 'Updated Successfully!');
    }
    public function get_employees_departments_complaint($id)
    {
        $complaint = (new ComplaintRegistration())->setConnection('tenant')->findOrFail($id);
        $employees = get_employees_by_department_id($complaint->department);

        if ($employees) {
            return response()->json([
                'status'    => 200,
                "employees" => $employees,
                "complaint" => $complaint,
            ]);
        } else {
            return response()->json([
                'status'  => 404,
                "message" => "Employees Not Found",
            ]);
        }
    }
}
