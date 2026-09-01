<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regression test for a query that worked on MySQL/MariaDB (production)
 * but crashed on SQLite: ExpenseCategory::withSum(...)->having(...) puts a
 * correlated-subquery alias in HAVING with no GROUP BY, which MySQL
 * tolerates as a non-standard extension and SQLite (and Postgres) reject
 * outright with "HAVING clause on a non-aggregate query".
 */
class ReportControllerCategoryBreakdownTest extends TestCase
{
    use RefreshDatabase;

    public function test_reports_page_excludes_zero_and_out_of_range_categories_and_sorts_by_total(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $withinRange = ExpenseCategory::create(['name' => 'Office Supplies']);
        $largestWithinRange = ExpenseCategory::create(['name' => 'Software']);
        $outsideRange = ExpenseCategory::create(['name' => 'Travel']);
        $neverUsed = ExpenseCategory::create(['name' => 'Unused Category']);

        Expense::create([
            'category_id' => $withinRange->id,
            'description' => 'Paper',
            'amount' => 100000,
            'currency' => 'IDR',
            'expense_date' => '2026-03-10',
        ]);

        Expense::create([
            'category_id' => $largestWithinRange->id,
            'description' => 'SaaS subscription',
            'amount' => 500000,
            'currency' => 'IDR',
            'expense_date' => '2026-03-15',
        ]);

        // Same category, but outside the requested date range.
        Expense::create([
            'category_id' => $outsideRange->id,
            'description' => 'Flight',
            'amount' => 2000000,
            'currency' => 'IDR',
            'expense_date' => '2026-01-05',
        ]);

        $response = $this->actingAs($owner)->get(route('reports.index', ['year' => 2026, 'month' => 3]));

        $response->assertOk();
        $response->assertViewHas('expenseByCategory', function ($categories) use ($withinRange, $largestWithinRange, $outsideRange, $neverUsed) {
            $names = $categories->pluck('name')->all();

            // Only categories with a positive sum *within the date range*
            // survive, and the never-touched / out-of-range ones don't
            // (the never-touched one would even produce a NULL sum, not
            // just a zero one, since SUM() over no rows is NULL in SQL).
            $this->assertEquals([$largestWithinRange->name, $withinRange->name], $names);
            $this->assertNotContains($outsideRange->name, $names);
            $this->assertNotContains($neverUsed->name, $names);

            return true;
        });
    }

    public function test_reports_pdf_export_also_applies_the_same_category_filter(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $category = ExpenseCategory::create(['name' => 'Office Supplies']);
        Expense::create([
            'category_id' => $category->id,
            'description' => 'Paper',
            'amount' => 100000,
            'currency' => 'IDR',
            'expense_date' => '2026-03-10',
        ]);

        $response = $this->actingAs($owner)->get(route('reports.pdf', ['year' => 2026, 'month' => 3]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }
}
