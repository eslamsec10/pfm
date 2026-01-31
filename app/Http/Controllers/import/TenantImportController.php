<?php

namespace App\Http\Controllers\import;

use App\Http\Controllers\Controller;
use App\Imports\TenantTemplate;
use App\Models\BusinessActivity;
use App\Models\CountryMaster;
use App\Models\LiveWith;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class TenantImportController extends Controller
{
    public function import_page(Request $request)
    {
        $instructions = '
        <p>1. ' . ui_change('Download_the_file_format_and_fill_the_correct_Data.') . '</p>
        <p>2. ' . ui_change('Tenant_Type_is_Mandatory_(_Individual_/_Company)') . '</p>
        <p>3. ' . ui_change('Country_and_Nationality_is_mandatory') . '</p>
        <p>4. ' . ui_change('Gender_field_is_Mandatory_(_Male/Female)') . '</p> 

    ';

        $data = [
            'instructions' => $instructions,
            'file'         => 'tenant',
            'file_name'         => 'cleaned_header',

        ];

        return view('import_excel.upload_page', $data);
    }
    public function preview(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'file' => 'required',
        ]);
        // dd($request->file('file'));
        $path = $request->file('file')->getRealPath();

        $data = Excel::toCollection(null, $request->file('file'))->first();

        return view('import_excel.tenant_preview', compact('data'));
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls',
        ]);

        Excel::import(new TenantTemplate, $request->file('file'));

        return back()->with('success', ui_change('imported_successfully'));
    }

    public function confirm_tenant(Request $request)
    {
        $rows = $request->input('rows', []);

        foreach ($rows as $row) {
            $normalizedRow = $this->normalizeRow($row);
            $validator = Validator::make($normalizedRow, [
                'tenant_type' => 'required|in:individual,company',
                'gender'      => 'nullable|in:male,female',
            ], [
                'tenant_type.required' => 'Tenant type is required',
                'tenant_type.in'       => 'Tenant type must be either individual or company',
                'gender.in'            => 'Gender must be male or female',
            ]);

            if ($validator->fails()) {
                return redirect()->route('import_tenant')
                    ->withErrors($validator)
                    ->withInput();
            }

            Tenant::updateOrCreate(
                [
                    'name'         => $normalizedRow['tenant_name'] ?? null,
                    'company_name' => $normalizedRow['company_name'] ?? null,
                ],
                [
                    'type'                 => $normalizedRow['tenant_type'] ?? 'individual',
                    'gender'               => $normalizedRow['gender'] ?? null,
                    'country_id'           => $this->getCountryId($normalizedRow['country_name'] ?? null),
                    'live_with_id'         => $this->getLiveWithId($normalizedRow['live_with'] ?? null),
                    'business_activity_id' => $this->getBusinessActivityId($normalizedRow['business_activity'] ?? null),
                    'id_number'            => $normalizedRow['id_number'] ?? null,
                    'registration_no'      => $normalizedRow['registration_no'] ?? null,
                    'nick_name'            => $normalizedRow['nick_name'] ?? null,
                    'group_company_name'   => $normalizedRow['group_company_name'] ?? null,
                    'contact_person'       => $normalizedRow['contact_person'] ?? null,
                    'designation'          => $normalizedRow['designation'] ?? null,
                    'contact_no'           => $normalizedRow['contact_no'] ?? null,
                    'whatsapp_no'          => $normalizedRow['whatsapp_no'] ?? null,
                    'fax_no'               => $normalizedRow['fax_no'] ?? null,
                    'telephone_no'         => $normalizedRow['telephone_no'] ?? null,
                    'other_contact_no'     => $normalizedRow['other_contact_no'] ?? null,
                    'address1'             => $normalizedRow['address_1'] ?? null,
                    'address2'             => $normalizedRow['address_2'] ?? null,
                    'address3'             => $normalizedRow['address_3'] ?? null,
                    'state'                => $normalizedRow['state'] ?? null,
                    'city'                 => $normalizedRow['city'] ?? null,
                    'nationality_id'       => $this->getNationalityId($normalizedRow['nationality'] ?? null),
                    'passport_no'          => $normalizedRow['passport_no'] ?? null,
                    'email1'               => $normalizedRow['email_1'] ?? null,
                    'email2'               => $normalizedRow['email_2'] ?? null,
                    'document'             => null,
                ]
            );
        }
        return redirect()->route('tenant.index')->with('success', ui_change('imported_successfully'));
    }

    private function normalizeRow(array $row): array
    {
        $normalized = [];

        foreach ($row as $key => $value) {
            $normalizedKey = strtolower(trim($key));
            $normalizedKey = str_replace([' ', '-'], '_', $normalizedKey);
            $normalizedKey = preg_replace('/[^a-z0-9_]/', '', $normalizedKey);

            $normalized[$normalizedKey] = $value;
        }

        return $normalized;
    }

    // public function confirm_tenant(Request $request)
    // {

    //     $rows = $request->input('rows', []);

    //     foreach ($rows as $row) {
    //         $normalizedRow = $this->normalizeRow($row);

    //         Tenant::updateOrCreate(
    //             [
    //                 'name'         => $normalizedRow['tenant_name'] ?? null,
    //                 'company_name' => $normalizedRow['company_name'] ?? null,
    //             ],
    //             [
    //                 'type'                 => $normalizedRow['tenant_type'] ?? 'individual',
    //                 'gender'               => $normalizedRow['gender'] ?? null,
    //                 'country_id'           => $this->getCountryId($normalizedRow['country_name'] ?? null),
    //                 'live_with_id'         => $this->getLiveWithId($normalizedRow['live_with'] ?? null),
    //                 'business_activity_id' => $this->getBusinessActivityId($normalizedRow['business_activity'] ?? null),
    //                 'id_number'            => $normalizedRow['id_number'] ?? null,
    //                 'registration_no'      => $normalizedRow['registration_no'] ?? null,
    //                 'nick_name'            => $normalizedRow['nick_name'] ?? null,
    //                 'group_company_name'   => $normalizedRow['group_company_name'] ?? null,
    //                 'contact_person'       => $normalizedRow['contact_person'] ?? null,
    //                 'designation'          => $normalizedRow['designation'] ?? null,
    //                 'contact_no'           => $normalizedRow['contact_no'] ?? null,
    //                 'whatsapp_no'          => $normalizedRow['whatsapp_no'] ?? null,
    //                 'fax_no'               => $normalizedRow['fax_no'] ?? null,
    //                 'telephone_no'         => $normalizedRow['telephone_no'] ?? null,
    //                 'other_contact_no'     => $normalizedRow['other_contact_no'] ?? null,
    //                 'address1'             => $normalizedRow['address_1'] ?? null,
    //                 'address2'             => $normalizedRow['address_2'] ?? null,
    //                 'address3'             => $normalizedRow['address_3'] ?? null,
    //                 'state'                => $normalizedRow['state'] ?? null,
    //                 'city'                 => $normalizedRow['city'] ?? null,
    //                 'nationality_id'       => $this->getNationalityId($normalizedRow['nationality'] ?? null),
    //                 'passport_no'          => $normalizedRow['passport_no'] ?? null,
    //                 'email1'               => $normalizedRow['email_1'] ?? null,
    //                 'email2'               => $normalizedRow['email_2'] ?? null,
    //                 'document'             => null,
    //             ]
    //         );
    //     }

    //     return redirect()->route('tenant.index')->with('success', ui_change('imported_successfully'));

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
    private function getCountryId($name)
    {
        if (! $name) {
            return null;
        }
        $country = CountryMaster::whereHas('country', function ($c) use ($name) {
            $c->where('name', 'like', "%{$name}%");
        })->first();

        return $country?->id;
    }

    private function getLiveWithId($name)
    {
        return $name ? LiveWith::firstOrCreate(['name' => $name])->id : null;
    }

    private function getBusinessActivityId($name)
    {
        return $name ? BusinessActivity::firstOrCreate(['name' => $name])->id : null;
    }

    private function getNationalityId($name)
    {
        if (! $name) {
            return null;
        }

        $country = CountryMaster::whereHas('country', function ($c) use ($name) {
            $c->where('name', 'like', "%{$name}%");
        })->first();

        return $country?->id;
    }
}
