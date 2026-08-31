<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTotalRevenueTest extends TestCase
{
    use RefreshDatabase;

    private function makeClient(): Client
    {
        return Client::create([
            'company_name' => 'Acme Corp',
            'contact_name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);
    }

    public function test_total_revenue_converts_mixed_currency_paid_invoices_to_idr(): void
    {
        Setting::create(['key' => 'exchange_rate_usd', 'value' => '15800', 'group' => 'finance']);
        Setting::create(['key' => 'exchange_rate_aud', 'value' => '10500', 'group' => 'finance']);

        $client = $this->makeClient();

        // IDR invoice: contributes its amount as-is.
        Invoice::create([
            'client_id' => $client->id,
            'invoice_date' => now(),
            'due_date' => now()->addDays(14),
            'currency' => 'IDR',
            'status' => 'paid',
            'paid_amount_idr' => 10000000,
            'paid_date' => now(),
            'service_type' => 'virtual_assistant_fee',
            'amount' => 10000000,
        ]);

        // USD invoice: no paid_amount_idr recorded, so it must fall back to
        // amount converted at the current rate (20 * 15800 = 316,000).
        Invoice::create([
            'client_id' => $client->id,
            'invoice_date' => now(),
            'due_date' => now()->addDays(14),
            'currency' => 'USD',
            'status' => 'paid',
            'paid_date' => now(),
            'service_type' => 'virtual_assistant_fee',
            'amount' => 20,
        ]);

        // AUD invoice: an unpaid one must be excluded entirely.
        Invoice::create([
            'client_id' => $client->id,
            'invoice_date' => now(),
            'due_date' => now()->addDays(14),
            'currency' => 'AUD',
            'status' => 'sent',
            'service_type' => 'virtual_assistant_fee',
            'amount' => 1000,
        ]);

        $client->refresh();

        // Naively summing raw amounts would give 10000000 + 20 + 1000 =
        // 10001020 (silently including even the unpaid invoice's raw
        // number if status filtering were also broken). The correct
        // IDR-converted, paid-only total is 10,000,000 + 316,000 = 10,316,000.
        $this->assertEquals(10316000, (float) $client->total_revenue);
    }

    public function test_total_revenue_excludes_unpaid_invoices(): void
    {
        $client = $this->makeClient();

        Invoice::create([
            'client_id' => $client->id,
            'invoice_date' => now(),
            'due_date' => now()->addDays(14),
            'currency' => 'IDR',
            'status' => 'draft',
            'service_type' => 'virtual_assistant_fee',
            'amount' => 5000000,
        ]);

        $client->refresh();

        $this->assertEquals(0, (float) $client->total_revenue);
    }
}
