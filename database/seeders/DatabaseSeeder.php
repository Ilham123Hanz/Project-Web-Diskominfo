<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Mengorkestrasi pengisian data awal secara sekuensial berdasarkan dependensi tabel.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,       // Mengisi data akun (Admin & Petugas) terlebih dahulu
            AttendanceSeeder::class, // Mengisi riwayat absensi tiruan yang terikat ke User
        ]);
    }
}