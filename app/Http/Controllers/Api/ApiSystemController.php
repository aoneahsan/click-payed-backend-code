<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\UserAccount;
use App\Model\UserDetails;
use App\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ApiSystemController extends Controller
{
    public function InitAppDefaultUsersSetup()
    {
        // Creating Admin
        $referer_code_admin = $this->random_strings(6);
        $admin = User::create([
            'name' => 'admin',
            'email' => 'a@cp.c',
            'phone_number' => '03000000001',
            'password' => Hash::make('03000000001'),
            'role' => 'admin',
            'referer_code' => $referer_code_admin
        ]);
        UserDetails::create([
            'user_id' => $admin->id
        ]);
        UserAccount::create([
            'user_id' => $admin->id
        ]);

        // Creating Editor
        $referer_code_editor = $this->random_strings(6);
        $editor = User::create([
            'name' => 'editor',
            'email' => 'e@cp.c',
            'phone_number' => '03000000002',
            'password' => Hash::make('03000000002'),
            'role' => 'editor',
            'referer_code' => $referer_code_editor,
            'referer_user_id' => $admin->id
        ]);
        UserDetails::create([
            'user_id' => $editor->id
        ]);
        UserAccount::create([
            'user_id' => $editor->id
        ]);

        // Creating Simple User
        $referer_code_user = $this->random_strings(6);
        $user = User::create([
            'name' => 'user',
            'email' => 'u@cp.c',
            'phone_number' => '03000000003',
            'password' => Hash::make('03000000003'),
            'role' => 'user',
            'referer_code' => $referer_code_user,
            'referer_user_id' => $admin->id
        ]);
        UserDetails::create([
            'user_id' => $user->id
        ]);
        UserAccount::create([
            'user_id' => $user->id
        ]);

        // Roles
        $admin_role = Role::create(['name' => 'admin']);
        $editor_role = Role::create(['name' => 'editor']);
        $user_role = Role::create(['name' => 'user']);

        // Permissions
        $app_user_p = Permission::create(['name' => 'app_user']);
        $view_dashboard_p = Permission::create(['name' => 'view_dashboard']);
        $create_notifications_p = Permission::create(['name' => 'create_notifications']);
        $create_notices_p = Permission::create(['name' => 'create_notices']);
        $process_deposits_p = Permission::create(['name' => 'process_deposits']);
        $process_withdrawals_p = Permission::create(['name' => 'process_withdrawals']);

        // Giving Permissions to Roles
        // Admin Role
        $admin_role->givePermissionTo($app_user_p);
        $admin_role->givePermissionTo($view_dashboard_p);
        $admin_role->givePermissionTo($create_notifications_p);
        $admin_role->givePermissionTo($process_deposits_p);
        $admin_role->givePermissionTo($process_withdrawals_p);
        // Editor Role
        $editor_role->givePermissionTo($app_user_p);
        $editor_role->givePermissionTo($view_dashboard_p);
        // Simple User Role
        $user_role->givePermissionTo($app_user_p);

        // Assigning Roles To Users
        $admin->assignRole($admin_role);
        $editor->assignRole($editor_role);
        $user->assignRole($user_role);

        return "All Done Users Created and Roles Assigned With respective Permissions";
    }

    public function refererCode()
    {
        
        // This function will generate
        // Random string of length 10
        $referer_code = $this->random_strings(6);
        echo $referer_code;
    }

    public function random_strings($length_of_string)
    {
        $str_result = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz';
        return strtolower(substr(str_shuffle($str_result), 0, $length_of_string));
    }

    public function checkRefererCode()
    {
        $admin = User::where('id', 1)->with('referals')->get();
        dd($admin->toArray());
        return response()->json(['data' => $admin], 200);
    }
}
