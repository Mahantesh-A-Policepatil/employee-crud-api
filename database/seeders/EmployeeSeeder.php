<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        for ($i = 1; $i <= 100; $i++) {
            Employee::create([
                'name' => "Employee $i",
                'email' => "employee$i@example.com",
                'phone' => "98765" . str_pad($i, 5, '0', STR_PAD_LEFT),
                'designation' => ['Developer', 'Manager', 'Tester', 'HR'][rand(0, 3)],
            ]);
        }
    }
}