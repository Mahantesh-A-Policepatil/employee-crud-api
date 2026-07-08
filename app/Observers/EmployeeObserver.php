<?php

namespace App\Observers;

use App\Models\Employee;
use App\Mail\AdminNotificationMail;
use Illuminate\Support\Facades\Mail;

class EmployeeObserver
{
    public function created(Employee $employee)
    {
        Mail::to(config('mail.admin_notification_address'))
            ->queue((new AdminNotificationMail('Employee', $employee->first_name . ' ' . $employee->last_name))->afterCommit());
    }
}
