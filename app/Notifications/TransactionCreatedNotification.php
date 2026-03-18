<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class TransactionCreatedNotification extends Notification
{
    use Queueable;

    protected $transaction;

    /**
     * Simpan data transaksi ke dalam property agar bisa diakses di semua method.
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Tentukan channel apa yang digunakan (WebPush).
     */
    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    /**
     * Susun pesan notifikasi untuk Browser/HP.
     */
    public function toWebPush($notifiable, $notification)
    {
        $userName = $this->transaction->user->name;
        $amount = number_format($this->transaction->amount, 0, ',', '.');
        $type = $this->transaction->type === 'Income' ? 'Pemasukan' : 'Pengeluaran';
        $category = $this->transaction->category ? $this->transaction->category->name : 'Lain-lain';

        return (new WebPushMessage)
            ->title("💰 $type Baru!")
            ->icon('/icons/icon-192x192.png') // Pastikan file ini ada di public/icons/
            ->badge('/icons/icon-192x192.png') // Ikon kecil di status bar Android
            ->body("{$userName} baru saja mencatat {$type}: {$category} sebesar Rp {$amount}")
            ->action('Lihat Detail', 'view_transaction')
            ->data([
                'url' => url('/transactions'), // Halaman yang dibuka saat diklik
                'id'  => $this->transaction->id
            ])
            ->options(['TTL' => 1000]); // Time To Live (durasi simpan di server push)
    }
}