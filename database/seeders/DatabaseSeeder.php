<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Doctor;
use App\Models\DoctorBranchSchedule;
use App\Models\Patient;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $dam = Branch::firstOrCreate(['code' => 'DAM'], ['name' => 'فرع دمشق', 'city' => 'دمشق']);
        $ale = Branch::firstOrCreate(['code' => 'ALE'], ['name' => 'فرع حلب', 'city' => 'حلب']);
        Branch::firstOrCreate(['code' => 'LAT'], ['name' => 'فرع اللاذقية', 'city' => 'اللاذقية']);
        $specialty = Specialty::firstOrCreate(['name' => 'طب عام']);
        $doctor = Doctor::firstOrCreate(['name' => 'د. سامر أحمد'], ['specialty_id' => $specialty->id, 'phone' => '0990000000']);
        DoctorBranchSchedule::firstOrCreate(['doctor_id' => $doctor->id, 'branch_id' => $dam->id, 'day_of_week' => 1], ['starts_at' => '09:00', 'ends_at' => '15:00']);
        DoctorBranchSchedule::firstOrCreate(['doctor_id' => $doctor->id, 'branch_id' => $ale->id, 'day_of_week' => 3], ['starts_at' => '10:00', 'ends_at' => '16:00']);
        Patient::firstOrCreate(['phone' => '0991111111'], ['name' => 'مريض تجريبي']);
        $admin = User::firstOrCreate(['email' => 'admin@balsam.test'], ['name' => 'مدير النظام', 'password' => 'password', 'role' => 'admin']);
        if (!$admin->tokens()->where('name', 'admin-demo')->exists()) $admin->createToken('admin-demo');
        User::firstOrCreate(['email' => 'reception@balsam.test'], ['name' => 'استقبال دمشق', 'password' => 'password', 'role' => 'receptionist', 'branch_id' => $dam->id]);
        User::firstOrCreate(['email' => 'doctor@balsam.test'], ['name' => 'د. سامر أحمد', 'password' => 'password', 'role' => 'doctor', 'doctor_id' => $doctor->id]);
    }
}
