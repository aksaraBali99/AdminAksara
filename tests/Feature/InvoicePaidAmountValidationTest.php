<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Income;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoicePaidAmountValidationTest extends TestCase
{
    use RefreshDatabase;

    private function baseInvoicePayload(array $overrides = []): array
    {
        $client = Client::create([
            'company_name' => 'Acme Corp',
            'contact_name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        return array_merge([
            'client_id' => $client->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(14)->toDateString(),
            'status' => 'paid',
            'currency' => 'IDR',
            'items' => [
                [
                    'type_of_fees' => 'virtual_assistant_fee',
                    'description' => 'VA services',
                    'item_hours' => 1,
                    'rate' => 1000000,
                    'total' => 1000000,
                    'sort_order' => 0,
                ],
            ],
            'paid_date' => now()->toDateString(),
        ], $overrides);
    }

    public function test_paid_amount_wildly_above_invoice_total_is_rejected(): void
    {
        $user = User::factory()->create();

        $payload = $this->baseInvoicePayload([
            // Invoice total is 1,000,000 - this is a 100x typo.
            'paid_amount_idr' => 100000000,
        ]);

        $response = $this->actingAs($user)->post(route('invoices.store'), $payload);

        $response->assertSessionHasErrors('paid_amount_idr');
        $this->assertDatabaseCount('invoices', 0);
    }

    public function test_paid_amount_matching_invoice_total_is_accepted(): void
    {
        $user = User::factory()->create();

        $payload = $this->baseInvoicePayload([
            'paid_amount_idr' => 1000000,
        ]);

        $response = $this->actingAs($user)->post(route('invoices.store'), $payload);

        $response->assertSessionDoesntHaveErrors();
        $this->assertDatabaseCount('invoices', 1);

        $invoice = Invoice::first();
        $this->assertEquals(1000000, (float) $invoice->paid_amount_idr);

        // The Invoice model's boot() hook should have synced a matching income row.
        $income = Income::where('invoice_id', $invoice->id)->first();
        $this->assertNotNull($income);
        $this->assertEquals(1000000, (float) $income->amount);
    }
}
