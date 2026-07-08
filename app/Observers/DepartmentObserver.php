<?php

namespace App\Observers;

use App\Models\Department;
use App\Mail\AdminNotificationMail;
use Illuminate\Support\Facades\Mail;

class DepartmentObserver
{
    public function created(Department $department)
    {
        Mail::to(config('mail.admin_notification_address'))
            ->queue((new AdminNotificationMail('Department', $department->name))->afterCommit());
    }
}
