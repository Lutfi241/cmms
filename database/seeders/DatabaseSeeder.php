<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat role & permission dulu, sebelum assign ke user
        $this->call(RoleAndPermissionSeeder::class);

        // 2. Buat 1 akun Super Admin untuk login pertama kali
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@cmms-tkj.test'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'), // ganti setelah login pertama
            ]
        );
        $superAdmin->assignRole('Super Admin');

        // 3. (Opsional) akun contoh untuk role lain, memudahkan testing manual
        $teknisi = User::firstOrCreate(
            ['email' => 'teknisi@cmms-tkj.test'],
            ['name' => 'Teknisi Demo', 'password' => bcrypt('password')]
        );
        $teknisi->assignRole('Teknisi');

        $requester = User::firstOrCreate(
            ['email' => 'requester@cmms-tkj.test'],
            ['name' => 'Requester Demo', 'password' => bcrypt('password')]
        );
        $requester->assignRole('Requester');
    }
}
