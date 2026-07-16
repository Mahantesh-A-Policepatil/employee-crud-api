<?php

namespace App\Observers;

use App\Models\Employee;
use App\Mail\AdminNotificationMail;
use Illuminate\Support\Facades\Mail;

class EmployeeObserver
{
    public function created(Employee $employee): void
    {
        $this->sendNotification($employee, 'created');
    }

    public function updated(Employee $employee): void
    {
        $this->sendNotification($employee, 'updated');
    }

    public function deleted(Employee $employee): void
    {
        $this->sendNotification($employee, 'deleted');
    }

    private function sendNotification(Employee $employee, string $action): void
    {
        Mail::to(config('mail.admin_notification_address'))
            ->queue((new AdminNotificationMail('Employee', $employee->name, $action))->afterCommit());
    }
}
