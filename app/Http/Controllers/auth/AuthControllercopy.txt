<?php
namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        try {
            $request->validate([
                'company_id' => 'required',
                'domain'     => 'required',
            ]);
            // 1. اتصل بقاعدة بيانات admin لإيجاد الشركة
            Config::set('database.connections.mysql.database', 'property_management_admin');
            DB::purge('mysql');
            DB::reconnect('mysql');

            $company = (new Company())
                ->setConnection('mysql')
                ->where('company_id', $request->company_id)
                ->where('domain', $request->domain)
                ->first();

            if (! $company) {
                return redirect()->back()->with('error', __('login.company_not_found'));
            }

// 2. فك تشفير إعدادات قاعدة البيانات الخاصة بالشركة
            $dbOptions = json_decode($company->database_options, true);
            if (! isset($dbOptions['dbname'])) {
                return redirect()->back()->with('error', __('login.database_not_found'));
            }

// 3. غير الاتصال لقاعدة بيانات الشركة (tenant)
            Config::set('database.connections.tenant.database', $dbOptions['dbname']);
            DB::purge('tenant');
            DB::reconnect('tenant');

// 4. جرب تسجيل الدخول باستخدام اتصال tenant
            if (isset($request['user_name']) && Auth::guard('web')->attempt([
                'user_name' => $request['user_name'],
                'password'  => $request['password'],
            ])) {
                $user = Auth::guard('web')->user();
                // dd($user);
                session()->put('user_logged_in', true);
                session()->put('user_id', $user->id);

                return redirect()->route('main_dashboard');
            } else {
                return redirect()->back()->with('error', __('login.user_not_found'));
            }

            // Config::set('database.connections.mysql.database', 'property_management_admin');
            // DB::purge('tenant');
            // DB::reconnect('tenant');
            // $company =  (new Company())->setConnection('mysql')->where('company_id', $request->company_id)->where('domain' , $request->domain)->first();
            // if(!isset($company)){
            //     return redirect()->back()->with('error', __('login.company_not_found'));
            // }
            // if (isset($request['user_name']) && auth()->attempt(['user_name' => $request['user_name'], 'password' => $request['password']])) {
            //    dd("user");

            //     $user    = (new User())->setConnection('mysql')->where('user_name', $request['user_name'])->first();
            //     $company = (new Company())->setConnection('mysql')->where('id', $user->company_id)->first();
            //     // dd($company , 'new');
            //     if ($company && $company->domain) {
            //         session()->put('user_logged_in', true);
            //         session()->put('user_id', $user->id);

            //         $dbOptions = json_decode($company->database_options, true);
            //         if (isset($dbOptions['dbname'])) {
            //             Config::set('database.connections.tenant.database', $dbOptions['dbname']);
            //             DB::purge('tenant');
            //             DB::reconnect('tenant');
            //             dd(DB::connection('tenant')->getDatabaseName(), 'new');
            //         }

            //         // return redirect()->away('https://' . $company->domain);
            //         // return redirect()->away('http://' . $company->domain.'/dev-pfm/dev-pfm.finexerp.com/pfm');  //.'/https://admin-pfm.finexerp.com/'
            //     }
            // }
            // // elseif (isset($request['email']) && auth()->attempt(['email' => $request['email'], 'password' => $request['password']])) {
            // //     $user = (new User())->setConnection('mysql')->where('email', $request['email'])->first();
            // //     session()->put('user_logged_in', true);
            // // }

            // if (auth()->check()) {

            //     return redirect()->route('main_dashboard');
            // } else {
            //     return redirect()->back()->with('error', __('login.user_not_found'));
            // }

        } catch (\Throwable $th) {
            return redirect()->back()->with("error", $th->getMessage());

        }
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('login-page');
    }
    public function admin_login(Request $request)
    {
        // Config::set('database.connections.mysql.database' , 'finexerp');
        // DB::purge('mysql');
        // DB::reconnect('mysql');
        // dd( DB::connection()->getDatabaseName()    );
        // Config::set('database.connections.mysql.database', 'property_management_admin');
        // DB::purge('mysql');
        // DB::reconnect('mysql');
        dd($request->all());
        if (isset($request['user_name']) && Auth::guard('admins')->attempt(['user_name' => $request->input('user_name'), 'password' => $request->input('password')])) {
            $user = (new Admin())->setConnection('mysql')->where('user_name', $request['user_name'])->first();
            session()->put('user_logged_in', true);
            session()->put('admin_id', $user->id);

            // elseif (isset($request['email']) && Auth::guard('admins')->attempt(['email' => $request->input('email'), 'password' => $request->input('password')])){
            //     $user = Admin::where('email', $request->input('email'))->first();
            //     session()->put('user_logged_in', true);
            // }

            if (auth()->guard('admins')->check()) {
                // dd($request->all());

                return redirect()->route('admin.dashboard');

                // return redirect()->route('admin.dashboard');
            } else {
                // dd("Eeroe");
                return redirect()->back()->with('error', __('login.user_not_found'));
            }
        }
        return redirect()->back()->with('error', __('login.user_not_found'));

    }

    public function admin_logout()
    {
        Auth::guard('admins')->logout();
        session()->invalidate();
        session()->regenerateToken();
        return to_route('admin.login-page');
    }
    public function login_page()
    {

        return view('auth.login');
    }
}
