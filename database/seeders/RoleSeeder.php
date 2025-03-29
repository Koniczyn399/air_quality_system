<?php

namespace Database\Seeders;

use App\Enums\Auth\PermissionType;
use App\Enums\Auth\RoleType;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Uruchomienie konkretnego seedera:
        // sail artisan db:seed --class=RoleSeeder

        // Reset cache'a ról i uprawnień:
        // sail artisan permission:cache-reset
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Role::create(['name' => RoleType::ADMIN]);
        Role::create(['name' => RoleType::MAINTEINER]);
        Role::create(['name' => RoleType::USER]);

        // ADMINISTRATOR SYSTEMU
        $adminRole = Role::findByName(RoleType::ADMIN->value);
        $adminRole->givePermissionTo(PermissionType::USER_ACCESS->value);
        $adminRole->givePermissionTo(PermissionType::USER_MANAGE->value);



        // Serwisant
        $workerRole = Role::findByName(RoleType::MAINTEINER->value);
        $workerRole->givePermissionTo(PermissionType::USER_ACCESS->value);
        $workerRole->givePermissionTo(PermissionType::USER_MANAGE->value);


        // UŻYTKOWNIK
        $userRole = Role::findByName(RoleType::USER->value);
        $userRole->givePermissionTo(PermissionType::USER_ACCESS->value);
        $userRole->givePermissionTo(PermissionType::USER_MANAGE->value);


    }
}
