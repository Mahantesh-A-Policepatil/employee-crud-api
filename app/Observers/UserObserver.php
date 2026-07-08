<?php

namespace App\Observers;

use App\Models\User;
use App\Mail\AdminNotificationMail;
use Illuminate\Support\Facades\Mail;

class UserObserver
{
    public function created(User $user)
    {
        Mail::to('mahantesh_policepatil@yahoo.in')
            ->send(new AdminNotificationMail('User', $user->name));
    }
}
