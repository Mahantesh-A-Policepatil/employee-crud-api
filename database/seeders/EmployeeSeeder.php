<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Employee;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        $departments = collect([
            ['name' => 'Engineering', 'description' => 'Product and software development'],
            ['name' => 'Human Resources', 'description' => 'People operations and recruitment'],
            ['name' => 'Quality Assurance', 'description' => 'Testing and release quality'],
            ['name' => 'Operations', 'description' => 'Business operations and support'],
        ])->map(fn ($department) => Department::create($department));

        for ($i = 1; $i <= 100; $i++) {
            $department = $departments->random();

            Employee::create([
                'department_id' => $department->id,
                'name' => "Employee $i",
                'email' => "employee$i@example.com",
                'phone' => "98765" . str_pad($i, 5, '0', STR_PAD_LEFT),
                'designation' => ['Developer', 'Manager', 'Tester', 'HR'][rand(0, 3)],
            ]);
        }
    }
}
