<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Operational', 'description' => 'Day-to-day operational expenses'],
            ['name' => 'Salary & Wages', 'description' => 'Employee salaries and wages'],
            ['name' => 'Marketing', 'description' => 'Marketing and advertising expenses'],
            ['name' => 'Software & Tools', 'description' => 'Software subscriptions and tools'],
            ['name' => 'Hardware & Equipment', 'description' => 'Computer and office equipment'],
            ['name' => 'Training & Development', 'description' => 'Employee training and courses'],
            ['name' => 'Travel', 'description' => 'Business travel expenses'],
            ['name' => 'Office Supplies', 'description' => 'Office consumables and supplies'],
            ['name' => 'Utilities', 'description' => 'Internet, phone, electricity'],
            ['name' => 'Miscellaneous', 'description' => 'Other uncategorized expenses'],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
