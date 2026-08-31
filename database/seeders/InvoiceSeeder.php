<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Client;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::all();
        
        if ($clients->isEmpty()) {
            return;
        }

        $invoices = [
            [
                'client_id' => $clients[0]->id ?? 1,
                'invoice_date' => Carbon::now()->subDays(30),
                'due_date' => Carbon::now()->subDays(15),
                'currency' => 'IDR',
                'description' => 'Virtual Assistant Services - December 2025',
                'service_type' => 'va_service',
                'amount' => 15000000,
                'status' => 'paid',
            ],
            [
                'client_id' => $clients[1]->id ?? 1,
                'invoice_date' => Carbon::now()->subDays(20),
                'due_date' => Carbon::now()->subDays(5),
                'currency' => 'IDR',
                'description' => 'Social Media Management - December 2025',
                'service_type' => 'social_media',
                'amount' => 8500000,
                'status' => 'paid',
            ],
            [
                'client_id' => $clients[2]->id ?? 1,
                'invoice_date' => Carbon::now()->subDays(10),
                'due_date' => Carbon::now()->addDays(5),
                'currency' => 'AUD',
                'description' => 'Website Maintenance - January 2026',
                'service_type' => 'web_development',
                'amount' => 1500,
                'status' => 'sent',
            ],
            [
                'client_id' => $clients[3]->id ?? 1,
                'invoice_date' => Carbon::now()->subDays(5),
                'due_date' => Carbon::now()->addDays(10),
                'currency' => 'IDR',
                'description' => 'Content Creation - January 2026',
                'service_type' => 'content_creation',
                'amount' => 5000000,
                'status' => 'sent',
            ],
            [
                'client_id' => $clients[4]->id ?? 1,
                'invoice_date' => Carbon::now(),
                'due_date' => Carbon::now()->addDays(14),
                'currency' => 'USD',
                'description' => 'Design Package - January 2026',
                'service_type' => 'design',
                'amount' => 800,
                'status' => 'draft',
            ],
        ];

        foreach ($invoices as $invoice) {
            Invoice::create($invoice);
        }
    }
}
