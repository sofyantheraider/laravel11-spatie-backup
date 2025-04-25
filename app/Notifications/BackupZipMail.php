<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class BackupZipMail extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */

    protected string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    

    public function toMail($notifiable)
    {
        $filename = basename($this->filePath);
        $fullPath = Storage::disk('backups')->path($this->filePath);

        return (new MailMessage)
            ->subject('🎉 Backup Laravel Berhasil')
            ->line('Backup harian Laravel berhasil dibuat. File terlampir.')
            ->attach($fullPath, [
                'as' => $filename,
                'mime' => 'application/zip',
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
