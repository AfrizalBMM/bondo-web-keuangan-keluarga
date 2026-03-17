<script setup>
import { ref, onMounted } from 'vue';
import { Bell, BellOff, Loader2 } from 'lucide-vue-next';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import axios from 'axios';

const isSupported = ref(false);
const permission = ref('default');
const isSubscribed = ref(false);
const isLoading = ref(false);
const vapidPublicKey = import.meta.env.VITE_VAPID_PUBLIC_KEY;

// Base64 to Uint8Array converter
function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/');

    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

onMounted(async () => {
    isSupported.value = 'serviceWorker' in navigator && 'PushManager' in window;
    if (isSupported.value) {
        permission.value = Notification.permission;
        
        try {
            const registration = await navigator.serviceWorker.register('/sw.js');
            const subscription = await registration.pushManager.getSubscription();
            isSubscribed.value = !!subscription;
        } catch (error) {
            console.error('Service Worker registration failed:', error);
        }
    }
});

const toggleSubscription = async () => {
    if (!isSupported.value || !vapidPublicKey) return;
    
    isLoading.value = true;
    
    try {
        const registration = await navigator.serviceWorker.ready;
        
        if (isSubscribed.value) {
            // Unsubscribe
            const subscription = await registration.pushManager.getSubscription();
            if (subscription) {
                await subscription.unsubscribe();
                
                // Send to backend to remove
                await axios.post(route('push.unsubscribe'), {
                    endpoint: subscription.endpoint
                });
            }
            isSubscribed.value = false;
        } else {
            // Ask for permission if not granted
            if (permission.value !== 'granted') {
                const newPermission = await Notification.requestPermission();
                permission.value = newPermission;
                if (newPermission !== 'granted') {
                    throw new Error('Notification permission denied');
                }
            }
            
            // Subscribe
            const subscribeOptions = {
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(vapidPublicKey)
            };
            
            const subscription = await registration.pushManager.subscribe(subscribeOptions);
            
            // Send to backend
            await axios.post(route('push.subscribe'), subscription.toJSON());
            
            isSubscribed.value = true;
        }
    } catch (error) {
        console.error('Failed to toggle push subscription:', error);
        alert('Gagal mengatur notifikasi: ' + error.message);
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <div v-if="isSupported" class="glass-card p-4 flex items-center justify-between mt-4">
        <div class="flex items-center gap-3">
            <div :class="['w-10 h-10 rounded-full flex items-center justify-center text-white', isSubscribed ? 'bg-royal-600' : 'bg-slate-300 dark:bg-slate-700']">
                <Bell v-if="isSubscribed" class="w-5 h-5" />
                <BellOff v-else class="w-5 h-5 text-slate-500 dark:text-slate-400" />
            </div>
            <div>
                <h4 class="font-bold text-slate-800 dark:text-slate-100 text-sm">Notifikasi Web Push</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    {{ isSubscribed ? 'Aktif! Anda akan menerima pengingat hutang & budget.' : 'Dapatkan pengingat langsung ke perangkat Anda.' }}
                </p>
            </div>
        </div>
        <PrimaryButton 
            @click="toggleSubscription" 
            :disabled="isLoading"
            :class="[isSubscribed ? 'bg-rose-600 hover:bg-rose-700' : 'bg-royal-600 hover:bg-royal-700', 'px-3 py-1.5 text-xs']"
        >
            <Loader2 v-if="isLoading" class="w-3 h-3 animate-spin mr-1" />
            {{ isSubscribed ? 'Matikan' : 'Aktifkan' }}
        </PrimaryButton>
    </div>
</template>
