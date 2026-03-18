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

import { Sparkles, Loader2, ArrowRight } from 'lucide-vue-next';
import axios from 'axios';

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

//ai
const smartText = ref('');
const isAnalyzing = ref(false);
const aiResult = ref(null);

const showToast = ref(false);
const toastMessage = ref('');
const toastData = ref(null);

const handleAiAnalysis = async () => {
    if (!smartText.value || isAnalyzing.value) return;
    
    isAnalyzing.value = true;
    
    // Pastikan toast tertutup dulu jika sebelumnya masih ada
    showToast.value = false;

    try {
        const response = await axios.post(route('smart-add.process'), {
            text: smartText.value
        });
        
        if (response.data.success) {
            toastData.value = response.data.data;
            smartText.value = '';
            
            // Tampilkan Toast
            showToast.value = true;

            // REFRESH DATA DASHBOARD (Agar saldo & aktivitas langsung nambah)
            router.reload({ 
                only: ['metrics', 'recentActivities', 'doughnutData'],
                preserveScroll: true 
            });

            // LOGIKA TUTUP OTOMATIS (4 detik)
            setTimeout(() => {
                showToast.value = false;
            }, 4000);
        }
    } catch (error) {
        console.error("Gagal analisa:", error);
        alert("Gagal memproses AI. Cek koneksi atau API Key Groq.");
    } finally {
        isAnalyzing.value = false;
    }
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

                    <div class="w-full md:w-[450px] relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <Sparkles v-if="!isAnalyzing" class="h-5 w-5 text-royal-500/50 group-focus-within:text-royal-500 transition-colors" />
                            <Loader2 v-else class="h-5 w-5 animate-spin text-royal-500" />
                        </div>

                        <input 
                            v-model="smartText"
                            @keyup.enter="handleAiAnalysis"
                            type="text" 
                            :disabled="isAnalyzing"
                            placeholder="Ketik: 'Makan siang 50rb tunai'..." 
                            class="block w-full pl-11 pr-32 py-4 border-none rounded-2xl bg-white dark:bg-slate-800 shadow-xl shadow-royal-500/5 focus:ring-2 focus:ring-royal-500/20 text-sm transition-all text-slate-900 dark:text-slate-100 placeholder-slate-400 disabled:opacity-70" 
                        />

                        <div class="absolute inset-y-0 right-2 flex items-center">
                            <button 
                                @click="handleAiAnalysis"
                                :disabled="!smartText || isAnalyzing"
                                :class="[
                                    'flex items-center gap-2 px-3 py-2 rounded-xl font-bold text-[10px] tracking-wider transition-all shadow-sm',
                                    smartText && !isAnalyzing 
                                        ? 'bg-gradient-to-r from-royal-600 to-indigo-600 text-white shadow-royal-500/20 hover:scale-105 active:scale-95' 
                                        : 'bg-slate-100 dark:bg-slate-700 text-slate-400 cursor-not-allowed'
                                ]"
                            >
                                <span v-if="!isAnalyzing">ANALISA AI</span>
                                <span v-else>PROSES...</span>
                                <ArrowRight v-if="!isAnalyzing" class="w-3 h-3" />
                            </button>
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

        <Transition
            enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
            leave-active-class="transition ease-in duration-100"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showToast" class="fixed bottom-5 right-5 z-[100] w-full max-w-sm overflow-hidden rounded-2xl bg-white dark:bg-slate-800 shadow-2xl border border-slate-100 dark:border-slate-700 pointer-events-auto">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <CheckCircle2 class="h-6 w-6 text-emerald-500" />
                        </div>
                        <div class="ml-3 w-0 flex-1 pt-0.5">
                            <p class="text-sm font-bold text-slate-900 dark:text-white">{{ toastMessage }}</p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                {{ toastData?.description }} sebesar <span class="font-bold text-emerald-600">{{ formatIDR(toastData?.amount) }}</span> telah ditambahkan ke dompet {{ toastData?.wallet }}.
                            </p>
                        </div>
                        <div class="ml-4 flex flex-shrink-0">
                            <button @click="showToast = false" class="inline-flex text-slate-400 hover:text-slate-500">
                                <span class="sr-only">Close</span>
                                <X class="h-5 w-5" />
                            </button>
                        </div>
                    </div>
                </div>
                <div class="h-1 bg-emerald-500 animate-progress"></div>
            </div>
        </Transition>

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
.animate-progress {
    animation: progress 4s linear forwards;
}
</style>