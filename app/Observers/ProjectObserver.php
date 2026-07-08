<?php

namespace App\Observers;

use App\Models\Project;
use App\Mail\AdminNotificationMail;
use Illuminate\Support\Facades\Mail;

class ProjectObserver
{
    public function created(Project $project)
    {
        Mail::to(config('mail.admin_notification_address'))
            ->queue((new AdminNotificationMail('Project', $project->name))->afterCommit());
    }
}
