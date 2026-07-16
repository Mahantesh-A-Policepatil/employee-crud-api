<?php

namespace App\Observers;

use App\Models\Department;
use App\Mail\AdminNotificationMail;
use Illuminate\Support\Facades\Mail;

class DepartmentObserver
{
    public function created(Department $department): void
    {
        $this->sendNotification($department, 'created');
    }

    public function updated(Department $department): void
    {
        $this->sendNotification($department, 'updated');
    }

    public function deleted(Department $department): void
    {
        $this->sendNotification($department, 'deleted');
    }

    private function sendNotification(Department $department, string $action): void
    {
        Mail::to(config('mail.admin_notification_address'))
            ->queue((new AdminNotificationMail('Department', $department->name, $action))->afterCommit());
    }
}
