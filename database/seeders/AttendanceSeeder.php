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
        // Ambil semua user ber-role petugas untuk dijadikan subjek data absensi
        $petugasUsers = User::where('role', '!=', 'Admin')->get();

        // Jika user kosong, buat fallback user simulasi
        if ($petugasUsers->isEmpty()) {
            return;
        }

        // Generate data absensi mundur untuk 5 hari ke belakang
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            
            // Lewati hari Sabtu dan Minggu jika libur operasional reguler
            if ($date->isWeekend()) {
                continue;
            }

            foreach ($petugasUsers as $user) {
                // Variasi jam masuk simulasi (Acak: Tepat Waktu atau Terlambat)
                // Batas reguler: 07:30
                $randomMinutesIn = rand(15, 45); // e.g. menit ke 15 s/d 45
                $clockInTime = Carbon::createFromTime(7, $randomMinutesIn, 0)->toTimeString();
                $statusIn = Attendance::checkIsLate($clockInTime);

                // Variasi jam pulang simulasi (Acak: Pulang Awal atau Selesai Waktu)
                // Batas reguler: 16:00
                $randomMinutesOut = rand(50, 75); // e.g. jam 15:50 atau jam 16:15
                $clockOutTime = Carbon::createFromTime(15, 0, 0)->addMinutes($randomMinutesOut)->toTimeString();
                $statusOut = Attendance::checkIsEarlyLeave($clockOutTime);

                Attendance::create([
                    'user_id'         => $user->id,
                    'attendance_date' => $date->toDateString(),
                    'clock_in'        => $clockInTime,
                    'clock_out'       => $clockOutTime,
                    'status_in'       => $statusIn,
                    'status_out'      => $statusOut,
                    'notes_in'        => $statusIn === 'Terlambat' ? 'Terjebak macet di jalur perimeter utama.' : 'Masuk tugas reguler aman.',
                    'notes_out'       => 'Serah terima workstation selesai, semua sistem clear.',
                    'ip_address_in'   => '192.168.10.' . rand(10, 254),
                    'ip_address_out'  => '192.168.10.' . rand(10, 254),
                    'device_agent'    => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/126.0.0.0 Safari/537.36',
                ]);
            }
        }
    }
}