<?php

namespace App\Observers;

use App\Mail\AdminNotificationMail;
use App\Models\Attendance;
use Illuminate\Support\Facades\Mail;

class AttendanceObserver
{
    public function created(Attendance $attendance): void
    {
        $this->sendNotification($attendance, 'created');
    }

    public function updated(Attendance $attendance): void
    {
        $this->sendNotification($attendance, 'updated');
    }

    public function deleted(Attendance $attendance): void
    {
        $this->sendNotification($attendance, 'deleted');
    }

    private function sendNotification(Attendance $attendance, string $action): void
    {
        $employeeName = $attendance->employee?->name ?? "Employee #{$attendance->employee_id}";
        $monthName = now()->setMonth($attendance->attendance_month)->format('F');
        $recordName = "{$employeeName} — {$monthName} {$attendance->attendance_year}";

        Mail::to(config('mail.admin_notification_address'))
            ->queue((new AdminNotificationMail('Attendance', $recordName, $action))->afterCommit());
    }
}
