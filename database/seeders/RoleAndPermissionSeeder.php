<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Modul-modul yang ada di aplikasi CMMS TKJ.
     * Setiap modul otomatis dibuatkan permission: view, create, edit, delete.
     */
    protected array $modules = [
        'assets',
        'asset_categories',
        'work_orders',
        'spare_parts',
        'pm_schedules',
        'pm_templates',
        'sites',
        'buildings',
        'floors',
        'location_areas',
        'users',
        'roles',
        'reports',
    ];

    protected array $actions = ['view', 'create', 'edit', 'delete'];

    /**
     * Permission khusus di luar pola view/create/edit/delete.
     * PENTING: dibuat SEBELUM Super Admin di-sync, supaya Super Admin
     * benar-benar mendapat SEMUA permission (termasuk yang custom ini).
     */
    protected array $customPermissions = [
        'approve_work_orders',  // menyetujui/menolak work order
        'assign_work_orders',   // menugaskan teknisi ke work order
        'execute_work_orders',  // mulai & menyelesaikan work order
    ];

    public function run(): void
    {
        // Reset cache permission (wajib, biar tidak ada permission "nyangkut" dari run sebelumnya)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Generate semua permission dari kombinasi modul x action
        foreach ($this->modules as $module) {
            foreach ($this->actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$action}_{$module}",
                    'guard_name' => 'web',
                ]);
            }
        }

        // 2. Generate permission custom (harus sebelum Super Admin di-sync)
        foreach ($this->customPermissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        // 3. Buat role
        $superAdmin  = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $adminSite   = Role::firstOrCreate(['name' => 'Admin Site', 'guard_name' => 'web']);
        $manager     = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $teknisi     = Role::firstOrCreate(['name' => 'Teknisi', 'guard_name' => 'web']);
        $requester   = Role::firstOrCreate(['name' => 'Requester', 'guard_name' => 'web']);
        $supervisor  = Role::firstOrCreate(['name' => 'Supervisor Maintenance', 'guard_name' => 'web']);

        // 4. Super Admin dapat SEMUA permission
        $superAdmin->syncPermissions(Permission::all());

        // 5. Admin Site: full CRUD ke modul operasional + bisa menugaskan teknisi,
        //    tapi Users/Roles cuma view dan tidak punya wewenang approve.
        $adminSite->syncPermissions([
            ...$this->permsFor(['assets', 'asset_categories', 'work_orders', 'spare_parts',
                                 'pm_schedules', 'pm_templates', 'sites', 'buildings',
                                 'floors', 'location_areas', 'reports'], $this->actions),
            'assign_work_orders',
            'view_users',
            'view_roles',
        ]);

        // 6. Manager: view semua, bisa approve/reject dan menugaskan teknisi
        $manager->syncPermissions([
            ...$this->permsFor(['assets', 'work_orders', 'spare_parts', 'reports'], ['view']),
            'approve_work_orders',
            'assign_work_orders',
        ]);

        // 7. Teknisi: view assets/spare_parts, view+execute work order
        $teknisi->syncPermissions([
            'view_assets',
            'view_spare_parts',
            'view_work_orders',
            'execute_work_orders',
        ]);

        // 8. Requester: create + view work order milik sendiri (scoping "milik sendiri" diatur di Policy, bukan di sini)
        $requester->syncPermissions([
            'view_work_orders',
            'create_work_orders',
        ]);

        // 9. Supervisor Maintenance: view + bisa menugaskan teknisi, tapi tanpa approve
        $supervisor->syncPermissions([
            ...$this->permsFor(['assets', 'work_orders', 'spare_parts', 'reports'], ['view']),
            'assign_work_orders',
        ]);
    }

    /**
     * Helper: generate nama permission dari kombinasi modul x action.
     */
    protected function permsFor(array $modules, array $actions): array
    {
        $result = [];
        foreach ($modules as $module) {
            foreach ($actions as $action) {
                $result[] = "{$action}_{$module}";
            }
        }
        return $result;
    }
}
