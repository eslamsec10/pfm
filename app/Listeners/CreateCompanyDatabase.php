<?php
namespace App\Listeners;

use App\Events\CompanyCreated;
use App\Services\DatabaseCreationService;
use DirectoryIterator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CreateCompanyDatabase
{
    /**
     * Create the event listener.
     */
    public function __construct(protected DatabaseCreationService $databaseCreator)
    {}

    public function handle(CompanyCreated $event): void
    {
        $company                   = $event->company;
        $db                        = "finexerp_{$company->id}";
        $company->database_options = [
            'dbname' => $db,
        ];
        $company->save();

        // $this->databaseCreator->create($db);
        DB::statement("CREATE DATABASE `{$db}`");

        Config::set('database.connections.tenant.database', $db);
        DB::purge('tenant');
        DB::reconnect('tenant');
        // $dir = new DirectoryIterator(database_path('migrations/tenants'));
        $dir = new DirectoryIterator(database_path('migrations/tenants'));
        // $files = iterator_to_array($dir);
        // usort($files, function ($a, $b) {
        //     return strcmp($a->getFilename(), $b->getFilename());
        // });
        // foreach ($dir as $file) {
        //     if ($file->isFile()) {
        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path'     => 'database/migrations/tenants',
            '--force'    => true,
        ]);
        //     };
        // }
        // foreach ($dir as $file) {
        //     if ($file->isFile()) {
        //         Artisan::call('migrate', [
        //             '--database'        => 'tenant',
        //             '--path'  =>  'database/migrations/tenants/' . $file->getFilename(),
        //             '--force'   => true,
        //         ]);
        //     };
        // }

        $this->copyDataToTenantDB($db, $company);
    }

    private function copyDataToTenantDB(string $db, $company)
{
    DB::purge('tenant');
    Config::set('database.connections.tenant.database', $db);
    DB::reconnect('tenant');

    $tablesToCopy = [
        'roles', 'sections', 'permissions', 'regions', 'business_settings',
        'countries', 'country_masters', 'ownerships',
         'property_types',
         'business_activities', 'live_withs',
        'enquiry_statuses', 'enquiry_request_statuses','admins' ,
        //'departments', 'employee_types', 'views',
        // 'departments', 'employee_types', 'employees', 'agents',
      //  'complaint_categories', 'maintenance_types', 'warranty_types',
        //'receipt_settings', 'service_masters', 'company_settings'
    ];

    foreach ($tablesToCopy as $table) {
        $data = DB::table($table)->get();
        if ($data->isNotEmpty()) {
            foreach ($data as $row) {
                $rowArr = (array) $row;
                if (!DB::connection('tenant')->table($table)->where('id', $row->id)->exists()) {
                    DB::connection('tenant')->table($table)->insert($rowArr);
                }
            }
        }
    }

    // Copy groups
    $groupsData = DB::connection('mysql')->table('groups')
        ->whereNull('property_id')
        ->get();

    if ($groupsData->isNotEmpty()) {
        foreach ($groupsData as $row) {
            $rowArr = (array) $row;
            if (!DB::connection('tenant')->table('groups')->where('id', $row->id)->exists()) {
                DB::connection('tenant')->table('groups')->insert($rowArr);
            }
        }
    }

    $copiedGroupIds = $groupsData->pluck('id')->toArray();

    // Copy ledgers
    $ledgersData = DB::connection('mysql')->table('main_ledgers')
        ->whereIn('group_id', $copiedGroupIds)
        ->get();

    if ($ledgersData->isNotEmpty()) {
        foreach ($ledgersData as $row) {
            $rowArr = (array) $row;
            if (!DB::connection('tenant')->table('main_ledgers')->where('id', $row->id)->exists()) {
                DB::connection('tenant')->table('main_ledgers')->insert($rowArr);
            }
        }
    }

    // Copy latest user
    $latestUser = DB::table('users')->orderBy('id', 'desc')->first();
    if ($latestUser && !DB::connection("tenant")->table('users')->where('id', $latestUser->id)->exists()) {
        DB::connection("tenant")->table('users')->insert((array) $latestUser);
    }

    // Copy latest company
    $latestCompany = DB::table('companies')->orderBy('id', 'desc')->first();
    if ($latestCompany && !DB::connection("tenant")->table('companies')->where('id', $latestCompany->id)->exists()) {
        DB::connection("tenant")->table('companies')->insert((array) $latestCompany);
    }

    // Insert main branch
    $branch = [
        'name'             => 'Main Branch',
        'logo'             => $company->logo,
        'domain'           => $company->domain,
        'address'          => $company->address1,
        'database_options' => $company->database_options1,
    ];
    if (!DB::connection("tenant")->table('branches')->where('name', 'Main Branch')->exists()) {
        DB::connection("tenant")->table('branches')->insert((array) $branch);
    }

    // Reconnect tenant again
    DB::purge('tenant');
    Config::set('database.connections.tenant.database', 'finexerp_'.$company->id);
    DB::reconnect('tenant');

    // Copy last user again safely
    $latestUserAgain = DB::connection('mysql')->table('users')->orderBy('id', 'desc')->first();
    if ($latestUserAgain && !DB::connection("tenant")->table('users')->where('id', $latestUserAgain->id)->exists()) {
        DB::connection("tenant")->table('users')->insert((array) $latestUserAgain);
    }
}

    // private function copyDataToTenantDB(string $db, $company)
    // {
    //     DB::purge('tenant');
    //     Config::set('database.connections.tenant.database', $db);
    //     DB::reconnect('tenant');

    //     $tablesToCopy = ['roles', 'sections', 'permissions', 'regions', 'business_settings'
    //         , 'countries', 'country_masters', 'ownerships', 'property_types',
    //         // 'blocks', 'floors', 'unit_descriptions',
    //         // 'unit_types', 'unit_conditions', 'unit_parkings', //'groups','main_ledgers','invoice_settings' ,
    //         'views', 'business_activities', 'live_withs',
    //         'enquiry_statuses', 'enquiry_request_statuses',
    //         //  'units',
    //         //  'property_management','block_management', 'floor_management','unit_management',
    //         'departments', 'employee_types', 'employees', 'agents', 'complaint_categories',
    //         'maintenance_types', 'warranty_types', 'receipt_settings', 'service_masters', 'company_settings', 'admins'];
    //     foreach ($tablesToCopy as $table) {
    //         $data = DB::table($table)->get();
    //         if ($data->isNotEmpty()) {
    //             DB::connection('tenant')->table($table)->insert($data->map(function ($row) {
    //                 return (array) $row;
    //             })->toArray());
    //         }
    //     }

    //     $groupsData = DB::connection('mysql')->table('groups')
    //         ->whereNull('property_id')
    //         ->get();

    //     if ($groupsData->isNotEmpty()) {
    //         DB::connection('tenant')->table('groups')->insert($groupsData->map(fn($row) => (array) $row)->toArray());
    //     }

    //     $copiedGroupIds = $groupsData->pluck('id')->toArray();

    //     $ledgersData = DB::connection('mysql')->table('main_ledgers')
    //         ->whereIn('group_id', $copiedGroupIds)
    //         ->get();

    //     if ($ledgersData->isNotEmpty()) {
    //         DB::connection('tenant')->table('main_ledgers')->insert($ledgersData->map(fn($row) => (array) $row)->toArray());
    //     }

    //     $latestUser = DB::table('users')->orderBy('id', 'desc')->first();

    //     if ($latestUser) {
    //         DB::connection("tenant")->table('users')->insert((array) $latestUser);
    //     }

    //     $latestCompany = DB::table('companies')->orderBy('id', 'desc')->first();
    //     if ($latestCompany) {
    //         DB::connection("tenant")->table('companies')->insert((array) $latestCompany);
    //     }
    //     $branch = [
    //         'name'             => 'Main Branch',
    //         'logo'             => $company->logo,
    //         'domain'           => $company->domain,
    //         'address'          => $company->address1,
    //         'database_options' => $company->database_options1,
    //     ];
    //     DB::connection("tenant")->table('branches')->insert((array) $branch);
    //     DB::purge('tenant');
    //     DB::purge('tenant');

    //     Config::set('database.connections.tenant.database', 'finexerp_95');
    //     DB::reconnect('tenant');
    //     $latestCompany = DB::connection('mysql')->table('users')->orderBy('id', 'desc')->first();
    //     if ($latestCompany) {
    //         DB::connection("tenant")->table('users')->insert((array) $latestCompany);
    //     }

    // }
}
