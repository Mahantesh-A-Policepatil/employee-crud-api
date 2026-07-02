<?php

namespace App\Repositories;

use App\Models\Employee;
use App\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProjectRepository extends BaseRepository
{
    protected function getModel(): Model
    {
        return app(Project::class);
    }

    public function paginate(
        int $length,
        int $start,
        ?string $search = null,
        string $orderColumn = 'id',
        string $orderDir = 'asc'
    ): array {
        $query = $this->query()
            ->with(['employees.department'])
            ->withCount('employees');

        if ($search) {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('employees', function ($employeeQuery) use ($search) {
                        $employeeQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $total = $this->model->count();
        $filtered = (clone $query)->count();

        return [
            'total' => $total,
            'filtered' => $filtered,
            'data' => $query->orderBy($orderColumn, $orderDir)
                ->offset($start)
                ->limit($length)
                ->get(),
        ];
    }

    public function options()
    {
        return $this->query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Project $project) => [
                'value' => $project->id,
                'label' => $project->name,
            ]);
    }

    public function createWithEmployees(array $attributes): Project
    {
        return DB::transaction(function () use ($attributes) {
            $employeeIds = $attributes['employee_ids'] ?? [];
            unset($attributes['employee_ids']);

            /** @var Project $project */
            $project = $this->create($attributes);
            $this->syncEmployees($project, $employeeIds);

            return $this->findWithEmployees($project->id);
        });
    }

    public function updateWithEmployees($id, array $attributes): Project
    {
        return DB::transaction(function () use ($id, $attributes) {
            $shouldSyncEmployees = array_key_exists('employee_ids', $attributes);
            $employeeIds = $attributes['employee_ids'] ?? [];
            unset($attributes['employee_ids']);

            /** @var Project $project */
            $project = $this->update($id, $attributes);
            if ($shouldSyncEmployees) {
                $this->syncEmployees($project, $employeeIds);
            }

            return $this->findWithEmployees($project->id);
        });
    }

    public function findWithEmployees($id): Project
    {
        /** @var Project $project */
        $project = $this->query()->with(['employees.department'])->findOrFail($id);
        $project->setAttribute('employee_ids', $project->employees->pluck('id')->values());

        return $project;
    }

    private function syncEmployees(Project $project, array $employeeIds): void
    {
        Employee::where('project_id', $project->id)
            ->when(
                count($employeeIds) > 0,
                fn ($query) => $query->whereNotIn('id', $employeeIds)
            )
            ->update(['project_id' => null]);

        if (count($employeeIds) > 0) {
            Employee::whereIn('id', $employeeIds)
                ->update(['project_id' => $project->id]);
        }
    }
}
