<script setup>
import { useConfirm } from '@/Composables/useConfirm';
import { h } from 'vue';
import { X, AlertTriangle } from 'lucide-vue-next';

const { state, close } = useConfirm();

const handleConfirm = () => {
    if (state.onConfirm) {
        state.onConfirm();
    }
    close();
};

// --- WARNING SVG ---
const WarningSVG = h('svg', { viewBox: '0 0 24 24', fill: 'none', xmlns: 'http://www.w3.org/2000/svg' }, [
    h('path', { d: 'M12 3L2 21H22L12 3Z', fill: 'currentColor', opacity: '0.2' }),
    h('path', { d: 'M12 9V13M12 17H12.01', stroke: 'currentColor', 'stroke-width': '2', 'stroke-linecap': 'round', 'stroke-linejoin': 'round' })
]);
</script>

<template>
    <transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
    >
        <div v-if="state.isOpen" class="fixed inset-0 z-[10000] flex items-center justify-center px-4">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-slate-950/40 backdrop-blur-sm" @click="close"></div>
            
            <!-- Modal Content -->
            <div class="relative bg-white dark:bg-slate-900 rounded-[2rem] shadow-2xl w-full max-w-sm overflow-hidden transform transition-all border border-slate-200 dark:border-slate-800 p-8">
                
                <div class="flex flex-col items-center text-center">
                    <!-- Icon -->
                    <div class="w-20 h-20 bg-amber-50 dark:bg-amber-900/30 rounded-3xl flex items-center justify-center mb-6 text-amber-500">
                        <component :is="WarningSVG" class="w-12 h-12" />
                    </div>
                    
                    <h3 class="text-xl font-black text-slate-900 dark:text-white mb-2 leading-tight">
                        {{ state.title }}
                    </h3>
                    
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-8 leading-relaxed">
                        {{ state.message }}
                    </p>
                    
                    <div class="flex flex-col w-full gap-3">
                        <button 
                            @click="handleConfirm"
                            class="w-full py-4 bg-rose-500 hover:bg-rose-600 text-white rounded-2xl font-bold transition-all shadow-lg shadow-rose-500/20 active:scale-95"
                        >
                            {{ state.confirmText }}
                        </button>
                        
                        <button 
                            @click="close"
                            class="w-full py-4 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-2xl font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition-all active:scale-95"
                        >
                            {{ state.cancelText }}
                        </button>
                    </div>
                </div>
                
                <!-- Close Button (Optional) -->
                <button @click="close" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600 transition-colors">
                    <X class="w-5 h-5" />
                </button>
            </div>
        </div>
    </transition>
</template>
