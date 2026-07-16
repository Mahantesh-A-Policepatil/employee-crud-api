<?php

namespace App\Observers;

use App\Models\Project;
use App\Mail\AdminNotificationMail;
use Illuminate\Support\Facades\Mail;

class ProjectObserver
{
    public function created(Project $project): void
    {
        $this->sendNotification($project, 'created');
    }

    public function updated(Project $project): void
    {
        $this->sendNotification($project, 'updated');
    }

    public function deleted(Project $project): void
    {
        $this->sendNotification($project, 'deleted');
    }

    private function sendNotification(Project $project, string $action): void
    {
        Mail::to(config('mail.admin_notification_address'))
            ->queue((new AdminNotificationMail('Project', $project->name, $action))->afterCommit());
    }
}
