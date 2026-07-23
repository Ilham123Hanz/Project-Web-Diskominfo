<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ambil seluruh user dengan role Petugas
        $petugasUsers = User::where('role', 'Petugas')
            ->orWhere('role', 'petugas')
            ->get();

        // Fallback: Jika tidak ada Petugas, ambil semua user non-Admin
        if ($petugasUsers->isEmpty()) {
            $petugasUsers = User::where('role', '!=', 'Admin')
                ->where('role', '!=', 'admin')
                ->get();
        }

        if ($petugasUsers->isEmpty()) {
            return;
        }

        // Koordinat Acak Sekitar Area Kantor Diskominfotik Lampung (Geofencing Simulation)
        $baseLat = -5.435421;
        $baseLng = 105.258321;

        // User Agent Simulator
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4 Safari/605.1.15',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1',
        ];

        // 2. Generate data presensi 10 hari ke belakang agar histori laporan lebih kaya
        for ($i = 10; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);

            // Lewati hari libur operasional (Sabtu & Minggu)
            if ($date->isWeekend()) {
                continue;
            }

            foreach ($petugasUsers as $user) {
                // Simulasi Probabilitas Kehadiran (85% Hadir, 15% Absen/Izin/Sakit)
                $isAttending = rand(1, 100) <= 85;

                if (!$isAttending) {
                    continue; // Skip untuk mendokumentasikan kondisi petugas yang tidak presensi
                }

                // --- LOGIKA WAKTU MASUK (CLOCK IN) ---
                // 70% Tepat Waktu (07:00 - 07:30), 30% Terlambat (07:31 - 08:30)
                $isLateScenario = rand(1, 100) <= 30;

                if ($isLateScenario) {
                    $clockInTime = Carbon::createFromTime(7, rand(31, 59), rand(0, 59))->toTimeString();
                } else {
                    $clockInTime = Carbon::createFromTime(7, rand(0, 30), rand(0, 59))->toTimeString();
                }

                // Kalkulasi status_in menggunakan method helper bawaan model jika ada
                $statusIn = method_exists(Attendance::class, 'checkIsLate')
                    ? Attendance::checkIsLate($clockInTime)
                    : ($clockInTime > '07:30:00' ? 'Terlambat' : 'Tepat Waktu');

                // --- LOGIKA WAKTU PULANG (CLOCK OUT) ---
                // Untuk hari H (hari ini), ada kemungkinan belum clock_out
                $isToday = $date->isToday();
                $hasClockedOut = $isToday ? (rand(1, 100) <= 40) : true;

                $clockOutTime = null;
                $statusOut = null;
                $notesOut = null;

                if ($hasClockedOut) {
                    // 80% Pulang Normal (16:00 - 17:00), 20% Pulang Awal (15:00 - 15:59)
                    $isEarlyScenario = rand(1, 100) <= 20;

                    if ($isEarlyScenario) {
                        $clockOutTime = Carbon::createFromTime(15, rand(0, 59), rand(0, 59))->toTimeString();
                    } else {
                        $clockOutTime = Carbon::createFromTime(16, rand(0, 59), rand(0, 59))->toTimeString();
                    }

                    $statusOut = method_exists(Attendance::class, 'checkIsEarlyLeave')
                        ? Attendance::checkIsEarlyLeave($clockOutTime)
                        : ($clockOutTime < '16:00:00' ? 'Pulang Awal' : 'Sesuai Jam Kerja');

                    $notesOut = $statusOut === 'Pulang Awal'
                        ? 'Izin penanganan insiden siber mendesak di lokasi OPD luar.'
                        : 'Serah terima workstation dan log operasional shift selesai.';
                }

                // Dynamic Note In
                $notesIn = match ($statusIn) {
                    'Terlambat'    => 'Terjebak kemacetan lalu lintas di jalur utama perkotaan.',
                    'Tepat Waktu'  => 'Masuk tugas piket operasional rutin.',
                    default        => 'Presensi tercatat otomatis.',
                };

                // Offset Geofencing Simulation (Jarak sekitar < 50 meter)
                $latIn = $baseLat + (rand(-100, 100) / 100000);
                $lngIn = $baseLng + (rand(-100, 100) / 100000);

                // --- PERSISTENSI DATA IDEMPOTEN (AMEN TERHADAP RE-SEED & UNIQUE CONSTRAINT) ---
                Attendance::updateOrCreate(
                    [
                        'user_id'         => $user->id,
                        'attendance_date' => $date->toDateString(),
                    ],
                    [
                        'clock_in'       => $clockInTime,
                        'clock_out'      => $clockOutTime,
                        'status_in'      => $statusIn,
                        'status_out'     => $statusOut,
                        'notes_in'       => $notesIn,
                        'notes_out'      => $notesOut,
                        'ip_address_in'  => '192.168.10.' . rand(10, 254),
                        'ip_address_out' => $clockOutTime ? '192.168.10.' . rand(10, 254) : null,
                        'device_agent'   => $userAgents[array_rand($userAgents)],
                    ]
                );
            }
        }
    }
}