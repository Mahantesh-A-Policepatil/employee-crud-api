<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Repositories\ProjectRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct(private ProjectRepository $projectRepository)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $columns = [null, 'id', 'name', 'description', 'employees_count'];
        $columnIndex = (int) $request->input('order.0.column', 0);
        $orderColumn = $columns[$columnIndex] ?? 'id';
        $orderColumn = $orderColumn === 'employees_count' ? 'employees_count' : $orderColumn;

        $result = $this->projectRepository->paginate(
            (int) $request->input('length', 10),
            (int) $request->input('start', 0),
            $request->input('search.value'),
            $orderColumn,
            $request->input('order.0.dir', 'asc')
        );

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $result['total'],
            'recordsFiltered' => $result['filtered'],
            'data' => $result['data'],
        ]);
    }

    public function options(): JsonResponse
    {
        return response()->json($this->projectRepository->options());
    }

    public function store(StoreProjectRequest $request)
    {
        return $this->projectRepository->createWithEmployees($request->validated());
    }

    public function show($id)
    {
        return $this->projectRepository->findWithEmployees($id);
    }

    public function update(UpdateProjectRequest $request, $id)
    {
        return $this->projectRepository->updateWithEmployees($id, $request->validated());
    }

    public function destroy($id): JsonResponse
    {
        $this->projectRepository->delete($id);

        return response()->json(['message' => 'Deleted']);
    }
}
