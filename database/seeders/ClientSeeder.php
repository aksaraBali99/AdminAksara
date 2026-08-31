<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $employee = Employee::first();
        
        $clients = [
            [
                'company_name' => 'PT Maju Bersama',
                'contact_name' => 'Hendra Wijaya',
                'email' => 'hendra@majubersama.com',
                'phone_number' => '081234567890',
                'address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
                'start_date' => '2024-01-20',
                'assigned_employee_id' => $employee?->id,
            ],
            [
                'company_name' => 'CV Sukses Mandiri',
                'contact_name' => 'Diana Putri',
                'email' => 'diana@suksesmandiri.co.id',
                'phone_number' => '081298765432',
                'address' => 'Jl. Gatot Subroto Kav. 45, Jakarta Selatan',
                'start_date' => '2024-02-15',
                'assigned_employee_id' => $employee?->id,
            ],
            [
                'company_name' => 'Digital Solutions Australia',
                'contact_name' => 'Michael Chen',
                'email' => 'michael@digitalsolutions.com.au',
                'phone_number' => '+61412345678',
                'address' => 'Level 5, 100 Collins Street, Melbourne VIC 3000',
                'start_date' => '2024-03-01',
                'assigned_employee_id' => $employee?->id,
            ],
            [
                'company_name' => 'Startup Hub ID',
                'contact_name' => 'Ricky Tanaka',
                'email' => 'ricky@startuphub.id',
                'phone_number' => '081555123456',
                'address' => 'CoHive Building, Jl. Mega Kuningan Barat',
                'start_date' => '2024-04-10',
                'assigned_employee_id' => null,
            ],
            [
                'company_name' => 'Fashion Forward LLC',
                'contact_name' => 'Amanda Lee',
                'email' => 'amanda@fashionforward.com',
                'phone_number' => '+1234567890',
                'address' => '500 Fashion Ave, New York, NY 10018',
                'start_date' => '2024-05-01',
                'assigned_employee_id' => null,
            ],
        ];

        foreach ($clients as $client) {
            Client::updateOrCreate(
                ['email' => $client['email']],
                $client
            );
        }
    }
}
