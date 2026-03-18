<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Bell, AlertTriangle, AlertCircle, CheckCircle2, TrendingUp, Info, Check, BellRing } from 'lucide-vue-next';
import axios from 'axios';

const props = defineProps({
    notifications: Array
});

const getIcon = (type) => {
    switch(type) {
        case 'danger': return AlertTriangle;
        case 'warning': return AlertCircle;
        case 'success': return CheckCircle2;
        case 'info': return Info;
        default: return Bell;
    }
};

const getIconColorClass = (type) => {
    switch(type) {
        case 'danger': return 'bg-rose-50 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400';
        case 'warning': return 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400';
        case 'success': return 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400';
        case 'info': return 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400';
        default: return 'bg-slate-50 text-slate-600 dark:bg-slate-900/30 dark:text-slate-400';
    }
};

const markAllAsRead = () => {
    import('@inertiajs/vue3').then(({ router }) => {
        router.post(route('notifications.mark-read'), {}, {
            preserveScroll: true
        });
    });
};

const isSubscribing = ref(false);

const urlB64ToUint8Array = (base64String) => {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
};

const enablePushNotifications = async () => {
    isSubscribing.value = true;
    try {
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
            alert('Browser ini tidak mendukung Push Notification.');
            return;
        }

        const permission = await Notification.requestPermission();
        if (permission !== 'granted') {
            alert('Izin notifikasi ditolak oleh pengguna.');
            return;
        }

        const sw = await navigator.serviceWorker.register('/sw.js');
        const vapidPublicKey = import.meta.env.VITE_VAPID_PUBLIC_KEY;
        
        const subscription = await sw.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlB64ToUint8Array(vapidPublicKey)
        });

        await axios.post('/push-subscriptions', subscription);
        alert('Notifikasi Push Berhasil Diaktifkan!');
    } catch (error) {
        console.error('Error enabling push notifications:', error);
        alert('Gagal mengaktifkan notifikasi: ' + error.message);
    } finally {
        isSubscribing.value = false;
    }
};

</script>

<template>
    <AuthenticatedLayout>
        <Head title="Pusat Notifikasi" />

        <div class="py-12">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 space-y-6">
                
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900 dark:text-slate-50">Pusat Notifikasi</h1>
                        <p class="mt-1 text-slate-500 dark:text-slate-400">Riwayat sinkronisasi aktivitas pasangan, pengingat, dan peringatan.</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button @click="enablePushNotifications" :disabled="isSubscribing" class="flex items-center gap-2 text-sm font-medium px-4 py-2 bg-royal-50 text-royal-600 hover:bg-royal-100 dark:bg-royal-900/30 dark:text-royal-400 dark:hover:bg-royal-900/50 rounded-lg transition-colors disabled:opacity-50">
                            <BellRing class="w-4 h-4" />
                            {{ isSubscribing ? 'Mengaktifkan...' : 'Aktifkan Push Notifikasi' }}
                        </button>
                        <button @click="markAllAsRead" class="flex justify-center items-center gap-2 text-sm font-medium text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200 px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-lg">
                            <Check class="w-4 h-4" />
                            Tandai Dibaca
                        </button>
                    </div>
                </div>

                <!-- Notifications List -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden divide-y divide-slate-100 dark:divide-slate-700/50">
                    <div v-for="notif in props.notifications" :key="notif.id" 
                        :class="['p-5 transition-colors flex gap-4', notif.read ? 'opacity-70 bg-transparent' : 'bg-slate-50/50 dark:bg-slate-900/30 border-l-4 border-l-royal-500']">
                        
                        <div :class="['w-12 h-12 rounded-full flex-shrink-0 flex items-center justify-center mt-1', getIconColorClass(notif.type)]">
                            <component :is="getIcon(notif.type)" class="w-6 h-6" />
                        </div>
                        
                        <div class="flex-grow">
                            <div class="flex justify-between items-start mb-1">
                                <h3 :class="['font-semibold text-lg', notif.read ? 'text-slate-700 dark:text-slate-300' : 'text-slate-900 dark:text-slate-100']">
                                    {{ notif.title }}
                                </h3>
                                <span class="text-xs text-slate-500 dark:text-slate-400 whitespace-nowrap ml-4">{{ notif.time }}</span>
                            </div>
                            <p :class="['text-sm', notif.read ? 'text-slate-500 dark:text-slate-400' : 'text-slate-700 dark:text-slate-300 font-medium']">
                                {{ notif.message }}
                            </p>
                        </div>

                    </div>
                    
                    <div v-if="!props.notifications || props.notifications.length === 0" class="p-12 text-center text-slate-500 dark:text-slate-400">
                        <Bell class="w-12 h-12 mx-auto mb-3 opacity-50" />
                        <p>Belum ada notifikasi baru.</p>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
