<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $entityType;
    public $entityName;

    /**
     * Create a new message instance.
     */
    public function __construct(string $entityType, string $entityName)
    {
        $this->entityType = $entityType; // e.g., 'Employee', 'Department'
        $this->entityName = $entityName; // e.g., 'John Doe', 'HR Department'
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject("New {$this->entityType} Created Notification")
                    ->html("<p>Hello Admin,</p><p>A new <strong>{$this->entityType}</strong> has been successfully created: <strong>{$this->entityName}</strong>.</p>");
    }
}
