<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use App\Repositories\AttendanceRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\UploadAttendanceCsvRequest;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * AttendanceController
 *
 * Handles CRUD operations for Employee Attendance.
 *
 * @package App\Http\Controllers\API
 */
class AttendanceController extends Controller
{
    /**
     * Attendance Repository instance.
     */
    protected AttendanceRepository $attendanceRepository;

    /**
     * Constructor.
     */
    public function __construct(AttendanceRepository $attendanceRepository)
    {
        $this->attendanceRepository = $attendanceRepository;
    }

    /**
     * Display listing.
     */
    public function index(Request $request): JsonResponse
    {
        $result = $this->attendanceRepository->paginate(

            $request->input('length', 10),

            $request->input('start', 0),

            $request->input('search.value'),

            $request->input('columns.' . $request->input('order.0.column', 0) . '.data', 'id'),

            $request->input('order.0.dir', 'asc')

        );

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $result['total'],
            'recordsFiltered' => $result['filtered'],
            'data' => $result['data'],
        ]);
    }

    /**
     * Store attendance.
     */
    public function store(StoreAttendanceRequest $request): JsonResponse
    {
        if (
            $this->attendanceRepository->exists(
                $request->employee_id,
                $request->attendance_month,
                $request->attendance_year
            )
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance already exists for selected employee and month.'
            ], 422);
        }

        $attendance = $this->attendanceRepository->create(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Attendance created successfully.',
            'data' => $attendance
        ]);
    }

    /**
     * Show attendance.
     */
    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->attendanceRepository->findOrFail($id)
        );
    }

    /**
     * Update attendance.
     */
    public function update(UpdateAttendanceRequest $request, int $id): JsonResponse
    {
        if (
            $this->attendanceRepository->exists(
                $request->employee_id,
                $request->attendance_month,
                $request->attendance_year,
                $id
            )
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance already exists for selected employee and month.'
            ], 422);
        }

        $attendance = $this->attendanceRepository->update(
            $id,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Attendance updated successfully.',
            'data' => $attendance
        ]);
    }

    /**
     * Delete attendance.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->attendanceRepository->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Attendance deleted successfully.'
        ]);
    }

    /**
     * Upload attendance CSV.
     */
    public function uploadCsv(UploadAttendanceCsvRequest $request): JsonResponse
    {
        $result = $this->attendanceRepository->uploadCsv(
            $request->file('file'),
            $request->integer('attendance_month'),
            $request->integer('attendance_year')
        );

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'imported' => $result['imported'],
            'skipped' => $result['skipped'],
            'errors' => $result['errors'],
        ]);
    }

    /**
     * Download the CSV structure accepted by the attendance importer.
     */
    public function downloadTemplate(): StreamedResponse
    {
        return response()->streamDownload(function (): void {
            $output = fopen('php://output', 'w');

            fputcsv($output, [
                'Employee Email',
                'Working Days',
                'Present Days',
                'Leave Days',
                'LOP Days',
                'Remarks',
            ]);
            fputcsv($output, [
                'employee@example.com',
                22,
                20,
                1,
                1,
                'Optional note',
            ]);

            fclose($output);
        }, 'attendance_template.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
