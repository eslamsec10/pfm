<?php

namespace App\Http\Controllers\Admin\RolesAndCompanyManagement;

use App\Models\Role;
use App\Models\Section;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
// use Mpdf\Mpdf;
// use PDF;
class RoleController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('show_admin_roles');
        $search      = $request['search'];
        $query_param = $search ? ['search' => $request['search']] : '';

        $roles = Role::when($request['search'], function ($q) use($request){
                $key = explode(' ', $request['search']);
                foreach ($key as $value) {
                    $q->Where('name', 'like', "%{$value}%")
                      ->orWhere('id', $value);
                }
            })
            ->latest()->paginate()->appends($query_param);

        if(isset($search) && empty($search)) {
            $roles = Role::with('users')
            ->orderBy('created_at', 'asc')
            ->paginate(10);
        }

        
        $data = [
            'roles' => $roles,
            'search' => $search,
        ];

        return view('admin-views.roles.lists', $data);
    }

    public function create()
    {
        $this->authorize('create_admin_roles');
        $roles = Role::with('users')
            ->orderBy('created_at', 'asc')
            ->paginate(10);
        $sections = Section::whereNull('section_group_id')
            ->with('children')
            ->get();

        $data = [
            'pageTitle' => trans('admin/main.role_new_page_title'),
            'sections' => $sections,
            'roles' => $roles,
        ];

        return view('admin-views.roles.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('create_admin_roles');

        $request->validate(  [
            'name' => 'required|min:3|max:64|unique:roles,name',
            'caption' => 'required|min:3|max:64|unique:roles,caption',
        ]);

        $data = $request->all();

        $role = Role::create([
            'name' => $data['name'],
            'caption' => $data['caption'],
            'is_admin' => (!empty($data['is_admin']) and $data['is_admin'] == 'on'),
            'created_at' => time(),
        ]);

        if ($request->has('permissions')) {
            $this->storePermission($role, $data['permissions']);
        }

        Cache::forget('sections');

        return redirect()->route('roles')->with('success' , "Created Successfully");
    }

    public function edit($id)
    {
        $this->authorize('edit_admin_roles');

        $role = Role::find($id);
        $permissions = Permission::where('role_id', '=', $role->id)->get();
        $sections = Section::whereNull('section_group_id')
            ->with('children')
            ->get();

        $data = [
            'role' => $role,
            'sections' => $sections,
            'permissions' => $permissions->keyBy('section_id')
        ];

        return view('admin-views.roles.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('update_admin_roles');

        $role = Role::find($id);

        $data = $request->all();

        $role->update([
            'caption' => $data['caption'],
            'is_admin' => ((!empty($data['is_admin']) and $data['is_admin'] == 'on') or $role->name == Role::$admin),
        ]);

        Permission::where('role_id', '=', $role->id)->delete();

        if (!empty($data['permissions'])) {
            $this->storePermission($role, $data['permissions']);
        }

        Cache::forget('sections');

        return redirect()->route('roles')->with('success' , "Updated Successfully");
    }

    public function destroy(Request $request)
    {
        $this->authorize('delete_admin_roles');

        $role = Role::find($request->id);
        if ($role->id !== 2) {
            $role->delete();
        }

        return redirect()->route('roles')->with('success' , "Deleted Successfully");
    }

    public function storePermission($role, $sections)
    {
        $sectionsId = Section::whereIn('id', $sections)->pluck('id');
        $permissions = [];
        foreach ($sectionsId as $section_id) {
            $permissions[] = [
                'role_id' => $role->id,
                'section_id' => $section_id,
                'allow' => true,
            ];
        }
        Permission::insert($permissions);
    }

    // public function print(){
    //     $invoice = [
    //         'invoice_number' => 'INV12345',
    //         'customer_name' => 'محمد أحمد',
    //         'customer_email' => 'mohamed.ahmed@example.com',
    //         'invoice_date' => '2024-12-02',
    //         'due_date' => '2024-12-09',
    //         'items' => [
    //             [
    //                 'item_name' => 'خدمة تصميم موقع',
    //                 'quantity' => 1,
    //                 'unit_price' => 1500.00,
    //                 'total' => 1500.00,
    //             ],
    //             [
    //                 'item_name' => 'صيانة شهرية',
    //                 'quantity' => 2,
    //                 'unit_price' => 500.00,
    //                 'total' => 1000.00,
    //             ],
    //         ],
    //         'sub_total' => 2500.00,
    //         'tax' => 250.00, // 10% VAT
    //         'discount' => 100.00,
    //         'total' => 2650.00,
    //         'status' => 'غير مدفوعة',
    //     ];
    //     return PDF::loadView('admin-views.print-temp.print' , $invoice)->download('invoice-'.time().'-data.pdf');        
    // }
}
