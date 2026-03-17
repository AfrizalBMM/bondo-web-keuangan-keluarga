<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { useVisibility } from '@/Composables/useVisibility';
import { computed, ref, onMounted } from 'vue';
import { 
    Copy, 
    Wallet, 
    TrendingUp, 
    TrendingDown, 
    CreditCard, 
    ArrowUpRight, 
    ArrowDownRight,
    Search
} from 'lucide-vue-next';

// Chart.js setup
import { Chart as ChartJS, ArcElement, Tooltip, Legend, CategoryScale, LinearScale, PointElement, LineElement } from 'chart.js';
import { Doughnut, Line } from 'vue-chartjs';

ChartJS.register(ArcElement, Tooltip, Legend, CategoryScale, LinearScale, PointElement, LineElement);

const page = usePage();
const { maskValue } = useVisibility();

// Hardcoded data based on PRD
const familyName = 'Keluarga Cemara';
const inviteCode = 'XDASCASDAS';
const copied = ref(false);

const copyInviteCode = () => {
    navigator.clipboard.writeText(inviteCode);
    copied.value = true;
    setTimeout(() => copied.value = false, 2000);
};

// Greeting logic
const greeting = computed(() => {
    const hour = new Date().getHours();
    if (hour < 11) return 'Selamat Pagi';
    if (hour < 15) return 'Selamat Siang';
    if (hour < 18) return 'Selamat Sore';
    return 'Selamat Malam';
});

// Dummy Metrics
const metrics = {
    saldoTersedia: 45500000,
    posisiBersih: 125000000,
    cashflowMasuk: 15000000,
    cashflowKeluar: 8500000
};

// Format Currency
const IDR = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
});

// Chart Data
const doughnutData = {
    labels: ['Makan', 'Transport', 'Tagihan', 'Hiburan'],
    datasets: [{
        backgroundColor: ['#3b82f6', '#f59e0b', '#ef4444', '#10b981'],
        data: [40, 20, 30, 10]
    }]
};
const doughnutOptions = { responsive: true, maintainAspectRatio: false };

const lineData = {
    labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
    datasets: [{
        label: 'Arus Kas',
        borderColor: '#3b82f6',
        backgroundColor: 'rgba(59, 130, 246, 0.2)',
        data: [120, 150, 180, 90, 200, 250, 100],
        fill: true,
        tension: 0.4
    }]
};
const lineOptions = {
    responsive: true,
    maintainAspectRatio: false,
    scales: { x: { display: false }, y: { display: false } },
    plugins: { legend: { display: false } }
};

// Dummy Recent Activity
const recentActivities = [
    { id: 1, text: 'Makan Bareng Keluarga', category: 'Makan', wallet: 'BCA', amount: -250000, date: '17 Mar, 14:30' },
    { id: 2, text: 'Gaji Bulanan', category: 'Gaji', wallet: 'BCA', amount: 15000000, date: '16 Mar, 09:00' },
    { id: 3, text: 'Bayar Listrik', category: 'Tagihan', wallet: 'Gopay', amount: -500000, date: '15 Mar, 10:15' },
    { id: 4, text: 'Bensin Mobil', category: 'Transport', wallet: 'Mandiri', amount: -300000, date: '14 Mar, 08:20' },
    { id: 5, text: 'Belanja Bulanan', category: 'Kebutuhan', wallet: 'BCA', amount: -1200000, date: '13 Mar, 19:40' },
];

</script>

