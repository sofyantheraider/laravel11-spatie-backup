<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use App\Notifications\BackupZipMail;
use Illuminate\Notifications\Notifiable;

class BackupAndSendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    use Notifiable;

    protected $signature = 'backup:run-and-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Laravel backup and email the zip file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Menjalankan backup...');
        Artisan::call('backup:run');

        $files = Storage::disk('backups')->files();
        $zipFiles = collect($files)->filter(fn($f) => str_ends_with($f, '.zip'));

        if ($zipFiles->isEmpty()) {
            $this->error('Tidak ada file backup ditemukan.');
            return 1;
        }

        $latestFile = $zipFiles->sortByDesc(fn($f) => Storage::disk('backups')->lastModified($f))->first();

        $this->info("File backup ditemukan: $latestFile");
        Notification::route('mail', 'mccpas99@gmail.com') // ganti dengan email tujuan
            ->notify(new BackupZipMail($latestFile));

        $this->info('Email berhasil dikirim!');
        return 0;
    }
}
