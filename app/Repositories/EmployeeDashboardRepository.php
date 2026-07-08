<?php

namespace App\Repositories;

use App\Models\Employee;

class EmployeeDashboardRepository
{
    public function getEmployees()
    {
        return Employee::query()
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('projects', 'employees.project_id', '=', 'projects.id')
            ->select(
                'employees.id',
                'employees.name',
                'employees.email',
                'employees.phone',
                'employees.designation',
                'employees.skills',
                'employees.profile_pic',
                'employees.date_of_birth',
                'employees.date_of_joining',
                'employees.total_years_of_experience',
                'departments.name as department_name',
                'projects.name as project_name'
            )
            ->orderBy('employees.name')
            ->get();
    }

    public function getEmployee($id)
    {
        return Employee::query()
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('projects', 'employees.project_id', '=', 'projects.id')
            ->select(
                'employees.*',
                'departments.name as department_name',
                'projects.name as project_name'
            )
            ->findOrFail($id);
    }
}
