<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\User;
use App\Model\UserDetails;
use App\Model\UserAccount;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ApiSystemController extends Controller
{
    public function InitAppDefaultUsersSetup()
    {
        // Creating Admin
        $admin = User::create([
            'name' => 'admin',
            'email' => 'a@cp.c',
            'phone_number' => '03046619706',
            'password' => Hash::make('aaaaaa'),
            'role' => 'admin'
        ]);
        UserDetails::create([
            'user_id' => $admin->id
        ]);
        UserAccount::create([
            'user_id' => $admin->id
        ]);

        // Creating Editor
        $editor = User::create([
            'name' => 'editor',
            'email' => 'e@cp.c',
            'phone_number' => '03046619706',
            'password' => Hash::make('aaaaaa'),
            'role' => 'editor'
        ]);
        UserDetails::create([
            'user_id' => $editor->id
        ]);
        UserAccount::create([
            'user_id' => $editor->id
        ]);

        // Creating Simple User
        $user = User::create([
            'name' => 'user',
            'email' => 'u@cp.c',
            'phone_number' => '03046619706',
            'password' => Hash::make('aaaaaa'),
            'role' => 'user'
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
}
