import { ref } from 'vue';
import axios from 'axios';

export function usePushNotification() {
    const isSubscribed = ref(false);
    const loading = ref(false);

    const subscribeUser = async () => {
        loading.value = true;
        try {
            // 1. Cek Service Worker
            const registration = await navigator.serviceWorker.ready;

            // 2. Minta Izin Browser
            const permission = await Notification.requestPermission();
            if (permission !== 'granted') {
                throw new Error('Izin notifikasi ditolak oleh pengguna.');
            }

            // 3. Subscribe ke Push Manager
            // Ganti 'YOUR_PUBLIC_VAPID_KEY' dengan hasil php artisan webpush:vapid
            const subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: 'YOUR_PUBLIC_VAPID_KEY'
            });

            // 4. Kirim ke Backend (PushNotificationController@store)
            await axios.post(route('push.store'), subscription);

            isSubscribed.value = true;
            console.log('Push notification aktif!');
        } catch (error) {
            console.error('Gagal mengaktifkan push:', error);
        } finally {
            loading.value = false;
        }
    };

    return { subscribeUser, isSubscribed, loading };
}