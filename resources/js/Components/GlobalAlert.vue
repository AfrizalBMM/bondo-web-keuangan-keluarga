<script setup>
import { computed, watch, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { CheckCircle, AlertTriangle, XCircle, Info, X } from 'lucide-vue-next';

// Define ref for self-dismissing logic
const isVisible = ref(false);

const page = usePage();

const hasFlash = computed(() => {
    return page.props.flash?.success || page.props.flash?.error || page.props.flash?.warning || page.props.flash?.info;
});

const flashType = computed(() => {
    if (page.props.flash?.success) return 'success';
    if (page.props.flash?.error) return 'error';
    if (page.props.flash?.warning) return 'warning';
    if (page.props.flash?.info) return 'info';
    return null;
});

const flashMessage = computed(() => {
    if (page.props.flash?.success) return page.props.flash.success;
    if (page.props.flash?.error) return page.props.flash.error;
    if (page.props.flash?.warning) return page.props.flash.warning;
    if (page.props.flash?.info) return page.props.flash.info;
    return null;
});

// Watch for flash message changes to show notification and set timeout
watch(
    () => page.props.flash,
    () => {
        if (hasFlash.value) {
            isVisible.value = true;
            setTimeout(() => {
                isVisible.value = false;
            }, 5000); // Auto dismiss after 5 seconds
        }
    },
    { deep: true, immediate: true }
);

const close = () => {
    isVisible.value = false;
};

const getAlertConfig = (type) => {
    switch(type) {
        case 'success':
            return {
                icon: CheckCircle,
                bgClass: 'bg-emerald-50 dark:bg-emerald-900/30',
                borderClass: 'border-emerald-200 dark:border-emerald-800',
                textClass: 'text-emerald-800 dark:text-emerald-300',
                iconClass: 'text-emerald-500 dark:text-emerald-400'
            };
        case 'error':
            return {
                icon: XCircle,
                bgClass: 'bg-rose-50 dark:bg-rose-900/30',
                borderClass: 'border-rose-200 dark:border-rose-800',
                textClass: 'text-rose-800 dark:text-rose-300',
                iconClass: 'text-rose-500 dark:text-rose-400'
            };
        case 'warning':
            return {
                icon: AlertTriangle,
                bgClass: 'bg-amber-50 dark:bg-amber-900/30',
                borderClass: 'border-amber-200 dark:border-amber-800',
                textClass: 'text-amber-800 dark:text-amber-300',
                iconClass: 'text-amber-500 dark:text-amber-400'
            };
        case 'info':
        default:
            return {
                icon: Info,
                bgClass: 'bg-blue-50 dark:bg-blue-900/30',
                borderClass: 'border-blue-200 dark:border-blue-800',
                textClass: 'text-blue-800 dark:text-blue-300',
                iconClass: 'text-blue-500 dark:text-blue-400'
            };
    }
};
</script>

<template>
    <transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="isVisible" class="fixed bottom-4 right-4 z-50 max-w-sm w-full">
            <div :class="[
                'rounded-xl border p-4 shadow-lg backdrop-blur-md flex items-start gap-3',
                getAlertConfig(flashType).bgClass,
                getAlertConfig(flashType).borderClass
            ]">
                <div class="flex-shrink-0 mt-0.5">
                    <component :is="getAlertConfig(flashType).icon" :class="['w-5 h-5', getAlertConfig(flashType).iconClass]" />
                </div>
                <div class="flex-1 w-0">
                    <p :class="['text-sm font-medium leading-relaxed', getAlertConfig(flashType).textClass]">
                        {{ flashMessage }}
                    </p>
                </div>
                <div class="flex-shrink-0 flex">
                    <button @click="close" type="button" :class="[
                        'inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2 hover:bg-black/5 dark:hover:bg-white/5 transition-colors',
                        getAlertConfig(flashType).textClass
                    ]">
                        <span class="sr-only">Close</span>
                        <X class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>
    </transition>
</template>
