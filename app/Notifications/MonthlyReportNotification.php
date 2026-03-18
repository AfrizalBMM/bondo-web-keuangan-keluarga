<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MonthlyReportNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
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
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
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

    public function toWebPush($notifiable, $notification)
    {
        $lastMonth = now()->subMonth();
        $monthName = $lastMonth->translatedFormat('F Y');
        
        // Kita akan kirim data total dari Command nanti
        $totalExp = number_format($this->data['expense'], 0, ',', '.');
        $diff = $this->data['diff'];
        $status = $diff >= 0 ? "Surplus (Aman) ✅" : "Defisit (Boros) ⚠️";

        return (new WebPushMessage)
            ->title("📊 Laporan Keuangan $monthName")
            ->icon('/icons/icon-192x192.png')
            ->body("Total Belanja: Rp $totalExp. Status: $status")
            ->data(['url' => url('/reports')]) // Klik untuk lihat grafik detail
            ->options(['TTL' => 86400]);
    }

}
