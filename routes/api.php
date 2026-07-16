<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\DepartmentController;
use App\Http\Controllers\API\PermissionController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\UserRoleController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\EmployeeDashboardController;
use App\Http\Controllers\API\AttendanceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Employee Dashboard (Public for Development)
|--------------------------------------------------------------------------
|
| Remove these two routes later and move them inside auth:sanctum
| once React authentication is integrated.
|
*/

Route::get('/employee-dashboard', [EmployeeDashboardController::class, 'index']);
Route::get('/employee-dashboard/{id}', [EmployeeDashboardController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user', [AuthController::class, 'updateUser']);

    /*
    |--------------------------------------------------------------------------
    | Employees
    |--------------------------------------------------------------------------
    */

    Route::get('/employees', [EmployeeController::class, 'index'])->middleware('permission:employees.view');
    Route::get('/employees/options', [EmployeeController::class, 'options'])->middleware('permission:projects.create|projects.update');
    Route::get('/employees/department-options', [EmployeeController::class, 'departmentOptions'])->middleware('permission:departments.create|departments.update');
    Route::post('/employees', [EmployeeController::class, 'store'])->middleware('permission:employees.create');
    Route::get('/employees/{id}', [EmployeeController::class, 'show'])->middleware('permission:employees.view');
    Route::put('/employees/{id}', [EmployeeController::class, 'update'])->middleware('permission:employees.update');
    Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->middleware('permission:employees.delete');

    /*
    |--------------------------------------------------------------------------
    | Projects
    |--------------------------------------------------------------------------
    */

    Route::get('/projects/options', [ProjectController::class, 'options'])->middleware('permission:projects.view|employees.view');
    Route::get('/projects', [ProjectController::class, 'index'])->middleware('permission:projects.view');
    Route::post('/projects', [ProjectController::class, 'store'])->middleware('permission:projects.create');
    Route::get('/projects/{id}', [ProjectController::class, 'show'])->middleware('permission:projects.view');
    Route::put('/projects/{id}', [ProjectController::class, 'update'])->middleware('permission:projects.update');
    Route::delete('/projects/{id}', [ProjectController::class, 'destroy'])->middleware('permission:projects.delete');

    /*
    |--------------------------------------------------------------------------
    | Departments
    |--------------------------------------------------------------------------
    */

    Route::get('/departments/options', [DepartmentController::class, 'options'])->middleware('permission:departments.view|employees.view');
    Route::get('/departments', [DepartmentController::class, 'index'])->middleware('permission:departments.view');
    Route::post('/departments', [DepartmentController::class, 'store'])->middleware('permission:departments.create');
    Route::get('/departments/{id}', [DepartmentController::class, 'show'])->middleware('permission:departments.view');
    Route::put('/departments/{id}', [DepartmentController::class, 'update'])->middleware('permission:departments.update');
    Route::delete('/departments/{id}', [DepartmentController::class, 'destroy'])->middleware('permission:departments.delete');

    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    */

    Route::get('/permissions/options', [PermissionController::class, 'options'])->middleware('permission:permissions.view|roles.manage');
    Route::get('/permissions/grouped-options', [PermissionController::class, 'groupedOptions'])->middleware('permission:roles.manage');
    Route::get('/permissions', [PermissionController::class, 'index'])->middleware('permission:permissions.view');
    Route::post('/permissions', [PermissionController::class, 'store'])->middleware('permission:permissions.manage');
    Route::get('/permissions/{id}', [PermissionController::class, 'show'])->middleware('permission:permissions.view');
    Route::put('/permissions/{id}', [PermissionController::class, 'update'])->middleware('permission:permissions.manage');
    Route::delete('/permissions/{id}', [PermissionController::class, 'destroy'])->middleware('permission:permissions.manage');

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    */

    Route::get('/roles', [RoleController::class, 'index'])->middleware('permission:roles.view');
    Route::post('/roles', [RoleController::class, 'store'])->middleware('permission:roles.manage');
    Route::get('/roles/{id}', [RoleController::class, 'show'])->middleware('permission:roles.view');
    Route::put('/roles/{id}', [RoleController::class, 'update'])->middleware('permission:roles.manage');
    Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->middleware('permission:roles.manage');

    /*
    |--------------------------------------------------------------------------
    | User Roles
    |--------------------------------------------------------------------------
    */

    Route::get('/user-options', [UserRoleController::class, 'userOptions'])->middleware('permission:roles.manage');
    Route::get('/role-options', [UserRoleController::class, 'roleOptions'])->middleware('permission:roles.manage');
    Route::get('/user-roles', [UserRoleController::class, 'index'])->middleware('permission:roles.manage');
    Route::post('/user-roles', [UserRoleController::class, 'store'])->middleware('permission:roles.manage');
    Route::get('/user-roles/{id}', [UserRoleController::class, 'show'])->middleware('permission:roles.manage');
    Route::put('/user-roles/{id}', [UserRoleController::class, 'update'])->middleware('permission:roles.manage');
    Route::delete('/user-roles/{id}', [UserRoleController::class, 'destroy'])->middleware('permission:roles.manage');

    /*
    |--------------------------------------------------------------------------
    | Attendance
    |--------------------------------------------------------------------------
    */

    Route::get('/attendance', [AttendanceController::class, 'index'])->middleware('permission:attendance.view');
    Route::post('/attendance', [AttendanceController::class, 'store'])->middleware('permission:attendance.create');
    Route::post('/attendance/upload', [AttendanceController::class, 'uploadCsv'])->middleware('permission:attendance.create');
    Route::get('/attendance/template/download', [AttendanceController::class, 'downloadTemplate'])->middleware('permission:attendance.view');
    Route::get('/attendance/{id}', [AttendanceController::class, 'show'])->middleware('permission:attendance.view');
    Route::put('/attendance/{id}', [AttendanceController::class, 'update'])->middleware('permission:attendance.update');
    Route::delete('/attendance/{id}', [AttendanceController::class, 'destroy'])->middleware('permission:attendance.delete');
});
