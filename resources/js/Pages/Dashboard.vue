<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { useVisibility } from '@/Composables/useVisibility';
import { computed, ref } from 'vue';
import { 
    Copy, 
    Wallet, 
    TrendingUp, 
    TrendingDown, 
    CreditCard, 
    ArrowUpRight, 
    ArrowDownRight,
    Search,
    CheckCircle2,
    Clock
} from 'lucide-vue-next';

import { ArrowRight } from 'lucide-vue-next';

// Chart.js setup
import { 
    Chart as ChartJS, 
    ArcElement, 
    Tooltip, 
    Legend, 
    CategoryScale, 
    LinearScale, 
    PointElement, 
    LineElement,
    Filler
} from 'chart.js';
import { Doughnut, Line } from 'vue-chartjs';

ChartJS.register(ArcElement, Tooltip, Legend, CategoryScale, LinearScale, PointElement, LineElement, Filler);

const page = usePage();
const { isVisible, maskValue, toggleVisibility } = useVisibility();

// Props dari DashboardController
const props = defineProps({
    metrics: Object,
    doughnutData: Object,
    lineData: Object,
    recentActivities: Array,
    familyData: Object
});

// Copy Invite Code Logic
const copied = ref(false);
const copyInviteCode = () => {
    navigator.clipboard.writeText(props.familyData?.inviteCode);
    copied.value = true;
    setTimeout(() => copied.value = false, 2000);
};

// Greeting & Formatting
const greeting = computed(() => {
    const hour = new Date().getHours();
    if (hour < 11) return 'Selamat Pagi';
    if (hour < 15) return 'Selamat Siang';
    if (hour < 18) return 'Selamat Sore';
    return 'Selamat Malam';
});

const formatIDR = (val) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(val);
};

// Chart Options
const doughnutOptions = {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '75%',
    plugins: {
        legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
    }
};

