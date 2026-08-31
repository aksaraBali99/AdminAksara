<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $employees = [
            [
                'name' => 'Andi Pratama',
                'email' => 'andi@aksaravirtual.com',
                'position' => 'Project Manager',
                'employee_type' => 'full_time',
                'employee_status' => 'active',
                'base_salary' => 10000000,
                'ktp_status' => 'verified',
                'start_work_date' => '2024-01-15',
            ],
            [
                'name' => 'Siti Rahayu',
                'email' => 'siti@aksaravirtual.com',
                'position' => 'Virtual Assistant Lead',
                'employee_type' => 'full_time',
                'employee_status' => 'active',
                'base_salary' => 8000000,
                'ktp_status' => 'verified',
                'start_work_date' => '2024-02-01',
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@aksaravirtual.com',
                'position' => 'Senior Virtual Assistant',
                'employee_type' => 'full_time',
                'employee_status' => 'active',
                'base_salary' => 6500000,
                'ktp_status' => 'pending',
                'start_work_date' => '2024-03-10',
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi.freelance@aksaravirtual.com',
                'position' => 'Graphic Designer',
                'employee_type' => 'freelance',
                'employee_status' => 'active',
                'base_salary' => 4000000,
                'ktp_status' => 'not_submitted',
                'start_work_date' => '2024-06-01',
            ],
        ];

        foreach ($employees as $employee) {
            Employee::updateOrCreate(
                ['email' => $employee['email']],
                $employee
            );
        }
    }
}
