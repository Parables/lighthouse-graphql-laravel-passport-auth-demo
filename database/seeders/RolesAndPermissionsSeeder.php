<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Option 1
        // create permissions
        /*
        Permission::create(['name' => 'edit articles']);
        Permission::create(['name' => 'delete articles']);
        Permission::create(['name' => 'publish articles']);
        Permission::create(['name' => 'unpublish articles']);
        */

        // create roles and assign created permissions

        // this can be done as separate statements
        /*
        $role = Role::create(['name' => 'writer']);
        $role->givePermissionTo('edit articles');
        */

        // or may be done by chaining
        /*
        $role = Role::create(['name' => 'moderator'])
             ->givePermissionTo(['publish articles', 'unpublish articles']);
        */

        /*
        $role = Role::create(['name' => 'super-admin']);
         $role->givePermissionTo(Permission::all());
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

        $super_admin_role_web = Role::create(['guard_name' => 'web', 'name' => 'super-admin']);
        $super_admin_role_api = Role::create(['guard_name' => 'api', 'name' => 'super-admin']);

        $super_admin = User::create([
            'username' => 'super-admin',
            'email' => 'super@admin.com',
            'password' => bcrypt('super-admin'),
            'phone_number' => '0123456789',
        ])->givePermissionTo(Permission::all())
            ->assignRole([$super_admin_role_web, $super_admin_role_api]);

    }
}
