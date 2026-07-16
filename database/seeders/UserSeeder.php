<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\App;
use Faker\Factory as Faker;

class UserSeeder extends Seeder 
{
    /**
     * Menjalankan seeding data user dengan role Admin dan Petugas.
     * Menggunakan pendekatan idempotent (aman dijalankan berulang kali).
     */
    public function run(): void 
    {
        // Hindari error integrity constraint (Foreign Key) jika ingin membersihkan data lokal
        if (App::environment('local')) {
            Schema::disableForeignKeyConstraints();
            User::truncate(); 
            Schema::enableForeignKeyConstraints();
        }

        // =========================================================================
        // 1. DATA MASTER CORE USER (ADMIN & PETUGAS TETAP)
        // =========================================================================
        $coreUsers = [
            // Sub-Grup: Administrator
            [
                'name'     => 'Iswara',
                'username' => 'iswara.admin',
                'password' => Hash::make('Iswara@Siber2026'),
                'role'     => 'Admin',
            ],
            [
                'name'     => 'Manda',
                'username' => 'manda.admin',
                'password' => Hash::make('Manda@Siber2026'),
                'role'     => 'Admin',
            ],
            [
                'name'     => 'Arie',
                'username' => 'arie.admin',
                'password' => Hash::make('Arie@Siber2026'),
                'role'     => 'Admin',
            ],
            // Sub-Grup: Petugas Lapangan Utama
            [
                'name'     => 'Rian Hidayat',
                'username' => 'rian.hidayat',
                'password' => Hash::make('Rian@Siber2026'),
                'role'     => 'Petugas',
            ],
            [
                'name'     => 'Siti Aminah',
                'username' => 'siti.aminah',
                'password' => Hash::make('Siti@Siber2026'),
                'role'     => 'Petugas',
            ],
        ];

        // Eksekusi menggunakan updateOrCreate untuk mencegah error duplikasi saat seed berulang
        foreach ($coreUsers as $user) {
            User::updateOrCreate(
                ['username' => $user['username']], // Kunci pengecekan unik
                [
                    'name'     => $user['name'],
                    'password' => $user['password'],
                    'role'     => $user['role'],
                ]
            );
        }

        // =========================================================================
        // 2. OPSI DATA TAMBAHAN (HANYA AKTIF PADA LINGKUNGAN TESTING/LOCAL)
        // =========================================================================
        if (App::environment('local', 'testing')) {
            $faker = Faker::create('id_ID'); // Melokalisasi generator nama ke Indonesia
            
            // Buat 15 akun petugas dummy tambahan untuk testing pagination tabel rekap admin
            for ($i = 1; $i <= 15; $i++) {
                $firstName = $faker->firstName;
                $lastName  = $faker->lastName;
                $username  = strtolower($firstName . '.' . $lastName . rand(10, 99));

                User::updateOrCreate(
                    ['username' => $username],
                    [
                        'name'     => $firstName . ' ' . $lastName,
                        'password' => Hash::make('Petugas@2026'), // Password standard untuk akun tiruan
                        'role'     => 'Petugas',
                    ]
                );
            }
        }
    }
}