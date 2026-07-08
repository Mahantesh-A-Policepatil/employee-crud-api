<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\EmployeeDashboardRepository;

class EmployeeDashboardController extends Controller
{
    private EmployeeDashboardRepository $repository;

    public function __construct(EmployeeDashboardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        return response()->json(
            $this->repository->getEmployees()
        );
    }

    public function show($id)
    {
        return response()->json(
            $this->repository->getEmployee($id)
        );
    }
}
