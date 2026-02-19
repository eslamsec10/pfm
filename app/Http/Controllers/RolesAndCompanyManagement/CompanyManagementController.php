<?php

namespace App\Http\Controllers\RolesAndCompanyManagement;

use Carbon\Carbon;
use App\Models\Levy;
use App\Models\Role;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\CountryMaster;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CompanyManagementController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $search      = $request['search'];
        $query_param = $search ? ['search' => $request['search']] : '';

        $companies = (new Company())->setConnection('tenant')->when($request['search'], function ($q) use ($request) {
            $key = explode(' ', $request['search']);
            foreach ($key as $value) {
                $q->Where('name', 'like', "%{$value}%")
                    ->orWhere('id', $value);
            }
        })
            ->latest()->paginate()->appends($query_param);

        if (isset($search) && empty($search)) {
            $companies = (new Company())->setConnection('tenant')->with('users')
                ->orderBy('created_at', 'asc')
                ->paginate(10);
        }

        $data = [
            'companies' => $companies,
            'search'    => $search,
        ];

        return view("admin-views.companies.all_companies", $data);
    }

    public function edit($id)
    {

        $company        = (new Company())->setConnection('tenant')->findOrFail($id);
        $user           = (new User())->setConnection('tenant')->first();
        $country        = (new CountryMaster())->setConnection('tenant')->get();
        $dail_code_main = DB::connection('tenant')->table('countries')->select('id', 'dial_code')->get();
        $levies         = Levy::select('id', 'name', 'percentage')->get();
        $data = [
            'company'           => $company,
            'country'           => $country,
            'dail_code_main'    => $dail_code_main,
            'user'              => $user,
            'levies'            => $levies

        ];
        return view("admin-views.companies.edit", $data);
    }
    public function show($id)
    {

        $company = (new Company())->setConnection('tenant')->findOrFail($id);
        $country = (new CountryMaster())->setConnection('tenant')->with('country', 'region')->where('id', $company->countryid)->first();
        $data    = [
            'company'      => $company,
            'country_main' => $country,
        ];
        return view("admin-views.companies.show", $data);
    }

    public function create()
    {
        $roles          = (new Role())->setConnection('tenant')->all();
        $country        = (new CountryMaster())->setConnection('tenant')->get();
        $dail_code_main = DB::connection('tenant')->table('countries')->select('id', 'dial_code')->get();
        $data           = [
            'roles'          => $roles,
            'country'        => $country,
            'dail_code_main' => $dail_code_main,
        ];

        return view("admin-views.companies.create", $data);
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'             => 'required|string|max:255',
            // 'code'             => 'required|string|max:10|unique:users',
            'phone_dail_code'  => 'string|max:5',
            'phone'            => 'string|max:15',
            'fax_dail_code'    => 'nullable|string|max:5',
            'fax'              => 'nullable|string|max:15',
            'user_name'        => 'required|string|max:50|unique:users',
            'password'         => 'nullable|string|min:5',
            'address1'         => 'nullable|string|max:255',
            'address2'         => 'nullable|string|max:255',
            'address3'         => 'nullable|string|max:255',
            'countryid'        => 'required|integer|exists:countries,id',
            'email'            => 'max:255|unique:users',
            'state'            => 'nullable|string|max:255',
            'city'             => 'nullable|string|max:255',
            'location'         => 'nullable|string|max:255',
            'mobile_dail_code' => 'nullable|string|max:5',
            'mobile'           => 'nullable|string|max:15',
            'group_vat_no'     => 'nullable|string|max:50',
            // 'tax_type' => 'required|in:exempted,taxable,zero_rated,non_taxable',
        ]);
        DB::beginTransaction();
        try {

            $country_main                                        = (new CountryMaster())->setConnection('tenant')->find($request->countryid);
            ($request->tax_reg_date != null) ? $tax_reg_date     = Carbon::createFromFormat('d/m/Y', $request->tax_reg_date)->format('Y-m-d') : $tax_reg_date     = null;
            ($request->book_begining != null) ? $book_begining   = Carbon::createFromFormat('d/m/Y', $request->book_begining)->format('Y-m-d') : $book_begining   = null;
            ($request->financial_year != null) ? $financial_year = Carbon::createFromFormat('d/m/Y', $request->financial_year)->format('Y-m-d') : $financial_year = null;
            $company                                             = (new Company())->setConnection('tenant')->create([
                'role_name'        => 'admin',
                'role_id'          => 2,
                'domain_code'      => $request->domain_code ?? 0,
                'countryid'        => $request->countryid,
                'common'           => $request->common ?? 0,
                'status'           => $request->status ?? 'active',
                'book_begining'    => $book_begining ?? null,
                'financial_year'   => $financial_year ?? null,
                'phone'            => $request->phone ?? null,
                'phone_dail_code'  => $request->phone_dail_code ?? null,
                'fax'              => $request->fax ?? null,
                'fax_dail_code'    => $request->fax_dail_code ?? null,
                'location'         => $request->location ?? null,
                'pin'              => $request->pin ?? null,
                // 'code'             => $request->code ?? null,
                'state'            => $request->state ?? null,
                'vat_tin_no'       => $request->vat_tin_no ?? null,
                'tax_type'         => (int) $request->tax_type ?? 0,
                'tax_rate'         => $request->tax_rate ?? null,
                'group_vat_no'     => $request->group_vat_no ?? null,
                'vat_no'           => $request->vat_no ?? null,
                'name'             => $request->name ?? null,
                'user_name'        => $request->user_name ?? null,
                'password'         => Hash::make($request->password),
                'my_name'          => $request->password,

                'countryName'      => $country_main->country->name ?? null,
                'countryCode'      => $country_main->country->code ?? null,
                'countryCode'      => $country_main->region->name ?? null,
                'decimals'         => $country_main->no_of_decimals ?? null,
                'currency'         => $country_main->currency_name ?? null,
                'symbol'           => $country_main->currency_symbol ?? null,
                'currency_code'    => $country_main->country->currency_code_en ?? null,
                'denomination'     => $country_main->denomination_name ?? null,
                'address1'         => $request->address1 ?? null,
                'address2'         => $request->address2 ?? null,
                'address3'         => $request->address3 ?? null,
                'mobile_dail_code' => $request->mobile_dail_code ?? null,
                'mobile'           => $request->mobile ?? null,
                'city'             => $request->city ?? null,
                'email'            => $request->email ?? null,
                'opening_time'     => $request->opening_time ?? null,
                'closing_time'     => $request->closing_time ?? null,
                'reg_tax_status'   => $request->reg_tax_status ?? 0,
                'tax_reg_date'     => $tax_reg_date ?? null,
                'signature'        => $request->signature,
            ]);

            $logo_image      = null;
            $signature_image = null;
            $seal_image      = null;

            // if ($request->hasFile('image')) {
            //     $image = $request->file('image');
            //     $logo_image = $image->getClientOriginalName();
            //     $imageName = $logo_image;
            //     $image->move(public_path('logo/' . $request->name), $imageName);
            // }
            // if ($request->hasFile('signature')) {
            //     $signature = $request->file('signature');
            //     $signature_image = $signature->getClientOriginalName();
            //     $imageNamesignature = $signature_image;
            //     $signature->move(main_path('signature/' . $request->name), $imageNamesignature);
            // }
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
            return redirect()->route('companies')->with("success", __('property_master.updated_successfully'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
    public function update(Request $request, $id)
    {
        // dd($request->all());
        $validatedData = $request->validate([
            'name'             => 'required|string|max:255',
            // 'code'             => 'required|string|max:10|unique:users,code,' . $id,

            'phone_dail_code'  => 'string|max:5',
            'phone'            => 'string|max:15',
            'fax_dail_code'    => 'nullable|string|max:5',
            'fax'              => 'nullable|string|max:15',
            'user_name'        => 'required|string|max:50',
            'password'         => 'nullable|string|min:5',
            'address1'         => 'nullable|string|max:255',
            'address2'         => 'nullable|string|max:255',
            'address3'         => 'nullable|string|max:255',
            'countryid'        => 'required|integer',
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
        DB::beginTransaction();
        try {
            $country_main                                        = (new CountryMaster())->setConnection('tenant')->find($request->countryid);
            ($request->tax_reg_date != null) ? $tax_reg_date     = Carbon::createFromFormat('d/m/Y', $request->tax_reg_date)->format('Y-m-d') : $tax_reg_date     = null;
            ($request->book_begining != null) ? $book_begining   = Carbon::createFromFormat('d/m/Y', $request->book_begining)->format('Y-m-d') : $book_begining   = null;
            ($request->financial_year != null) ? $financial_year = Carbon::createFromFormat('d/m/Y', $request->financial_year)->format('Y-m-d') : $financial_year = null;
            ($request->levy_applicable_date != null) ? $levy_applicable_date = Carbon::createFromFormat('d/m/Y', $request->levy_applicable_date)->format('Y-m-d') : $levy_applicable_date = null;
            // dd($book_begining );
            $company = (new Company())->setConnection('tenant')->findOrFail($id);

            $company->update([
                'domain_code'                    => $request->domain_code ?? 0,
                'countryid'                      => $request->countryid,
                'common'                         => $request->common ?? 0,
                'status'                         => $request->status,
                'book_begining'                  => $book_begining,
                'financial_year'                 => $financial_year,
                'phone'                          => $request->phone ?? null,
                'phone_dail_code'                => $request->phone_dail_code ?? null,
                'fax'                            => $request->fax ?? null,
                'fax_dail_code'                  => $request->fax_dail_code ?? null,
                'location'                       => $request->location ?? null,
                'pin'                            => $request->pin ?? null,
                'state'                          => $request->state ?? null,
                'vat_tin_no'                     => $request->vat_tin_no ?? null,
                'tax_type'                       => (int) $request->tax_type ?? 0,
                'tax_rate'                       => $request->tax_rate ?? null,
                'group_vat_no'                   => $request->group_vat_no ?? null,
                'vat_no'                         => $request->vat_no ?? null,
                'name'                           => $request->name ?? null,
                'lang_name'                      => $request->lang_name ?? null,
                'my_name'                        => $request->password ? $request->password : $company->password,

                'countryName'                    => $country_main->country->name ?? null,
                'countryCode'                    => $country_main->country->code ?? null,
                'region'                         => $country_main->region->name ?? null,
                'decimals'                       => $country_main->no_of_decimals ?? null,
                'currency'                       => $country_main->currency_name ?? null,
                'symbol'                         => $country_main->currency_symbol ?? null,
                'currency_code'                  => $country_main->international_currency_code ?? null,
                'denomination'                   => $country_main->denomination_name ?? null,
                'address1'                       => $request->address1 ?? null,
                'address2'                       => $request->address2 ?? null,
                'address3'                       => $request->address3 ?? null,
                'lang_address1'                  => $request->lang_address1 ?? null,
                'lang_address2'                  => $request->lang_address2 ?? null,
                'lang_address3'                  => $request->lang_address3 ?? null,
                'mobile_dail_code'               => $request->mobile_dail_code ?? null,
                'mobile'                         => $request->mobile ?? null,
                'city'                           => $request->city ?? null,
                'email'                          => $request->email ?? null,
                'opening_time'                   => $request->opening_time ?? null,
                'closing_time'                   => $request->closing_time ?? null,
                'reg_tax_status'                 => $request->reg_tax_status ?? 0,
                'tax_reg_date'                   => $tax_reg_date ?? null,
                'signature'                      => $request->signature ?? null,

                // update saudi info
                "organization_unit_name"         => $request->organization_unit_name ?? null,
                "commercial_registration_number" => $request->commercial_registration_number ?? null,
                "invoice_type"                   => $request->invoice_type ?? null,
                "environment"                    => $request->environment ?? null,
                "short_address"                  => $request->short_address ?? null,
                "otp"                            => $request->otp ?? null,
                "zip_code"                       => $request->zip_code ?? null,
                "company_category"               => $request->company_category ?? null,


                "levy_id"                               => $request->levy_id ?? null,
                "levy_percentage"                       => $request->levy_percentage ?? null,
                "levy_date"                             => $levy_applicable_date,
            ]);


            (new User())->setConnection('tenant')->first()->update([
                'user_name' => $request->user_name ?? null,
                'password'  => Hash::make($request->password),
                'my_name'   => $request->password,
            ]);
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
            return redirect()->route('companies')->with("success", __('property_master.updated_successfully'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage())->withInput();
        }
    }

    public function delete(Request $reqeust)
    {
        $user = (new Company())->setConnection('tenant')->findOrFail($reqeust->id);
        $user->delete();
        return redirect()->back()->with("success", __('general.deleted_successfully'));
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
        $user = (new Company())->setConnection('tenant')->find($request->user_id);
        $user->update([
            'signature' => $request->signatures[0],
        ]);
        return redirect()->route('main_dashboard')->with('success', 'All signatures saved successfully!');
    }

    public function get_country($id)
    {
        $country_master = (new CountryMaster())->setConnection('tenant')->with('country', 'region')->findOrFail($id);

        if ($country_master) {
            return response()->json($country_master);
        }

        return response()->json(['error' => 'Country not found'], 404);
    }
}
