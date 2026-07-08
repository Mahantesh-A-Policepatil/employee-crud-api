<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Project;
use App\Observers\UserObserver;
use App\Observers\EmployeeObserver;
use App\Observers\DepartmentObserver;
use App\Observers\ProjectObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        User::observe(UserObserver::class);
        Employee::observe(EmployeeObserver::class);
        Department::observe(DepartmentObserver::class);
        Project::observe(ProjectObserver::class);
    }
}
