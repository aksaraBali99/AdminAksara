<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Income;
use App\Models\Invoice;
use App\Models\Setting;
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

    /**
     * Regression test for the "mark as paid" quick-status form on the
     * invoice show page: an AUD 20 invoice (~ IDR 210,000 at the seeded
     * rate) marked paid with paid_amount_idr = 2,000,000,000 must be
     * rejected with a visible error, not silently dropped.
     */
    public function test_update_status_rejects_wildly_disproportionate_paid_amount(): void
    {
        Setting::create(['key' => 'exchange_rate_aud', 'value' => '10500', 'group' => 'finance']);

        $user = User::factory()->create();
        $client = Client::create([
            'company_name' => 'Acme Corp',
            'contact_name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);
        $invoice = Invoice::create([
            'client_id' => $client->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(14)->toDateString(),
            'currency' => 'AUD',
            'status' => 'sent',
            'service_type' => 'virtual_assistant_fee',
            'amount' => 20,
        ]);

        $response = $this->actingAs($user)->from(route('invoices.show', $invoice))->post(
            route('invoices.status', $invoice),
            [
                'status' => 'paid',
                'paid_amount_idr' => 2000000000,
                'paid_date' => now()->toDateString(),
            ]
        );

        $response->assertSessionHasErrors('paid_amount_idr');
        $response->assertRedirect(route('invoices.show', $invoice));

        $invoice->refresh();
        $this->assertEquals('sent', $invoice->status);
        $this->assertNull($invoice->paid_amount_idr);
        $this->assertDatabaseCount('incomes', 0);

        // The error must actually be visible: the "paid" fields wrapper
        // (which contains the @error message) has to render un-hidden even
        // though the invoice's saved status is still "sent", otherwise the
        // message sits in the DOM behind display:none - which was the bug.
        $page = $this->actingAs($user)->get(route('invoices.show', $invoice));
        $page->assertSee('too far off from the invoice total');
        $page->assertDontSee('id="paid_form_fields" class="hidden', false);
    }
}
