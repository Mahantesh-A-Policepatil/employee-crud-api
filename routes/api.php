<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\DepartmentController;
use App\Http\Controllers\API\PermissionController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\UserRoleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user', [AuthController::class, 'updateUser']);

    Route::get('/employees', [EmployeeController::class, 'index'])->middleware('permission:employees.view');
    Route::post('/employees', [EmployeeController::class, 'store'])->middleware('permission:employees.create');
    Route::get('/employees/{id}', [EmployeeController::class, 'show'])->middleware('permission:employees.view');
    Route::put('/employees/{id}', [EmployeeController::class, 'update'])->middleware('permission:employees.update');
    Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->middleware('permission:employees.delete');

    Route::get('/departments/options', [DepartmentController::class, 'options'])->middleware('permission:departments.view|employees.view');
    Route::get('/departments', [DepartmentController::class, 'index'])->middleware('permission:departments.view');
    Route::post('/departments', [DepartmentController::class, 'store'])->middleware('permission:departments.create');
    Route::get('/departments/{id}', [DepartmentController::class, 'show'])->middleware('permission:departments.view');
    Route::put('/departments/{id}', [DepartmentController::class, 'update'])->middleware('permission:departments.update');
    Route::delete('/departments/{id}', [DepartmentController::class, 'destroy'])->middleware('permission:departments.delete');

    Route::get('/permissions/options', [PermissionController::class, 'options'])->middleware('permission:permissions.view|roles.manage');
    Route::get('/permissions', [PermissionController::class, 'index'])->middleware('permission:permissions.view');
    Route::post('/permissions', [PermissionController::class, 'store'])->middleware('permission:permissions.manage');
    Route::get('/permissions/{id}', [PermissionController::class, 'show'])->middleware('permission:permissions.view');
    Route::put('/permissions/{id}', [PermissionController::class, 'update'])->middleware('permission:permissions.manage');
    Route::delete('/permissions/{id}', [PermissionController::class, 'destroy'])->middleware('permission:permissions.manage');

    Route::get('/roles', [RoleController::class, 'index'])->middleware('permission:roles.view');
    Route::post('/roles', [RoleController::class, 'store'])->middleware('permission:roles.manage');
    Route::get('/roles/{id}', [RoleController::class, 'show'])->middleware('permission:roles.view');
    Route::put('/roles/{id}', [RoleController::class, 'update'])->middleware('permission:roles.manage');
    Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->middleware('permission:roles.manage');

    Route::get('/user-options', [UserRoleController::class, 'userOptions'])->middleware('permission:roles.manage');
    Route::get('/role-options', [UserRoleController::class, 'roleOptions'])->middleware('permission:roles.manage');
    Route::get('/user-roles', [UserRoleController::class, 'index'])->middleware('permission:roles.manage');
    Route::post('/user-roles', [UserRoleController::class, 'store'])->middleware('permission:roles.manage');
    Route::get('/user-roles/{id}', [UserRoleController::class, 'show'])->middleware('permission:roles.manage');
    Route::put('/user-roles/{id}', [UserRoleController::class, 'update'])->middleware('permission:roles.manage');
    Route::delete('/user-roles/{id}', [UserRoleController::class, 'destroy'])->middleware('permission:roles.manage');
});