const lineOptions = {
    responsive: true,
    maintainAspectRatio: false,
    scales: { x: { display: false }, y: { display: false } },
    plugins: { legend: { display: false } },
    elements: { point: { radius: 0 } }
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Command Center" />

        <div class="py-10 animate-in fade-in zoom-in duration-500">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
                    <div>
                        <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                            {{ greeting }}, {{ page.props.auth.user.name }}
                        </h1>
                        <div class="mt-2 flex items-center gap-3">
                            <span class="text-slate-500 dark:text-slate-400 font-medium">Keluarga {{ props.familyData?.name }}</span>
                            <div class="flex items-center bg-royal-50 dark:bg-royal-900/30 border border-royal-100 dark:border-royal-800 rounded-full px-3 py-1">
                                <span class="text-xs font-mono font-bold text-royal-600 dark:text-royal-400 mr-2">
                                    #{{ props.familyData?.inviteCode }}
                                </span>
                                <button @click="copyInviteCode" class="hover:text-royal-500 transition-colors">
                                    <CheckCircle2 v-if="copied" class="w-3.5 h-3.5 text-emerald-500" />
                                    <Copy v-else class="w-3.5 h-3.5" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                    <div class="glass-card p-6 relative overflow-hidden group">
                        <div class="relative z-10 flex justify-between items-start">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Total Saldo</p>
                                <h3 class="text-2xl font-black text-slate-900 dark:text-white">
                                    {{ maskValue(formatIDR(props.metrics?.saldoTersedia)) }}
                                </h3>
                            </div>
                            <button @click="toggleVisibility" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition">
                                <Wallet class="w-6 h-6 text-royal-500" />
                            </button>
                        </div>
                        <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-700">
                            <Wallet class="w-24 h-24 text-royal-500" />
                        </div>
                    </div>

                    <div class="glass-card p-6 border-l-4 border-l-indigo-500">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Posisi Bersih</p>
                                <h3 class="text-2xl font-black text-slate-900 dark:text-white">
                                    {{ maskValue(formatIDR(props.metrics?.posiseBersih)) }}
                                </h3>
                            </div>
                            <div class="p-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg">
                                <CreditCard class="w-6 h-6 text-indigo-500" />
                            </div>
                        </div>
                    </div>

                    <div class="glass-card p-6">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Bulan Ini</p>
                            <TrendingUp class="w-4 h-4 text-emerald-500" />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-[10px] text-slate-400 block mb-1">MASUK</span>
                                <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">+{{ maskValue(formatIDR(props.metrics?.cashflowMasuk)) }}</span>
                            </div>
                            <div class="border-l border-slate-100 dark:border-slate-700 pl-4">
                                <span class="text-[10px] text-slate-400 block mb-1">KELUAR</span>
                                <span class="text-sm font-bold text-rose-500">-{{ maskValue(formatIDR(props.metrics?.cashflowKeluar)) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <div class="glass-card p-6 flex flex-col">
                        <h3 class="font-bold text-slate-800 dark:text-white mb-6">Distribusi Pengeluaran</h3>
                        <div class="flex-grow min-h-[250px] relative flex items-center justify-center">
                            <Doughnut v-if="props.doughnutData" :data="props.doughnutData" :options="doughnutOptions" />
                            <div v-else class="text-center">
                                <div class="w-20 h-20 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <Clock class="text-slate-300 w-8 h-8" />
                                </div>
                                <p class="text-xs text-slate-400">Belum ada transaksi</p>
                            </div>
                        </div>
                    </div>

                    <div class="glass-card p-0 lg:col-span-2 overflow-hidden flex flex-col">
                        <div class="p-6 border-b border-slate-50 dark:border-slate-700/50 flex justify-between items-center bg-slate-50/30 dark:bg-slate-800/30">
                            <h3 class="font-bold text-slate-800 dark:text-white">Aktivitas Terkini</h3>
                            <button class="text-xs font-bold text-royal-600 dark:text-royal-400 hover:underline tracking-widest uppercase">Lihat Semua</button>
                        </div>
                        
                        <div class="h-14 w-full bg-gradient-to-b from-royal-50/50 to-transparent dark:from-royal-900/10 dark:to-transparent">
                            <Line v-if="props.lineData" :data="props.lineData" :options="lineOptions" />
                        </div>

                        <div class="divide-y divide-slate-50 dark:divide-slate-700/30">
                            <div v-for="trx in props.recentActivities" :key="trx.id" class="px-6 py-4 flex items-center justify-between hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div :class="[
                                        'w-10 h-10 rounded-xl flex items-center justify-center shadow-sm',
                                        trx.amount > 0 ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30' : 'bg-rose-50 text-rose-600 dark:bg-rose-900/30'
                                    ]">
                                        <ArrowDownRight v-if="trx.amount > 0" class="w-5 h-5" />
                                        <ArrowUpRight v-else class="w-5 h-5" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 dark:text-slate-200 line-clamp-1">{{ trx.text }}</p>
                                        <p class="text-[10px] font-medium text-slate-400 uppercase tracking-tighter">
                                            {{ trx.category }} • {{ trx.wallet }} • {{ trx.date }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p :class="['text-sm font-black', trx.amount > 0 ? 'text-emerald-600' : 'text-slate-700 dark:text-slate-300']">
                                        {{ trx.amount > 0 ? '+' : '' }}{{ maskValue(formatIDR(trx.amount)) }}
                                    </p>
                                </div>
                            </div>
                            
                            <div v-if="props.recentActivities.length === 0" class="p-12 text-center">
                                <p class="text-sm text-slate-400">Mulai catat pengeluaran pertamamu!</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>



    </AuthenticatedLayout>
</template>

<style scoped>
.glass-card {
    @apply bg-white dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700/50 rounded-3xl shadow-sm;
}
@keyframes progress {
    from { width: 100%; }
    to { width: 0%; }
}
</style>