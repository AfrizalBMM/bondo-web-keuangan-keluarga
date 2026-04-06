<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

const isLoading = ref(false);
const showOverlay = ref(false);
let delayTimeout = null;

const startLoading = () => {
    isLoading.value = true;
    
    // Show overlay only if page takes > 300ms to load
    clearTimeout(delayTimeout);
    delayTimeout = setTimeout(() => {
        if (isLoading.value) {
            showOverlay.value = true;
        }
    }, 300);
};

const finishLoading = () => {
    isLoading.value = false;
    showOverlay.value = false;
    clearTimeout(delayTimeout);
};

onMounted(() => {
    router.on('start', startLoading);
    router.on('finish', finishLoading);
    router.on('error', finishLoading);
    router.on('cancel', finishLoading);
});

onUnmounted(() => {
    clearTimeout(delayTimeout);
});
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div 
            v-if="showOverlay" 
            class="fixed inset-0 z-[100] flex items-center justify-center bg-white/40 dark:bg-slate-950/40 backdrop-blur-md"
        >
            <div class="relative flex flex-col items-center">
                <!-- Pulsing Effect -->
                <div class="absolute inset-0 bg-royal-500/20 rounded-full blur-2xl animate-ping"></div>
                
                <!-- Bondo Emblem -->
                <div class="relative w-24 h-24 animate-pulse">
                    <ApplicationLogo class="w-full h-full drop-shadow-2xl" />
                </div>
                
                <!-- Subtitle -->
                <p class="mt-6 text-sm font-black text-royal-600 dark:text-royal-400 tracking-[0.2em] uppercase animate-pulse">
                    Bondo Finance
                </p>
                <div class="mt-2 flex gap-1">
                    <div class="w-1.5 h-1.5 rounded-full bg-royal-500 animate-bounce" style="animation-delay: 0s"></div>
                    <div class="w-1.5 h-1.5 rounded-full bg-royal-500 animate-bounce" style="animation-delay: 0.1s"></div>
                    <div class="w-1.5 h-1.5 rounded-full bg-royal-500 animate-bounce" style="animation-delay: 0.2s"></div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.8;
        transform: scale(0.95);
    }
}
</style>
