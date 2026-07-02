<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Project;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        $departments = collect([
            ['name' => 'Engineering', 'description' => 'Product and software development'],
            ['name' => 'Human Resources', 'description' => 'People operations and recruitment'],
            ['name' => 'Quality Assurance', 'description' => 'Testing and release quality'],
            ['name' => 'Operations', 'description' => 'Business operations and support'],
        ])->mapWithKeys(function ($department) {
            $model = Department::firstOrCreate(
                ['name' => $department['name']],
                ['description' => $department['description']]
            );

            return [$model->name => $model];
        });

        $projects = Project::pluck('id', 'name');

        $employees = [
            ['Aarav Sharma', 'aarav.sharma@example.com', '9876500001', 'Senior Software Engineer', 'Engineering', 'Employee Management System'],
            ['Meera Iyer', 'meera.iyer@example.com', '9876500002', 'Frontend Developer', 'Engineering', 'Hotel Management System'],
            ['Rohan Desai', 'rohan.desai@example.com', '9876500003', 'Backend Developer', 'Engineering', 'Hospital Management System'],
            ['Ananya Rao', 'ananya.rao@example.com', '9876500004', 'DevOps Engineer', 'Engineering', 'Air Ticket Reservation System'],
            ['Vikram Singh', 'vikram.singh@example.com', '9876500005', 'Engineering Manager', 'Engineering', 'Employee Management System'],
            ['Priya Nair', 'priya.nair@example.com', '9876500006', 'HR Manager', 'Human Resources', 'Hotel Management System'],
            ['Neha Kapoor', 'neha.kapoor@example.com', '9876500007', 'Talent Acquisition Specialist', 'Human Resources', 'Hospital Management System'],
            ['Karan Patel', 'karan.patel@example.com', '9876500008', 'People Operations Executive', 'Human Resources', 'Air Ticket Reservation System'],
            ['Ishita Mehta', 'ishita.mehta@example.com', '9876500009', 'QA Lead', 'Quality Assurance', 'Employee Management System'],
            ['Arjun Verma', 'arjun.verma@example.com', '9876500010', 'Automation Test Engineer', 'Quality Assurance', 'Hotel Management System'],
            ['Sneha Kulkarni', 'sneha.kulkarni@example.com', '9876500011', 'Quality Analyst', 'Quality Assurance', 'Hospital Management System'],
            ['Rahul Joshi', 'rahul.joshi@example.com', '9876500012', 'Performance Test Engineer', 'Quality Assurance', 'Air Ticket Reservation System'],
            ['Kavya Reddy', 'kavya.reddy@example.com', '9876500013', 'Operations Manager', 'Operations', 'Employee Management System'],
            ['Aditya Menon', 'aditya.menon@example.com', '9876500014', 'Business Operations Analyst', 'Operations', 'Hotel Management System'],
            ['Pooja Shah', 'pooja.shah@example.com', '9876500015', 'Customer Support Lead', 'Operations', 'Hospital Management System'],
            ['Nikhil Bhat', 'nikhil.bhat@example.com', '9876500016', 'Systems Administrator', 'Operations', 'Air Ticket Reservation System'],
        ];

        collect($employees)->each(function ($employee) use ($departments, $projects) {
            [$name, $email, $phone, $designation, $department, $project] = $employee;

            Employee::updateOrCreate(
                ['email' => $email],
                [
                    'department_id' => $departments[$department]->id,
                    'project_id' => $projects[$project] ?? null,
                    'name' => $name,
                    'phone' => $phone,
                    'designation' => $designation,
                ]
            );
        });
    }
}
