<script setup>
import { computed, watch, ref, h } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { X } from 'lucide-vue-next';

// --- PREMIUM SVG ICONS ---
const SuccessSVG = h('svg', { viewBox: '0 0 24 24', fill: 'none', xmlns: 'http://www.w3.org/2000/svg' }, [
    h('circle', { cx: '12', cy: '12', r: '10', fill: 'currentColor', opacity: '0.2' }),
    h('path', { d: 'M8 12L11 15L16 9', stroke: 'currentColor', 'stroke-width': '2', 'stroke-linecap': 'round', 'stroke-linejoin': 'round' })
]);

const ErrorSVG = h('svg', { viewBox: '0 0 24 24', fill: 'none', xmlns: 'http://www.w3.org/2000/svg' }, [
    h('circle', { cx: '12', cy: '12', r: '10', fill: 'currentColor', opacity: '0.2' }),
    h('path', { d: 'M15 9L9 15M9 9L15 15', stroke: 'currentColor', 'stroke-width': '2', 'stroke-linecap': 'round', 'stroke-linejoin': 'round' })
]);

const WarningSVG = h('svg', { viewBox: '0 0 24 24', fill: 'none', xmlns: 'http://www.w3.org/2000/svg' }, [
    h('path', { d: 'M12 3L2 21H22L12 3Z', fill: 'currentColor', opacity: '0.2' }),
    h('path', { d: 'M12 9V13M12 17H12.01', stroke: 'currentColor', 'stroke-width': '2', 'stroke-linecap': 'round', 'stroke-linejoin': 'round' })
]);

const InfoSVG = h('svg', { viewBox: '0 0 24 24', fill: 'none', xmlns: 'http://www.w3.org/2000/svg' }, [
    h('circle', { cx: '12', cy: '12', r: '10', fill: 'currentColor', opacity: '0.2' }),
    h('path', { d: 'M12 16V12M12 8H12.01', stroke: 'currentColor', 'stroke-width': '2', 'stroke-linecap': 'round', 'stroke-linejoin': 'round' })
]);

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
                icon: SuccessSVG,
                bgClass: 'bg-white/80 dark:bg-slate-900/80',
                borderClass: 'border-emerald-500/20',
                textClass: 'text-slate-800 dark:text-slate-200',
                accentClass: 'bg-emerald-500',
                iconClass: 'text-emerald-500'
            };
        case 'error':
            return {
                icon: ErrorSVG,
                bgClass: 'bg-white/80 dark:bg-slate-900/80',
                borderClass: 'border-rose-500/20',
                textClass: 'text-slate-800 dark:text-slate-200',
                accentClass: 'bg-rose-500',
                iconClass: 'text-rose-500'
            };
        case 'warning':
            return {
                icon: WarningSVG,
                bgClass: 'bg-white/80 dark:bg-slate-900/80',
                borderClass: 'border-amber-500/20',
                textClass: 'text-slate-800 dark:text-slate-200',
                accentClass: 'bg-amber-500',
                iconClass: 'text-amber-500'
            };
        case 'info':
        default:
            return {
                icon: InfoSVG,
                bgClass: 'bg-white/80 dark:bg-slate-900/80',
                borderClass: 'border-blue-500/20',
                textClass: 'text-slate-800 dark:text-slate-200',
                accentClass: 'bg-blue-500',
                iconClass: 'text-blue-500'
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
        <div v-if="isVisible" class="fixed bottom-6 right-6 z-[9999] max-w-sm w-full animate-in fade-in slide-in-from-right-4 duration-300">
            <div :class="[
                'relative overflow-hidden rounded-2xl border p-5 shadow-2xl backdrop-blur-xl flex items-center gap-4 transition-all',
                getAlertConfig(flashType).bgClass,
                getAlertConfig(flashType).borderClass
            ]">
                <!-- Decorative Accent Line -->
                <div :class="['absolute left-0 top-0 bottom-0 w-1.5', getAlertConfig(flashType).accentClass]"></div>

                <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center">
                    <component :is="getAlertConfig(flashType).icon" :class="['w-10 h-10', getAlertConfig(flashType).iconClass]" />
                </div>
                
                <div class="flex-1 min-w-0">
                    <p :class="['text-sm font-bold leading-tight', getAlertConfig(flashType).textClass]">
                        {{ flashMessage }}
                    </p>
                </div>

                <button @click="close" type="button" class="flex-shrink-0 p-1.5 rounded-full hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                    <X class="w-4 h-4 text-slate-400" />
                </button>
            </div>
        </div>
    </transition>
</template>
