<?php

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('create-super-admin', function () {
  $super_admin =  User::create([
        'username' => 'super-admin',
        'email' => 'super@admin.com',
        'password' => bcrypt('super-admin'),
        'phone_number'=> '0123456789',
    ]);

//  $this->comment("Created New User: $super_admin");

    // create 2 roles for both web and api guards
    $super_admin_role_web= Role::create(['guard_name'=>'web', 'name'=> 'super-admin']);
    $super_admin_role_api= Role::create(['guard_name'=>'api', 'name'=> 'super-admin']);

//    $this->comment("Created New Roles: ". json_encode(["web"=> $super_admin_role_web, "api"=>$super_admin_role_api]) );


    // create 2 permissions for both web and api guards
    // TODO: Rewrite this into [wildcards](https://spatie.be/docs/laravel-permission/v3/basic-usage/wildcard-permissions#subparts) when more permission are added

    /*
     * // Create a single permission
    $register_user_web = Permission::create(['guard_name'=>'web', 'name' => 'register-user']);
    $register_user_api = Permission::create(['guard_name'=>'api', 'name' => 'register-user']);
    */
    /*
     * // Create a wildcard permission to allow access to all resources
    $all_permission_web = Permission::create(['guard_name'=>'web', 'name' => '*.create,update,view,delete']);
    $all_permission_api = Permission::create(['guard_name'=>'api', 'name' => '*.create,update,view,delete']);
    */

    $permissionList = ['register-user', 'update-user', 'deactivate-user', 'assign-role', 'unassigned-role', 'view-secret'];
    $permissions_web = collect($permissionList)->map(function ($permission) {
        return [
            'guard_name' => 'web',
            'name' => $permission,
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString(),
        ];
    });

    $permissions_api = collect($permissionList)->map(function ($permission) {
        return [
            'guard_name' => 'api',
            'name' => $permission,
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString(),
        ];
    });

    $all_permissions = $permissions_web->concat($permissions_api)->toArray();
    Permission::insertOrIgnore($all_permissions);


    // give the admin some permissions
    $super_admin->givePermissionTo(Permission::all());
    $super_admin->givePermissionTo(Permission::all());
    // assign roles to the admin
    $super_admin->assignRole($super_admin_role_web);
    $super_admin->assignRole($super_admin_role_api);

    $this->comment("Created super-admin\n\tusername: admin\n\temail: admin@admin.com\n\tpassword: adminadmin\n\tphone_number: 0123456789");


})->describe('Create the admin');
