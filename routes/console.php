<?php

use App\Models\User;
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

Artisan::command('create-admin', function () {
  $super_admin =  User::create([
        'name' => 'Admin',
        'email' => 'admin@admin.com',
        'password' => bcrypt('adminadmin'),
        'phone_number'=> '0242158675',
    ]);

  $this->comment("Created New User: $super_admin");

    // create 2 roles for both web and api guards
    $super_admin_role_web= Role::create(['guard_name'=>'web', 'name'=> 'super-admin']);
    $super_admin_role_api= Role::create(['guard_name'=>'api', 'name'=> 'super-admin']);

    $this->comment("Created New Roles: ". json_encode(["web"=> $super_admin_role_web, "api"=>$super_admin_role_api]) );


    // create 2 permissions for both web and api guards
    // TODO: Rewrite this into [wildcards](https://spatie.be/docs/laravel-permission/v3/basic-usage/wildcard-permissions#subparts) when more permission are added
    /*
    $register_user_web = Permission::create(['guard_name'=>'web', 'name' => 'register-user']);
    $register_user_api = Permission::create(['guard_name'=>'api', 'name' => 'register-user']);
    */
    $all_permission_web = Permission::create(['guard_name'=>'web', 'name' => '*.create,update,view,delete']);
    $all_permission_api = Permission::create(['guard_name'=>'api', 'name' => '*.create,update,view,delete']);

    // give the 2 roles their permission
    $super_admin_role_web->givePermissionTo($all_permission_web);
    $super_admin_role_api->givePermissionTo($all_permission_api);

    // give the admin some permissions
    $super_admin->givePermissionTo($all_permission_web);
    $super_admin->givePermissionTo($all_permission_api);
    // assign roles to the admin
    $super_admin->assignRole($super_admin_role_web);
    $super_admin->assignRole($super_admin_role_api);



})->describe('Create the admin');
