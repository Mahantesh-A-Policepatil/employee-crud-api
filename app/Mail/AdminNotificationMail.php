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
    public $action;

    /**
     * Create a new message instance.
     */
    public function __construct(string $entityType, string $entityName, string $action = 'created')
    {
        $this->entityType = $entityType;
        $this->entityName = $entityName;
        $this->action = $action;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $entityType = e($this->entityType);
        $entityName = e($this->entityName);
        $action = e($this->action);
        $actionLabel = ucfirst($action);
        $message = match ($this->action) {
            'created' => 'A new record has been added successfully.',
            'updated' => 'An existing record has been updated successfully.',
            'deleted' => 'A record has been removed successfully.',
            default => 'A record has been changed successfully.',
        };

        return $this->subject("{$actionLabel}: {$this->entityType} notification")
            ->html(
                <<<HTML
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="margin: 0; padding: 0; background-color: #f4f7fb; color: #1f2937; font-family: Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; background-color: #f4f7fb; padding: 32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="width: 100%; max-width: 600px; background-color: #ffffff; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 18px rgba(15, 23, 42, 0.08);">
                    <tr>
                        <td style="padding: 28px 36px; background: #1d4ed8; color: #ffffff;">
                            <p style="margin: 0 0 6px; font-size: 13px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; color: #bfdbfe;">Employee Management</p>
                            <h1 style="margin: 0; font-size: 26px; line-height: 34px; font-weight: 700; color: #ffffff;">{$actionLabel} record</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 34px 36px 28px;">
                            <p style="margin: 0 0 16px; font-size: 16px; line-height: 24px; color: #374151;">Hello Admin,</p>
                            <p style="margin: 0 0 24px; font-size: 16px; line-height: 24px; color: #374151;">{$message} Here are the details:</p>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; border: 1px solid #dbeafe; border-radius: 10px; background-color: #eff6ff;">
                                <tr>
                                    <td style="padding: 18px 20px;">
                                        <p style="margin: 0 0 6px; font-size: 12px; line-height: 18px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase; color: #2563eb;">Record type</p>
                                        <p style="margin: 0 0 16px; font-size: 16px; line-height: 22px; font-weight: 700; color: #1e3a8a;">{$entityType}</p>
                                        <p style="margin: 0 0 6px; font-size: 12px; line-height: 18px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase; color: #2563eb;">Action</p>
                                        <p style="margin: 0 0 16px; font-size: 16px; line-height: 22px; font-weight: 700; color: #1e3a8a;">{$actionLabel}</p>
                                        <p style="margin: 0 0 6px; font-size: 12px; line-height: 18px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase; color: #2563eb;">Name</p>
                                        <p style="margin: 0; font-size: 16px; line-height: 22px; font-weight: 700; color: #1e3a8a;">{$entityName}</p>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin: 24px 0 0; font-size: 14px; line-height: 21px; color: #6b7280;">You are receiving this notification because you are an administrator of the Employee Management system.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px 36px; background-color: #f8fafc; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; font-size: 12px; line-height: 18px; color: #94a3b8; text-align: center;">&copy; {$this->getCurrentYear()} Employee Management System. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML
            );
    }

    private function getCurrentYear(): string
    {
        return now()->format('Y');
    }
}
