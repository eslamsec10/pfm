<?php
namespace App\Http\Controllers\Admin\RolesAndCompanyManagement;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use App\Models\Company;
use App\Mail\SendInvoice;
use Illuminate\Http\Request;
use App\Mail\SendCompanyInfo;
use App\Models\CountryMaster;
use App\Events\CompanyCreated;
use App\Models\ScheduleCompany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CompanyManagementController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        // $this->authorize('user_management');
        $ids         = $request->bulk_ids;
        $search      = $request['search'];
        $query_param = $search ? ['search' => $request['search']] : '';
        if ($request->bulk_action_btn === 'update_status' && is_array($ids) && count($ids)) {
            $data      = ['request_status' => $request->request_status];
            $companies = Company::whereIn('id', $ids)->get();
            foreach ($companies as $company) {
                if ($company->request_status == 'waiting_for_payment') {
                    $company->update(['request_status' => $request->request_status]);

                    if ($request->request_status == 'approve') {
                        event(new CompanyCreated($company));
                        $company->update([
                            'expiry_date' => now()->addMonth(),
                        ]);
                    }
                }
            }
            return back()->with('success', ui_change('updated_successfully'));
        }
        $companies = Company::when($request['search'], function ($q) use ($request) {
            $key = explode(' ', $request['search']);
            foreach ($key as $value) {
                $q->Where('name', 'like', "%{$value}%")
                    ->orWhere('id', $value);
            }
        })->select('id', 'company_id', 'created_at', 'name', 'countryName', 'code', 'request_status')
            ->latest()->paginate()->appends($query_param);

        if (isset($search) && empty($search)) {
            $companies = Company::orderBy('created_at', 'asc')->select('id', 'company_id', 'created_at', 'name', 'countryName', 'code', 'request_status')
                ->paginate(20);
        }
        $users = User::select('company_id', 'user_name')->get();

        if ($request->bulk_action_btn === 'filter') {
            // $data         = ['status' => 1];
            $report_query = Company::query();

            if ($request->request_status && $request->request_status != -1) { 
                $report_query->where('request_status', $request->request_status);

            }
            $companies = $report_query->orderBy('created_at', 'desc')->paginate(20);
        }
        $data = [
            'companies' => $companies,
            'search'    => $search,
            'users'     => $users,
        ];
        return view("super_admin.companies.all_companies", $data);
    }

    public function edit($id)
    {

        $company        = Company::findOrFail($id);
        $user           = User::where('company_id', $id)->first();
        $country        = CountryMaster::get();
        $dail_code_main = DB::table('dial_code_table')->select('id', 'dial_code')->get();

        $data = [
            'company'        => $company,
            'country'        => $country,
            'dail_code_main' => $dail_code_main,
            'user'           => $user,
        ];
        return view("super_admin.companies.edit", $data);
    }
    public function show($id)
    {
        $company = (new Company())->setConnection('mysql')->findOrFail($id);
        $user    = (new User())->setConnection('mysql')->where('company_id', $company->id)->first();
        $country = CountryMaster::with('country', 'region')->where('id', $company->countryid)->first();
        $data    = [
            'user'         => $user,
            'company'      => $company,
            'country_main' => $country,
        ];
        return view("super_admin.companies.show", $data);
    }

    public function create()
    {
        $roles          = (new Role())->setConnection('mysql')->get();
        $country        = (new CountryMaster())->setConnection('mysql')->get();
        $dail_code_main = DB::connection('mysql')->table('dial_code_table')->select('id', 'dial_code')->get();
        $data           = [
            'roles'          => $roles,
            'country'        => $country,
            'dail_code_main' => $dail_code_main,
        ];

        return view("super_admin.companies.create", $data);
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'            => 'required|string|max:255',
            'company_id'      => 'required|unique:companies,company_id',
            'phone_dail_code' => 'nullable|string|max:5',
            'phone'           => 'nullable|string|max:15',

            'user_name'       => 'required|string|max:50',
            'password'        => 'nullable|string|min:5',
            'countryid'       => 'required|integer',
        ]);
        // dd(DB::getDatabaseName());
        DB::beginTransaction();
        try {
            $country_main                                        = CountryMaster::find($request->countryid);
            ($request->tax_reg_date != null) ? $tax_reg_date     = Carbon::createFromFormat('d/m/Y', $request->tax_reg_date)->format('Y-m-d') : $tax_reg_date     = null;
            ($request->book_begining != null) ? $book_begining   = Carbon::createFromFormat('d/m/Y', $request->book_begining)->format('Y-m-d') : $book_begining   = null;
            ($request->financial_year != null) ? $financial_year = Carbon::createFromFormat('d/m/Y', $request->financial_year)->format('Y-m-d') : $financial_year = null;
            $company                                             = Company::create([
                'name'                          => $request->name ?? 0,
                'company_id'                    => $request->company_id ?? 0,
                'domain'                        => $request->company_id . '.' . $request->getHost(),
                'user_count'                    => $request->user_count ?? 10,
                'branches_count'                => $request->branches_count ?? 2,
                'building_count'                => $request->buildings_count ?? 3,
                'units_count'                   => $request->units_count ?? 10,
                'setup_cost'                    => $request->setup_cost ?? 0,
                'monthly_subscription_user'     => $request->monthly_subscription_user ?? 0,
                'monthly_subscription_building' => $request->monthly_subscription_building ?? 0,
                'monthly_subscription_units'    => $request->monthly_subscription_units ?? 0,
                'monthly_subscription_branches' => $request->monthly_subscription_branches ?? 0,
                'creation_date'                 => $request->creation_date ?? 0,
                'company_applicable_date'       => $request->company_applicable_date ?? 0,
                'domain_code'                   => $request->domain_code ?? 0,
                'countryid'                     => $request->countryid,
                'common'                        => $request->common ?? 0,
                'status'                        => $request->status ?? 'active',
                'book_begining'                 => $book_begining ?? null,
                'financial_year'                => $financial_year ?? null,
                'phone'                         => $request->phone ?? null,
                'phone_dail_code'               => $request->phone_dail_code ?? null,
                'fax'                           => $request->fax ?? null,
                'fax_dail_code'                 => $request->fax_dail_code ?? null,
                'location'                      => $request->location ?? null,
                'pin'                           => $request->pin ?? null,
                'code'                          => $request->code ?? null,
                'state'                         => $request->state ?? null,
                'vat_tin_no'                    => $request->vat_tin_no ?? null,
                'tax_type'                      => (int) $request->tax_type ?? 0,
                'request_status'                => 'approve',
                'tax_rate'                      => $request->tax_rate ?? null,
                'group_vat_no'                  => $request->group_vat_no ?? null,
                'vat_no'                        => $request->vat_no ?? null,
                'countryName'                   => $country_main->country->name ?? null,
                'countryCode'                   => $request->countryCode ?? null,
                'region'                        => $request->region ?? null,
                'decimals'                      => $request->decimals ?? null,
                'currency'                      => $request->currency ?? null,
                'symbol'                        => $request->symbol ?? null,
                'currency_code'                 => $request->international_currency_code ?? null,
                'denomination'                  => $request->denomination ?? null,
                'address1'                      => $request->address1 ?? null,
                'address2'                      => $request->address2 ?? null,
                'address3'                      => $request->address3 ?? null,
                'mobile_dail_code'              => $request->mobile_dail_code ?? null,
                'mobile'                        => $request->mobile ?? null,
                'city'                          => $request->city ?? null,
                'email'                         => $request->email ?? null,
                'opening_time'                  => $request->opening_time ?? null,
                'closing_time'                  => $request->closing_time ?? null,
                'reg_tax_status'                => $request->reg_tax_status ?? 0,
                'tax_reg_date'                  => $tax_reg_date ?? null,
                'signature'                     => $request->signature,
            ]);
            User::create([
                'name'            => $request->name ?? null,
                'user_name'       => $request->user_name ?? null,
                'password'        => Hash::make($request->password),
                'my_name'         => $request->password,
                'role_name'       => 'admin',
                'role_id'         => 2,
                'company_id'      => $company->id,
                'branch_id'       => 1,
                'phone'           => $request->phone ?? null,
                'phone_dail_code' => $request->phone_dail_code ?? null,
                'email'           => $request->email ?? null,
            ]);
            $schedule_company = ScheduleCompany::create([
                'company_id'                    => $company->id,
                'user_count'                    => $request->user_count ?? 10,
                'branches_count'                => $request->branches_count ?? 2,
                'building_count'                => $request->buildings_count ?? 3,
                'units_count'                   => $request->units_count ?? 10,
                'setup_cost'                    => $request->setup_cost ?? 0,
                'monthly_subscription_user'     => $request->monthly_subscription_user ?? 0,
                'monthly_subscription_building' => $request->monthly_subscription_building ?? 0,
                'monthly_subscription_units'    => $request->monthly_subscription_units ?? 0,
                'monthly_subscription_branches' => $request->monthly_subscription_branches ?? 0,
                'creation_date'                 => $request->creation_date ?? 0,
                'company_applicable_date'       => $request->company_applicable_date ?? 0,
            ]);
            $company->update([
                'domain_code' => $schedule_company->id,
            ]);
            // Mail::to($request->email)->send(new SendCompanyInfo([
            //     'user name'  => $request->user_name,
            //     'password'   => $request->password,
            //     'domain'     => $request->getHost(),
            //     'company_id' => $request->company_id,
            // ]));

            // Mail::to($request->email)->send(new SendInvoice([
            //     'users_count'     => $request->user_count,
            //     'users_cost'      => $request->monthly_subscription_user,
            //     'buildings_count' => $request->buildings_count,
            //     'buildings_cost'  => $request->monthly_subscription_building,
            //     'units_count'     => $request->units_count,
            //     'units_cost'      => $request->monthly_subscription_units,
            //     'branches_count'  => $request->branches_count,
            //     'branches_cost'   => $request->monthly_subscription_branches,
            //     'setup_cost'      => $request->setup_cost,
            //     'country_master'  => $country_main,
            //     'company'         => $company,
            // ]));

            $logo_image      = null;
            $signature_image = null;
            $seal_image      = null;

            if ($request->hasFile('image')) {
                $logoPath = main_path('logo/' . $request->name);
                if (! empty($company->logo_image) && file_exists($logoPath . '/' . $company->logo_image)) {
                    unlink($logoPath . '/' . $company->logo_image);
                }
                $logo          = $request->file('image');
                $logo_image    = $logo->getClientOriginalName();
                $imageNameLogo = $logo_image;
                if (! file_exists($logoPath)) {
                    mkdir($logoPath, 0777, true);
                }
                $logo->move($logoPath, $imageNameLogo);
                $company->update(['logo_image' => $imageNameLogo]);
            }

            if ($request->hasFile('signature')) {
                $signaturePath = main_path('signature/' . $request->name);
                if (! empty($company->signature) && file_exists($signaturePath . '/' . $company->signature)) {
                    unlink($signaturePath . '/' . $company->signature);
                }
                $signature          = $request->file('signature');
                $signature_image    = $signature->getClientOriginalName();
                $imageNameSignature = $signature_image;
                if (! file_exists($signaturePath)) {
                    mkdir($signaturePath, 0777, true);
                }
                $signature->move($signaturePath, $imageNameSignature);
                $company->update(['signature' => $imageNameSignature]);
            }
            if ($request->hasFile('seal')) {
                $sealPath = main_path('seal/' . $request->name);
                if (! empty($company->seal) && file_exists($sealPath . '/' . $company->seal)) {
                    unlink($sealPath . '/' . $company->seal);
                }
                $seal          = $request->file('seal');
                $seal_image    = $seal->getClientOriginalName();
                $imageNameSeal = $seal_image;
                if (! file_exists($sealPath)) {
                    mkdir($sealPath, 0777, true);
                }
                $seal->move($sealPath, $imageNameSeal);
                $company->update(['seal' => $imageNameSeal]);
            }
            DB::commit();
            event(new CompanyCreated($company));
            return redirect()->route('admin.companies')->with("success", ui_change('updated_successfully'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage())->withInputs();
        }
    }
    public function update(Request $request, $id)
    {
        // dd($request->all());
        $validatedData = $request->validate([
            'name'             => 'required|string|max:255',
            'phone_dail_code'  => 'nullable|string|max:5',
            'phone'            => 'nullable|string|max:15',
            'fax_dail_code'    => 'nullable|string|max:5',
            'fax'              => 'nullable|string|max:15',
            'user_name'        => 'required|string|max:50',
            'password'         => 'nullable|string|min:5',
            'address1'         => 'nullable|string|max:255',
            'address2'         => 'nullable|string|max:255',
            'address3'         => 'nullable|string|max:255',
            'countryid'        => 'required|integer|exists:countries,id',
            'email'            => 'nullable|email|max:255',
            'state'            => 'nullable|string|max:255',
            'city'             => 'nullable|string|max:255',
            'location'         => 'nullable|string|max:255',
            'mobile_dail_code' => 'nullable|string|max:5',
            'mobile'           => 'nullable|string|max:15',
            'vat_no'           => 'nullable|string|max:50',
            'group_vat_no'     => 'nullable|string|max:50',
            'tax_reg_date'     => 'nullable|date',
            // 'tax_type' => 'in:exempted,taxable,zero_rated,non_taxable',
        ]);
        // dd($request->all());
        DB::beginTransaction();
        try {
            $country_main                                        = CountryMaster::find($request->countryid);
            ($request->tax_reg_date != null) ? $tax_reg_date     = Carbon::createFromFormat('d/m/Y', $request->tax_reg_date)->format('Y-m-d') : $tax_reg_date     = null;
            ($request->book_begining != null) ? $book_begining   = Carbon::createFromFormat('d/m/Y', $request->book_begining)->format('Y-m-d') : $book_begining   = null;
            ($request->financial_year != null) ? $financial_year = Carbon::createFromFormat('d/m/Y', $request->financial_year)->format('Y-m-d') : $financial_year = null;
            // dd($book_begining );
            $company = (new Company())->setConnection('mysql')->findOrFail($id);
            $oldData = $company->only([
                'id',
                'user_count',
                'branches_count',
                'building_count',
                'units_count',
                'setup_cost',
                'monthly_subscription_user',
                'monthly_subscription_building',
                'monthly_subscription_units',
                'monthly_subscription_branches',
                'creation_date',
                'company_applicable_date',
            ]);
            $newData = [
                'company_id'                    => $company->id ?? 10,
                'user_count'                    => $request->user_count ?? 10,
                'branches_count'                => $request->branches_count ?? 2,
                'building_count'                => $request->building_count ?? 3,
                'units_count'                   => $request->units_count ?? 10,
                'setup_cost'                    => $request->setup_cost ?? 0,
                'monthly_subscription_user'     => $request->monthly_subscription_user ?? 0,
                'monthly_subscription_building' => $request->monthly_subscription_building ?? 0,
                'monthly_subscription_units'    => $request->monthly_subscription_units ?? 0,
                'monthly_subscription_branches' => $request->monthly_subscription_branches ?? 0,
                'creation_date'                 => $request->creation_date ?? 0,
                'company_applicable_date'       => $request->company_applicable_date ?? 0,
            ];

            $company->update([
                'name'                          => $request->name ?? 0,
                'company_id'                    => $request->company_id ?? 0,
                'domain'                        => $request->company_id . '.' . $request->getHost(),
                'user_count'                    => $request->user_count ?? 10,
                'branches_count'                => $request->branches_count ?? 2,
                'building_count'                => $request->building_count ?? 3,
                'units_count'                   => $request->units_count ?? 10,
                'setup_cost'                    => $request->setup_cost ?? 0,
                'monthly_subscription_user'     => $request->monthly_subscription_user ?? 0,
                'monthly_subscription_building' => $request->monthly_subscription_building ?? 0,
                'monthly_subscription_units'    => $request->monthly_subscription_units ?? 0,
                'monthly_subscription_branches' => $request->monthly_subscription_branches ?? 0,
                'creation_date'                 => $request->creation_date ?? 0,
                'company_applicable_date'       => $request->company_applicable_date ?? 0,
                'domain_code'                   => $request->domain_code ?? 0,
                'countryid'                     => $request->countryid,
                'common'                        => $request->common ?? 0,
                'status'                        => $request->status ?? 'active',
                'book_begining'                 => $book_begining ?? null,
                'financial_year'                => $financial_year ?? null,
                'phone'                         => $request->phone ?? null,
                'phone_dail_code'               => $request->phone_dail_code ?? null,
                'fax'                           => $request->fax ?? null,
                'fax_dail_code'                 => $request->fax_dail_code ?? null,
                'location'                      => $request->location ?? null,
                'pin'                           => $request->pin ?? null,
                'code'                          => $request->code ?? null,
                'state'                         => $request->state ?? null,
                'vat_tin_no'                    => $request->vat_tin_no ?? null,
                'tax_type'                      => (int) $request->tax_type ?? 0,
                'tax_rate'                      => $request->tax_rate ?? null,
                'group_vat_no'                  => $request->group_vat_no ?? null,
                'vat_no'                        => $request->vat_no ?? null,
                'countryName'                   => $country_main->country->name ?? null,
                'countryCode'                   => $request->countryCode ?? null,
                'region'                        => $request->region ?? null,
                'decimals'                      => $request->decimals ?? null,
                'currency'                      => $request->currency ?? null,
                'symbol'                        => $request->symbol ?? null,
                'currency_code'                 => $request->international_currency_code ?? null,
                'denomination'                  => $request->denomination ?? null,
                'address1'                      => $request->address1 ?? null,
                'address2'                      => $request->address2 ?? null,
                'address3'                      => $request->address3 ?? null,
                'mobile_dail_code'              => $request->mobile_dail_code ?? null,
                'mobile'                        => $request->mobile ?? null,
                'city'                          => $request->city ?? null,
                'email'                         => $request->email ?? null,
                'opening_time'                  => $request->opening_time ?? null,
                'closing_time'                  => $request->closing_time ?? null,
                'reg_tax_status'                => $request->reg_tax_status ?? 0,
                'tax_reg_date'                  => $tax_reg_date ?? null,
                'signature'                     => $request->signature,
            ]);
            (new User())->setConnection('mysql')->where('company_id', $company->id)->first()->update([
                'user_name'       => $request->user_name ?? null,
                'password'        => Hash::make($request->password),
                'my_name'         => $request->password,
                'phone'           => $request->phone ?? null,
                'phone_dail_code' => $request->phone_dail_code ?? null,
                'email'           => $request->email ?? null,

            ]);
            // (new User())->setConnection('tenant')->where('company_id', $company->id)->first()->update([
            //     'user_name'       => $request->user_name ?? null,
            //     'password'        => Hash::make($request->password),
            //     'my_name'         => $request->password,
            //     'phone'           => $request->phone ?? null,
            //     'phone_dail_code' => $request->phone_dail_code ?? null,
            //     'email'           => $request->email ?? null,

            // ]);

            if ($oldData != $newData) {
                $schedule_company = (new ScheduleCompany())->setConnection('mysql')->create($newData);
                $company->update([
                    'domain_code' => $schedule_company->id,
                ]);
            }
            if (! empty($request->password)) {
                $company->update([
                    'my_name' => $request->password,

                ]);
            }
            $logo_image      = null;
            $signature_image = null;
            $seal_image      = null;

            if ($request->hasFile('image')) {
                $logoPath = main_path('logo/' . $request->name);
                if (! empty($company->logo_image) && file_exists($logoPath . '/' . $company->logo_image)) {
                    unlink($logoPath . '/' . $company->logo_image);
                }
                $logo          = $request->file('image');
                $logo_image    = $logo->getClientOriginalName();
                $imageNameLogo = $logo_image;
                if (! file_exists($logoPath)) {
                    mkdir($logoPath, 0777, true);
                }
                $logo->move($logoPath, $imageNameLogo);
                $company->update(['logo_image' => $imageNameLogo]);
            }

            if ($request->hasFile('signature')) {
                $signaturePath = main_path('signature/' . $request->name);
                if (! empty($company->signature) && file_exists($signaturePath . '/' . $company->signature)) {
                    unlink($signaturePath . '/' . $company->signature);
                }
                $signature          = $request->file('signature');
                $signature_image    = $signature->getClientOriginalName();
                $imageNameSignature = $signature_image;
                if (! file_exists($signaturePath)) {
                    mkdir($signaturePath, 0777, true);
                }
                $signature->move($signaturePath, $imageNameSignature);
                $company->update(['signature' => $imageNameSignature]);

            }
            if ($request->hasFile('seal')) {
                $sealPath = public_path('seal/' . $request->name);
                if (! empty($company->seal) && file_exists($sealPath . '/' . $company->seal)) {
                    unlink($sealPath . '/' . $company->seal);
                }
                $seal          = $request->file('seal');
                $seal_image    = $seal->getClientOriginalName();
                $imageNameSeal = $seal_image;
                if (! file_exists($sealPath)) {
                    mkdir($sealPath, 0777, true);
                }

                $seal->move($sealPath, $imageNameSeal);
                $company->update(['seal' => $imageNameSeal]);
            }

            DB::commit();
            return redirect()->route('admin.companies')->with("success", ui_change('updated_successfully'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage())->withInputs();
        }
    }
    public function schedules($id)
    {
        $schedules = ScheduleCompany::where('company_id', $id)->paginate();
        $data      = [
            'schedules' => $schedules,
        ];
        return view('super_admin.companies.schedule_list', $data);
    }
    public function delete(Request $reqeust)
    {
        $company = (new Company())->setConnection('mysql')->findOrFail($reqeust->id);
        $user    = (new User())->setConnection('mysql')->where('company_id', $company->id)->first();
        if ($user) {
            $user->delete();
        }
        $db = "finexerp_{$company->id}";
        // DB::connection('tenant')->statement("DROP DATABASE `{$db}`");

        $company->delete();
        return redirect()->back()->with("success", ui_change('deleted_successfully'));
    }

    public function signature()
    {
        $this->authorize('add_sginature');
        $user = Auth::user();
        return view('admin.users.add_signature', compact(['user']));
    }
    public function storeSignature(Request $request)
    {
        $this->authorize('add_sginature');
        $request->validate([
            'signatures.*' => 'string',
            'user_id'      => 'required',
        ]);
        $user = User::find($request->user_id);
        $user->update([
            'signature' => $request->signatures[0],
        ]);
        return redirect()->route('main_dashboard')->with('success', 'All signatures saved successfully!');
    }

    public function get_country($id)
    {
        $country_master = CountryMaster::with('country', 'region')->findOrFail($id);

        if ($country_master) {
            return response()->json($country_master);
        }

        return response()->json(['error' => 'Country not found'], 404);
    }

}
