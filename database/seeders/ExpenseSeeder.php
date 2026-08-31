<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ExpenseCategory::all()->keyBy('name');
        
        $expenses = [
            [
                'category_id' => $categories['Software & Tools']->id ?? null,
                'description' => 'Google Workspace Subscription',
                'amount' => 500000,
                'currency' => 'IDR',
                'expense_date' => Carbon::now()->subDays(25),
            ],
            [
                'category_id' => $categories['Software & Tools']->id ?? null,
                'description' => 'Slack Pro Plan',
                'amount' => 350000,
                'currency' => 'IDR',
                'expense_date' => Carbon::now()->subDays(20),
            ],
            [
                'category_id' => $categories['Utilities']->id ?? null,
                'description' => 'Internet Bill - December',
                'amount' => 850000,
                'currency' => 'IDR',
                'expense_date' => Carbon::now()->subDays(15),
            ],
            [
                'category_id' => $categories['Marketing']->id ?? null,
                'description' => 'Facebook Ads Campaign',
                'amount' => 2000000,
                'currency' => 'IDR',
                'expense_date' => Carbon::now()->subDays(10),
            ],
            [
                'category_id' => $categories['Office Supplies']->id ?? null,
                'description' => 'Stationery & Printing',
                'amount' => 250000,
                'currency' => 'IDR',
                'expense_date' => Carbon::now()->subDays(5),
            ],
        ];

        foreach ($expenses as $expense) {
            Expense::create($expense);
        }
    }
}
