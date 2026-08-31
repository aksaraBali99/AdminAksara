<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;

class AuditPaidInvoiceAmounts extends Command
{
    /**
     * Same tolerance band as InvoiceController's sanity check: a paid
     * amount this far off the invoice's own total is almost certainly a
     * typo, not a legitimate partial payment or FX swing.
     */
    private const MIN_RATIO = 0.3;
    private const MAX_RATIO = 3.0;

    protected $signature = 'invoices:audit-paid-amounts {--fix : Correct flagged invoices by resetting paid_amount_idr to the amount converted at the current exchange rate}';

    protected $description = 'Find paid invoices whose paid_amount_idr looks inconsistent with the invoice amount (e.g. extra-digit typos)';

    public function handle(): int
    {
        $invoices = Invoice::where('status', 'paid')
            ->whereNotNull('paid_amount_idr')
            ->get();

        $flagged = $invoices->filter(function (Invoice $invoice) {
            $expectedIdr = Invoice::convertToIdr((float) $invoice->amount, $invoice->currency);

            if ($expectedIdr <= 0) {
                return false;
            }

            $ratio = (float) $invoice->paid_amount_idr / $expectedIdr;

            return $ratio < self::MIN_RATIO || $ratio > self::MAX_RATIO;
        });

        if ($flagged->isEmpty()) {
            $this->info('No suspicious paid_amount_idr values found.');

            return self::SUCCESS;
        }

        $rows = $flagged->map(function (Invoice $invoice) {
            $expectedIdr = Invoice::convertToIdr((float) $invoice->amount, $invoice->currency);

            return [
                $invoice->id,
                $invoice->invoice_number,
                $invoice->currency,
                number_format((float) $invoice->amount, 2),
                number_format((float) $invoice->paid_amount_idr, 2),
                number_format($expectedIdr, 2),
                number_format((float) $invoice->paid_amount_idr / max($expectedIdr, 0.01), 2) . 'x',
            ];
        });

        $this->table(
            ['ID', 'Invoice #', 'Currency', 'Amount', 'paid_amount_idr', 'Expected (current rate)', 'Ratio'],
            $rows
        );

        if (! $this->option('fix')) {
            $this->warn("Found {$flagged->count()} suspicious invoice(s). Re-run with --fix to correct them.");

            return self::FAILURE;
        }

        foreach ($flagged as $invoice) {
            $correctedIdr = round(Invoice::convertToIdr((float) $invoice->amount, $invoice->currency), 2);

            // Saving through Eloquent (not a raw query) so Invoice's
            // updated() model event keeps the matching incomes row in sync.
            $invoice->paid_amount_idr = $correctedIdr;
            $invoice->save();

            $this->line("Corrected invoice #{$invoice->invoice_number}: paid_amount_idr -> {$correctedIdr}");
        }

        $this->info("Fixed {$flagged->count()} invoice(s).");

        return self::SUCCESS;
    }
}