<template>
    <AuthenticatedLayout>
        <Head title="Command Center" />

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                
                <!-- Header Section -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900 dark:text-slate-50">
                            {{ greeting }}, {{ page.props.auth.user.name }}
                        </h1>
                        <p class="mt-1 text-slate-500 dark:text-slate-400 flex items-center gap-2">
                            <span>{{ familyName }}</span>
                            <span class="px-2 py-0.5 text-xs rounded-full bg-royal-100 text-royal-700 dark:bg-royal-900 dark:text-royal-300 font-medium tracking-wide">
                                Kode: {{ inviteCode }}
                            </span>
                            <button @click="copyInviteCode" class="text-royal-600 hover:text-royal-800 dark:text-royal-400 dark:hover:text-royal-300 transition" title="Salin Kode Undangan">
                                <Copy class="w-4 h-4" />
                            </button>
                            <span v-if="copied" class="text-xs text-emerald-600 dark:text-emerald-400">Tersalin!</span>
                        </p>
                    </div>

                    <!-- Smart Add Input Placeholder -->
                    <div class="mt-4 md:mt-0 w-full md:w-96 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <Search class="h-5 w-5" />
                        </div>
                        <input type="text" placeholder="Beli cilok 5rb tunai..." class="block w-full pl-10 pr-3 py-3 border-transparent rounded-full bg-white dark:bg-slate-800 shadow-sm focus:border-royal-500 focus:ring focus:ring-royal-500/20 glass text-sm transition-all text-slate-900 dark:text-slate-100 placeholder-slate-400" />
                        <div class="absolute inset-y-0 right-0 pr-2 flex items-center">
                            <span class="text-xs font-semibold bg-gradient-to-r from-royal-500 to-indigo-500 text-white px-2 py-1 rounded-full">AI</span>
                        </div>
                    </div>
                </div>

                <!-- Key Metrics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    
                    <!-- Saldo Tersedia -->
                    <div class="glass-card p-6 border-l-4 border-l-royal-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Total Saldo Tersedia</p>
                                <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-50">
                                    {{ maskValue(IDR.format(metrics.saldoTersedia)) }}
                                </h3>
                            </div>
                            <div class="p-3 bg-royal-100 dark:bg-royal-900/40 rounded-full text-royal-600 dark:text-royal-400">
                                <Wallet class="w-6 h-6" />
                            </div>
                        </div>
                    </div>

                    <!-- Posisi Bersih -->
                    <div class="glass-card p-6 border-l-4 border-l-indigo-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Posisi Bersih (Kekayaan)</p>
                                <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-50">
                                    {{ maskValue(IDR.format(metrics.posisiBersih)) }}
                                </h3>
                            </div>
                            <div class="p-3 bg-indigo-100 dark:bg-indigo-900/40 rounded-full text-indigo-600 dark:text-indigo-400">
                                <CreditCard class="w-6 h-6" />
                            </div>
                        </div>
                    </div>

                    <!-- Cashflow -->
                    <div class="glass-card p-6 border-l-4 border-l-emerald-500">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Cashflow Bulan Ini</p>
                            <TrendingUp class="w-5 h-5 text-emerald-500" />
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center text-sm">
                                <span class="flex items-center text-emerald-600 dark:text-emerald-400"><ArrowDownRight class="w-4 h-4 mr-1"/> Masuk</span>
                                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ maskValue(IDR.format(metrics.cashflowMasuk)) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="flex items-center text-rose-500 dark:text-rose-400"><ArrowUpRight class="w-4 h-4 mr-1"/> Keluar</span>
                                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ maskValue(IDR.format(metrics.cashflowKeluar)) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts & Recent Activity -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Spending Distribution -->
                    <div class="glass-card p-6 flex flex-col">
                        <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4">Pengeluaran per Kategori</h3>
                        <div class="flex-grow min-h-[250px] relative">
                            <Doughnut :data="doughnutData" :options="doughnutOptions" />
                        </div>
                    </div>

                    <!-- Mini Trend & Recent Activity -->
                    <div class="glass-card p-0 lg:col-span-2 overflow-hidden flex flex-col">
                        <div class="px-6 pt-6 pb-4 border-b border-slate-200 dark:border-slate-700/50 flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Aktivitas Terkini</h3>
                            <button class="text-sm text-royal-600 dark:text-royal-400 hover:text-royal-800 font-medium">Lihat Semua</button>
                        </div>
                        
                        <!-- Mini Trend Cashflow Sparkline -->
                        <div class="h-16 w-full -mt-2 bg-gradient-to-b from-transparent to-royal-50/50 dark:to-transparent">
                            <Line :data="lineData" :options="lineOptions" />
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <tbody>
                                    <tr v-for="trx in recentActivities" :key="trx.id" class="border-b border-slate-100 dark:border-slate-700/30 last:border-0 hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div :class="[
                                                    'w-10 h-10 rounded-full flex items-center justify-center mr-4',
                                                    trx.amount > 0 ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-400' : 'bg-rose-100 text-rose-600 dark:bg-rose-900/40 dark:text-rose-400'
                                                ]">
                                                    <TrendingUp v-if="trx.amount > 0" class="w-5 h-5" />
                                                    <TrendingDown v-else class="w-5 h-5" />
                                                </div>
                                                <div>
                                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ trx.text }}</p>
                                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ trx.category }} &bull; {{ trx.wallet }} &bull; {{ trx.date }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span :class="['text-sm font-bold', trx.amount > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-800 dark:text-slate-200']">
                                                {{ trx.amount > 0 ? '+' : '' }}{{ maskValue(IDR.format(trx.amount)) }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

