<?php

namespace App\Http\Controllers\auth;

use App\Models\Role;
use App\Models\User;
use App\Models\Company;
use AuthorizesRequests;
use App\Models\UserSettings;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\PropertyManagement;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {

        // $this->authorize('user_management', auth('web')->user());

        $search      = $request['search'];
        $query_param = $search ? ['search' => $request['search']] : '';
        // // if (!auth()->guard('admins')->check() || auth()->guard('staffs')->user()->branch_id == 0) {
        //     // $barnch = app()->make('branch');
        //     $users = (new User())->setConnection('tenant')->when($request['search'], function ($q) use ($request) {
        //         $key = explode(' ', $request['search']);
        //         foreach ($key as $value) {
        //             $q->Where('name', 'like', "%{$value}%")
        //                 ->orWhere('id', $value);
        //         }
        //     })
        //         ->latest()->paginate()->appends($query_param);

        //     if (isset($search) && empty($search)) {
        //         $users = (new User())->setConnection('tenant')->with('users')
        //             ->orderBy('created_at', 'asc')
        //             ->paginate(10);
        //     }
        // } else {
        $users = (new User())->setConnection('tenant')->when($request['search'], function ($q) use ($request) {
            $key = explode(' ', $request['search']);
            foreach ($key as $value) {
                $q->Where('name', 'like', "%{$value}%")
                    ->orWhere('id', $value);
            }
        })
            ->latest()->paginate()->appends($query_param);

        if (isset($search) && empty($search)) {
            $users = (new User())->setConnection('tenant')->with('users')
                ->orderBy('created_at', 'asc')
                ->paginate(10);
        }
        // }


        $data = [
            'users' => $users,
            'search' => $search,
        ];


        return view("admin-views.users.all_users", $data);
    }

    public function view($id)
    {
        $this->authorize('edit_user');
        $roles = (new Role())->setConnection('tenant')->all();
        $user = (new User())->setConnection('tenant')->findOrFail($id);
        return view("admin.users.show", compact("user", 'roles'));
    }

    public function edit($id)
    {
        // $this->authorize('edit_user');
        $user = (new User())->setConnection('tenant')->findOrFail($id);
        $dail_code_main = DB::connection('tenant')->table('countries')->select('id', 'dial_code')->get();
        $roles = (new Role())->setConnection('tenant')->select('id', 'name')->get();




        $data = [
            'user'                  => $user,
            'dail_code_main'        => $dail_code_main,
            'roles'                 => $roles,
        ];
        return view("admin-views.users.edit", $data);
    }

    public function create()
    {
        // $this->authorize('create_user');
        $dail_code_main = DB::connection('tenant')->table('countries')->select('id', 'dial_code')->get();
        $roles = (new Role())->setConnection('tenant')->select('id', 'name')->get();
        return view("admin-views.users.create", compact("roles", 'dail_code_main'));
    }
    public function store(Request $request)
    {
        // $this->authorize('create_user');
        $master_user = auth()->user();
        $userCount  = DB::connection('tenant')->table('companies')->value('user_count');
        $users      = DB::connection('tenant')->table('users')->count();
        if ($userCount == $users) {
            return redirect()->back()->with("error", __('general.you_have_reached_the_maximum_limit'));
        }
        $request->validate([
            'name'              => "required",
            'user_name'         => "required|unique:users,user_name",
            'email'             => "nullable|unique:users,email",
            'password'          => "required",
        ], [
            'name.required'             => __('general.name_required'),
            'user_name.required'        =>  __('general.username_required'),
            'user_name.unique'          =>  __('general.username_is_already_token'),
            'password.required'         => __('general.password_required'),
        ]);
        // $role = Role::where("id", $request->role_id)->first();
        DB::connection('tenant')->beginTransaction();
        try {
            $role = (new Role())->setConnection('tenant')->select('id', 'name')->where('id', $request->role_id)->first();
            $user = (new User())->setConnection('tenant')->create([
                'name' => $request->name,
                'user_name' =>  $request->user_name,
                'role_name' =>  $role->name  ?? 'user',
                'role_id' => $request->role_id ?? 1,
                'email' => $request->email ?? null,
                'phone' => $request->phone ?? null,
                'phone_dail_code' => $request->phone_dail_code ?? null,
                'password' => Hash::make($request->password),
                'my_name'   => $request->password,
                'company_id'    => $master_user->company_id,
                'branch_id'    => 1,
            ]);
            $buildingIds = PropertyManagement::pluck('id')->toArray();

            UserSettings::create([
                'user_id'        => $user->id,
                'building_ids'   => json_encode($buildingIds),
            ]);
 
            DB::connection('tenant')->commit();
            return redirect()->route('user_management')->with("success", __("general.added_successfully"));
        } catch (\Exception $e) {
            DB::connection('tenant')->rollBack();
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    public function update(Request $request, $id)
    {
        // $this->authorize('edit_user');
        $user = (new User())->setConnection('tenant')->findOrFail($id);
        $role = (new Role())->setConnection('tenant')->where("id", $request->role_id)->first();
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'user_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'email'             => [Rule::unique('users', 'email')->ignore($user->id), 'nullable'],
            'password'          => "required",
        ], [
            'user_name.unique' =>  __('general.username_is_already_token'),
        ]);
        $user->update([
            'name' => $request->name,
            'user_name' =>  $request->user_name,
            'role_name' => $role->name  ?? 'user',
            'role_id' => $request->role_id ?? 1,
            'email' => $request->email ?? null,
            'phone' => $request->phone ?? null,
            'phone_dail_code' => $request->phone_dail_code ?? null,
            'password' => Hash::make($request->password),
            'my_name'   => $request->password,
        ]);
        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }
        return redirect()->route('user_management')->with("success", __('general.updated_successfully'));
    }

    public function destroy(Request $request)
    {
        // $this->authorize('delete_user');

        $user = (new User())->setConnection('tenant')->findOrFail($request->id);
        $user->delete();
        return redirect()->route("user_management")->with("success", __('general.deleted_successfully'));
    }

    public function update_status(Request $request)
    {
        $this->authorize('change_users_status');

        $user = (new User())->setConnection('tenant')->findOrFail($request->id);
        $user->status = $request->status;
        $user->save();
        // Cache::forget('todays_deal_products');
        return  1;
    }
    public function bulk_user_delete(Request $request)
    {
        $this->authorize('delete_user');

        // $items = bulk_delete($request, 'App\Models\User');
        // return $items;
    }
}
