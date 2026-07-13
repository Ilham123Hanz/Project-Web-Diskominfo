<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder 
{
    /**
     * Menjalankan seeding data dengan username dinamis dan password terenkripsi.
     */
    public function run(): void 
    {
        // Guna menghindari masalah foreign key saat membersihkan data, kita matikan cek foreign key sebentar
        Schema::disableForeignKeyConstraints();
        User::truncate(); // Sapu bersih data user lama yang password-nya masih teks polos
        Schema::enableForeignKeyConstraints();

        // 1. Akun ADMIN (Menggunakan nama asli admin & password kombinasi instansi)
        User::create([
            'name'     => 'Ahmad Sanusi',              // Nama asli Admin
            'username' => 'ahmad.sanusi',             // Username menyesuaikan nama
            'password' => Hash::make('Sanusi@Siber2026'), // Terenkripsi menggunakan bcrypt via Hash::make
            'role'     => 'Admin',
        ]);

        // 2. Akun PETUGAS / OPERATOR 1
        User::create([
            'name'     => 'Rian Hidayat',              // Nama asli Petugas
            'username' => 'rian.hidayat',             // Username menyesuaikan nama
            'password' => Hash::make('Rian@Siber2026'), // Terenkripsi menggunakan bcrypt via Hash::make
            'role'     => 'Petugas',
        ]);

        // 3. Akun PETUGAS / OPERATOR 2 (Contoh tambahan jika ada petugas lain)
        User::create([
            'name'     => 'Siti Aminah',
            'username' => 'siti.aminah',
            'password' => Hash::make('Siti@Siber2026'), // Terenkripsi menggunakan bcrypt via Hash::make
            'role'     => 'Petugas',
        ]);
    }
}