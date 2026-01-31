<?php
namespace App\Imports;

use App\Models\Agreement;
use App\Models\AgreementDetails;
use App\Models\AgreementUnits;
use App\Models\Block;
use App\Models\BlockManagement;
use App\Models\CountryMaster;
use App\Models\Floor;
use App\Models\FloorManagement;
use App\Models\PropertyManagement;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\UnitManagement;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ContractTemplate implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if ($row['property_name']) {
                $dateValue     = $row['date'];
                $agreementDate = null;

                if (is_numeric($dateValue)) {
                    $agreementDate = Carbon::instance(
                        Date::excelToDateTimeObject((int) $dateValue)
                    );
                } elseif (! empty($dateValue) && $dateValue !== '-') {
                    $agreementDate = Carbon::parse($dateValue);
                } else {
                    $agreementDate = null;
                }

                $lease_start_date     = $row['lease_start_date'];
                $new_lease_start_date = null;

                if (is_numeric($lease_start_date)) {
                    $new_lease_start_date = Carbon::instance(
                        Date::excelToDateTimeObject((int) $lease_start_date)
                    );
                } elseif (! empty($lease_start_date) && $lease_start_date !== '-') {
                    $new_lease_start_date = Carbon::parse($lease_start_date);
                } else {
                    $new_lease_start_date = null;
                }

                $lease_end_date     = $row['lease_end_date'];
                $new_lease_end_date = null;

                if (is_numeric($lease_end_date)) {
                    $new_lease_end_date = Carbon::instance(
                        Date::excelToDateTimeObject((int) $lease_end_date)
                    );
                } elseif (! empty($lease_end_date) && $lease_end_date !== '-') {
                    $new_lease_end_date = Carbon::parse($lease_end_date);
                } else {
                    $new_lease_end_date = null;
                }

                $rent_start_date     = $row['rent_start_date'];
                $new_rent_start_date = null;

                if (is_numeric($rent_start_date)) {
                    $new_rent_start_date = Carbon::instance(
                        Date::excelToDateTimeObject((int) $rent_start_date)
                    );
                } elseif (! empty($rent_start_date) && $rent_start_date !== '-') {
                    $new_rent_start_date = Carbon::parse($rent_start_date);
                } else {
                    $new_rent_start_date = null;
                }

                $rent_end_date     = $row['rent_end_date'];
                $new_rent_end_date = null;

                if (is_numeric($rent_end_date)) {
                    $new_rent_end_date = Carbon::instance(
                        Date::excelToDateTimeObject((int) $rent_end_date)
                    );
                } elseif (! empty($rent_end_date) && $rent_end_date !== '-') {
                    $new_rent_end_date = Carbon::parse($rent_end_date);
                } else {
                    $new_rent_end_date = null;
                }

                // tenant
                $country = CountryMaster::first();
                $tenant  = Tenant::firstOrCreate(
                    ['name' => trim($row['tenant_name'])],
                    ['type' => 'individual', 'country_id' => $country->id]
                );

                $property = PropertyManagement::firstOrCreate(
                    ['name' => $row['property_name']],
                    [
                        'code' => $row['property_name'] ?? null,
                    ]
                );

                // ---------------------
                // Block
                // ---------------------
                $blockName = Block::firstOrCreate(['name' => $row['block'], 'code' => $row['block']]);
                if ($property) {
                    $block = BlockManagement::firstOrCreate(
                        [
                            'block_id'               => $blockName->id,
                            'property_management_id' => $property->id,
                        ],
                        [

                        ]
                    );
                }

                // ---------------------
                // Floor
                // ---------------------
                $floorName = Floor::firstOrCreate(['name' => $row['floor'], 'code' => $row['floor']]);
                if ($block) {
                    $floor = FloorManagement::firstOrCreate(
                        [
                            'floor_id'               => $floorName->id,
                            'block_management_id'    => $block->id,
                            'property_management_id' => $property->id,
                        ],
                        [

                        ]
                    );
                }

                // unit
                $unit_management = null;

                $unit = Unit::where('unit_no', $row['unit'])->first();

                if ($unit) {
                    $unit_management = UnitManagement::where([
                        'unit_id'                => $unit->id,
                        'property_management_id' => $property->id,
                        'block_management_id'    => $block->id,
                        'floor_management_id'    => $floor->id,
                    ])->first();
                    if (! $unit_management) {
                        $unit_management = UnitManagement::create([
                            'unit_id'                => $unit->id,
                            'property_management_id' => $property->id,
                            'block_management_id'    => $block->id,
                            'floor_management_id'    => $floor->id,
                        ]);
                    }
                } else {
                    $unit = Unit::create([
                        'unit_no' => $row['unit'],
                        'name'    => $row['unit'],
                        'code'    => $row['unit'],
                    ]);
                    $unit_management = UnitManagement::create([
                        'unit_id'                => $unit->id,
                        'property_management_id' => $property->id,
                        'block_management_id'    => $block->id,
                        'floor_management_id'    => $floor->id,
                    ]);
                }
                // lease agreement
                $agreement = Agreement::updateOrCreate(
                    ['agreement_no' => $row['lease_agreement_no']],
                    [
                        'agreement_no'               => $row['lease_agreement_no'] ?? agreementNo(),
                        'agreement_date'             => $agreementDate ?? Carbon::now(),
                        'tenant_id'                  => $tenant->id,
                        'name'                       => $tenant->name,
                        'gender'                     => $tenant->gender,
                        'id_number'                  => $tenant->id_number,
                        'registration_no'            => $tenant->registration_no,
                        'nick_name'                  => $tenant->nick_name,
                        'group_company_name'         => $tenant->group_company_name,
                        'contact_person'             => $tenant->contact_person,
                        'designation'                => $tenant->designation,
                        'contact_no'                 => $tenant->contact_no,
                        'whatsapp_no'                => $tenant->whatsapp_no,
                        'company_name'               => $tenant->company_name,
                        'fax_no'                     => $tenant->fax_no,
                        'telephone_no'               => $tenant->telephone_no,
                        'other_contact_no'           => $tenant->other_contact_no,
                        'address1'                   => $tenant->address1,
                        'address2'                   => $tenant->address2,
                        'address3'                   => $tenant->address3,
                        'state'                      => $tenant->state,
                        'city'                       => $tenant->city,
                        'country_id'                 => $tenant->country_id,
                        'nationality_id'             => $tenant->nationality_id,
                        'passport_no'                => $tenant->passport_no,
                        'email1'                     => $tenant->email1,
                        'email2'                     => $tenant->email2,
                        'live_with_id'               => null,
                        'business_activity_id'       => null,
                        'status'                     => 'pending',
                        'booking_status'             => 'agreement',
                        'total_no_of_required_units' => 1,
                    ]
                );

                if ($agreement) {
                    $agreement_details = (new AgreementDetails())->setConnection('tenant')->create([
                        'agreement_id'                => $agreement->id,
                        'employee_id'                 => null,
                        'agent_id'                    => null,
                        'agreement_status_id'         => null,
                        'agreement_request_status_id' => null,
                        'decision_maker'              => null,
                        'decision_maker_designation'  => null,
                        'current_office_location'     => null,
                        'reason_of_relocation'        => null,
                        'budget_for_relocation_start' => null,
                        'budget_for_relocation_end'   => null,
                        'no_of_emp_staff_strength'    => null,
                        'time_frame_for_relocation'   => null,
                        'relocation_date'             => null,
                        'period_from'                 => $new_lease_start_date ?? null,
                        'period_to'                   => $new_lease_end_date ?? null,
                    ]);
                    $rentModes = [
                        0 => 'select_rent_mode',
                        1 => 'Daily',
                        2 => 'Monthly',
                        3 => 'bi_monthly',
                        4 => 'quarterly',
                        5 => 'half_yearly',
                        6 => ['yearly', 'annually'],
                    ];

                    $rentModes = [
                        0 => ['select', 'select_rent_mode'],
                        1 => ['daily', 'per day'],
                        2 => ['monthly', 'per month'],
                        3 => ['bi_monthly', 'every two months', 'bimonthly'],
                        4 => ['quarterly', 'every 3 months'],
                        5 => ['half_yearly', 'semi annual', 'semi-annual'],
                        6 => ['yearly', 'annually', 'per year'],
                    ];

                    $excelValue = strtolower(trim($row['invoicing_frequency']));//Frequency
                    $excelValue = preg_replace('/\s+/', ' ', $excelValue);

                    $paymentModeKey = null;

                    foreach ($rentModes as $key => $synonyms) {
                        foreach ($synonyms as $word) {
                            if ($excelValue === strtolower($word)) {
                                $paymentModeKey = $key;
                                break 2;
                            }
                        }
                    }

                    preg_match('/^[A-Z]+/', $row['rent_per_month'], $matches);
                    $currency = $matches[0] ?? ''; 
                    $amount = preg_replace('/[^\d.]/', '', $row['rent_per_month']); 
                    $amount          = (float) $amount;
                    $agreement_units = (new AgreementUnits())->setConnection('tenant')->create([
                        'agreement_id'          => $agreement->id,
                        'property_id'           => $property->id,
                        'commencement_date'     => $new_rent_start_date,
                        'expiry_date'           => $new_rent_end_date,
                        'unit_id'               => $unit_management->id,
                        'payment_mode'          => $paymentModeKey,

                        'rent_amount'           => $amount,
                        'rent_mode'             => $paymentModeKey,
                        'rental_gl'             => 0,
                        'vat_amount'            => 0,
                        'vat_percentage'        => 0,
                        'total_net_rent_amount' => $amount,
                        'total'                 => 0,
                    ]);
                }

            }
        }
    }
    private function transformDate($value)
    {
        try {
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
            }
            return $value ? date('Y-m-d', strtotime($value)) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

}
