<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class BudgetExceededNotification extends Notification
{
    use Queueable;

    public $categoryName;
    public $percentage;

    public function __construct($categoryName, $percentage)
    {
        $this->categoryName = $categoryName;
        $this->percentage = $percentage;
    }

    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('Peringatan Anggaran!')
            ->icon('/favicon.ico')
            ->body('Pengeluaran untuk "' . $this->categoryName . '" telah mencapai ' . $this->percentage . '% dari batas anggaran bulanan.')
            ->action('Lihat Budget', '/budget')
            ->data(['url' => '/budget']);
    }
}
