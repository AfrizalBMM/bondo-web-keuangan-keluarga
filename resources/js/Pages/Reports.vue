<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { useVisibility } from '@/Composables/useVisibility';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Bar, Line, Doughnut } from 'vue-chartjs';
import { 
    Chart as ChartJS, Title, Tooltip, Legend, BarElement, 
    CategoryScale, LinearScale, PointElement, LineElement, 
    ArcElement, Filler 
} from 'chart.js';
import { 
    FileText, Download, Calendar, TrendingUp, TrendingDown, 
    LayoutDashboard, Wallet, PieChart, ArrowRight, Info,
    ChevronLeft, ChevronRight, Calculator, ChevronDown, History,
    Target, AlertCircle
} from 'lucide-vue-next';

ChartJS.register(
    Title, Tooltip, Legend, BarElement, CategoryScale, 
    LinearScale, PointElement, LineElement, ArcElement, Filler
);

const { maskValue } = useVisibility();

// Format Currency
const IDR = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
});

const props = defineProps({
    metrics: Object,
    monthList: Array,
    categorySummary: Array,
    selectedMonth: Number,
    selectedYear: Number,
    activeMonthLabel: String,
    trendChartData: Object,
    categoryChartData: Object,
});

const isDropdownOpen = ref(false);
const dropdownRef = ref(null);

const handleClickOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        isDropdownOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('mousedown', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('mousedown', handleClickOutside);
});

const switchMonth = (m, y) => {
    isDropdownOpen.value = false;
    router.get(route('reports'), { month: m, year: y }, { 
        preserveState: true,
        preserveScroll: true
    });
};

// LOGIC FOR TABS & DROPDOWN
const historyMonths = computed(() => {
    if (props.monthList.length <= 4) return [];
    return props.monthList.slice(0, props.monthList.length - 4);
});

const tabMonths = computed(() => {
    if (props.monthList.length <= 4) return props.monthList;
    return props.monthList.slice(-4);
});

const isMonthInDropdown = computed(() => {
    return historyMonths.value.some(m => m.month === props.selectedMonth && m.year === props.selectedYear);
});

const activeMonthInHistory = computed(() => {
    return historyMonths.value.find(m => m.month === props.selectedMonth && m.year === props.selectedYear);
});

// CHART OPTIONS
const trendChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    interaction: {
        intersect: false,
        mode: 'index',
    },
    plugins: {
        legend: { 
            position: 'top', 
            align: 'end',
            labels: { 
                usePointStyle: true, 
                boxWidth: 6,
                font: { size: 11, weight: '600' },
                padding: 20
            } 
        },
        tooltip: {
            backgroundColor: 'rgba(15, 23, 42, 0.9)',
            padding: 12,
            titleFont: { size: 13, weight: '700' },
            bodyFont: { size: 12 },
            cornerRadius: 8,
            boxPadding: 4
        }
    },
    scales: {
        y: { 
            beginAtZero: true, 
            grid: { color: 'rgba(148, 163, 184, 0.05)' },
            ticks: {
                font: { size: 10 },
                callback: (value) => value >= 1000000 ? (value/1000000) + 'jt' : value
            }
        },
        x: { 
            grid: { display: false },
            ticks: { font: { size: 10 } }
        }
    }
};

const categoryChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { 
            position: 'right',
            labels: {
                usePointStyle: true,
                boxWidth: 8,
                font: { size: 11 }
            }
        }
    },
    cutout: '70%'
};

const hasData = computed(() => {
    return props.metrics.totalIncome > 0 || props.metrics.totalExpense > 0;
});

</script>

<template>
    <AuthenticatedLayout>
        <Head title="Laporan Keuangan" />

        <div class="py-12 bg-slate-50 dark:bg-slate-950 min-h-screen">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-8">
                
                <!-- HEADER & MONTH TABS -->
                <div class="space-y-6">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Analisis Keuangan</h1>
                            <p class="text-slate-500 dark:text-slate-400 font-medium">Ringkasan performa finansial keluarga periode <span class="text-royal-500 font-bold">{{ activeMonthLabel }}</span>.</p>
                        </div>
                        
                        <a 
                            :href="route('reports.export-pdf', { month: selectedMonth, year: selectedYear })" 
                            target="_blank"
                            class="flex items-center gap-2 px-5 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm hover:shadow-md transition-all font-bold text-sm text-slate-700 dark:text-slate-200 group"
                        >
                            <Download class="w-4 h-4 text-royal-500 group-hover:scale-110 transition-transform" />
                            Unduh Laporan PDF
                        </a>
                    </div>

                    <!-- MONTH SELECTOR TABS -->
                    <div class="flex items-center gap-2">
                        <!-- HISTORY DROPDOWN -->
                        <div v-if="historyMonths.length > 0" class="relative flex-shrink-0" ref="dropdownRef">
                            <button 
                                @click="isDropdownOpen = !isDropdownOpen"
                                :class="[
                                    'flex items-center gap-2 px-5 py-3 rounded-2xl font-bold text-sm transition-all border shadow-sm',
                                    isMonthInDropdown 
                                        ? 'bg-royal-600 border-royal-500 text-white shadow-royal-500/25' 
                                        : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:border-royal-300'
                                ]"
                            >
                                <History class="w-4 h-4" />
                                <span>{{ isMonthInDropdown ? activeMonthInHistory.label : 'Riwayat' }}</span>
                                <ChevronDown class="w-3.5 h-3.5 transition-transform" :class="{ 'rotate-180': isDropdownOpen }" />
                            </button>

                            <div v-if="isDropdownOpen" class="absolute left-0 mt-2 w-48 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl z-50 overflow-hidden animate-in fade-in zoom-in duration-200 origin-top-left">
                                <div class="max-h-60 overflow-y-auto no-scrollbar">
                                    <button 
                                        v-for="m in historyMonths" 
                                        :key="m.label"
                                        @click="switchMonth(m.month, m.year)"
                                        class="w-full text-left px-4 py-3 text-sm font-bold transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/50 flex items-center justify-between"
                                        :class="m.month === selectedMonth && m.year === selectedYear ? 'text-royal-500 bg-royal-50/50 dark:bg-royal-900/10' : 'text-slate-600 dark:text-slate-300'"
                                    >
                                        {{ m.label }}
                                        <div v-if="m.month === selectedMonth && m.year === selectedYear" class="w-1.5 h-1.5 rounded-full bg-royal-500"></div>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- MONTH TABS -->
                        <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar scroll-smooth">
                            <button 
                                v-for="m in tabMonths" 
                                :key="m.label"
                                @click="switchMonth(m.month, m.year)"
                                :class="[
                                    'flex-shrink-0 px-6 py-3 rounded-2xl font-bold text-sm transition-all border shadow-sm items-center gap-2 flex whitespace-nowrap',
                                    m.month === selectedMonth && m.year === selectedYear
                                        ? 'bg-royal-600 border-royal-500 text-white shadow-royal-500/25 scale-105'
                                        : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:border-royal-300 dark:hover:border-royal-800'
                                ]"
                            >
                                {{ m.label }}
                            </button>
                        </div>
                    </div>
                </div>

                <div v-if="hasData" class="space-y-8">
                    <!-- STRATEGIC METRICS GRID -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- INCOME -->
                        <div class="group h-full">
                            <div class="glass-card p-6 h-full border-l-4 border-l-emerald-500 relative overflow-hidden transition-all hover:scale-[1.02]">
                                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                                    <TrendingUp class="w-16 h-16" />
                                </div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Total Pemasukan</p>
                                <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ maskValue(IDR.format(metrics.totalIncome)) }}</p>
                                <div class="mt-4 flex items-center gap-1 text-[10px] bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 px-2 py-1 rounded-lg w-fit">
                                    <Info class="w-3 h-3" /> Arus kas masuk bulan ini
                                </div>
                            </div>
                        </div>

                        <!-- EXPENSE -->
                        <div class="group h-full">
                            <div class="glass-card p-6 h-full border-l-4 border-l-rose-500 relative overflow-hidden transition-all hover:scale-[1.02]">
                                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                                    <TrendingDown class="w-16 h-16" />
                                </div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Total Pengeluaran</p>
                                <p class="text-2xl font-black text-rose-600 dark:text-rose-400">{{ maskValue(IDR.format(metrics.totalExpense)) }}</p>
                                <div class="mt-4 flex items-center gap-1 text-[10px] bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 px-2 py-1 rounded-lg w-fit">
                                    <Info class="w-3 h-3" /> Dana yang telah digunakan
                                </div>
                            </div>
                        </div>

                        <!-- SAVINGS RATE -->
                        <div class="group h-full">
                            <div class="glass-card p-6 h-full border-l-4 border-l-royal-500 relative overflow-hidden transition-all hover:scale-[1.02]">
                                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                                    <PieChart class="w-16 h-16" />
                                </div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Savings Rate</p>
                                <div class="flex items-end gap-2">
                                    <p class="text-2xl font-black text-royal-600 dark:text-royal-400">{{ metrics.savingsRate }}%</p>
                                    <p class="text-[10px] font-bold text-slate-400 mb-1.5">({{ maskValue(IDR.format(metrics.savingsAmount)) }})</p>
                                </div>
                                <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full mt-4 overflow-hidden">
                                    <div class="h-full bg-royal-500 rounded-full" :style="{ width: metrics.savingsRate + '%' }"></div>
                                </div>
                            </div>
                        </div>

                        <!-- LARGEST TRX -->
                        <div class="group h-full">
                            <div class="glass-card p-6 h-full border-l-4 border-l-amber-500 relative overflow-hidden transition-all hover:scale-[1.02]">
                                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                                    <Calculator class="w-16 h-16" />
                                </div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Transaksi Terbesar</p>
                                <p v-if="metrics.largestTransaction" class="text-lg font-bold text-slate-800 dark:text-white line-clamp-1">
                                    {{ metrics.largestTransaction.name }}
                                </p>
                                <p v-if="metrics.largestTransaction" class="text-sm font-black text-amber-600 mt-1">
                                    {{ maskValue(IDR.format(metrics.largestTransaction.amount)) }}
                                </p>
                                <p v-else class="text-sm font-bold text-slate-400">Tidak ada pengeluaran</p>
                            </div>
                        </div>
                    </div>

                    <!-- CHARTS SECTION -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-2 space-y-4">
                            <div class="glass-card p-8">
                                <div class="flex items-center justify-between mb-8">
                                    <div>
                                        <h3 class="text-lg font-black text-slate-800 dark:text-white">Tren Arus Kas (Hingga {{ activeMonthLabel }})</h3>
                                        <p class="text-xs text-slate-500 font-medium">Visualisasi performa keuangan 6 bulan terakhir</p>
                                    </div>
                                    <div class="flex items-center gap-4 text-xs font-bold">
                                        <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div> Masuk</div>
                                        <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full bg-rose-500"></div> Keluar</div>
                                    </div>
                                </div>
                                <div class="h-[350px] w-full">
                                    <Line :data="trendChartData" :options="trendChartOptions" />
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="glass-card p-8 h-full flex flex-col">
                                <div class="mb-8">
                                    <h3 class="text-lg font-black text-slate-800 dark:text-white">Alokasi Kas Keluar</h3>
                                    <p class="text-xs text-slate-500 font-medium">Distribusi berdasarkan kategori belanja</p>
                                </div>
                                <div class="flex-grow flex items-center justify-center relative">

                                    <div class="h-[280px] w-full z-10">
                                        <Doughnut v-if="categoryChartData.labels.length > 0" :data="categoryChartData" :options="categoryChartOptions" />
                                        <div v-else class="h-full flex flex-col items-center justify-center text-center p-6 space-y-2">
                                            <PieChart class="w-12 h-12 text-slate-200 dark:text-slate-800" />
                                            <p class="text-sm font-bold text-slate-400">Belum ada kategori</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DETAILED CATEGORY SUMMARY TABLE -->
                    <div class="glass-card overflow-hidden">
                        <div class="p-8 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/20">
                            <div>
                                <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight">Ringkasan Pengeluaran per Kategori</h3>
                                <p class="text-sm text-slate-500 font-medium mt-1">Perbandingan realisasi pengeluaran terhadap anggaran standar.</p>
                            </div>
                            <div class="p-3 bg-royal-50 dark:bg-royal-950/30 rounded-2xl text-royal-600 dark:text-royal-400">
                                <Target class="w-6 h-6" />
                            </div>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="bg-slate-50/30 dark:bg-slate-900/40">
                                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Kategori</th>
                                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Anggaran</th>
                                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Realisasi</th>
                                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Sisa / Lebih</th>
                                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right w-[20%]">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    <tr v-for="cat in categorySummary" :key="cat.name" class="group hover:bg-slate-50/80 dark:hover:bg-slate-800/30 transition-all">
                                        <td class="px-8 py-5">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm shadow-sm transition-transform group-hover:scale-110" :style="{ backgroundColor: cat.color + '15', color: cat.color }">
                                                    {{ cat.icon || '📦' }}
                                                </div>
                                                <span class="font-bold text-slate-700 dark:text-slate-200">{{ cat.name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <span class="text-sm font-bold text-slate-500 dark:text-slate-400">{{ cat.limit > 0 ? maskValue(IDR.format(cat.limit)) : '-' }}</span>
                                        </td>
                                        <td class="px-6 py-5">
                                            <span class="text-sm font-black text-slate-800 dark:text-white">{{ maskValue(IDR.format(cat.actual)) }}</span>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div v-if="cat.limit > 0">
                                                <span v-if="cat.is_over" class="text-xs font-black text-rose-600 bg-rose-50 dark:bg-rose-900/20 px-2 py-1 rounded-lg">
                                                    Lebih {{ maskValue(IDR.format(cat.actual - cat.limit)) }}
                                                </span>
                                                <span v-else class="text-xs font-black text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 px-2 py-1 rounded-lg">
                                                    Sisa {{ maskValue(IDR.format(cat.remaining)) }}
                                                </span>
                                            </div>
                                            <span v-else class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Tanpa Anggaran</span>
                                        </td>
                                        <td class="px-8 py-5 text-right">
                                            <div class="flex flex-col items-end gap-2">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[10px] font-black" :class="cat.is_over ? 'text-rose-500' : 'text-slate-500'">{{ cat.percentage }}%</span>
                                                    <div v-if="cat.is_over" class="text-rose-500 animate-pulse"><AlertCircle class="w-3.5 h-3.5" /></div>
                                                </div>
                                                <div class="w-32 bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden">
                                                    <div 
                                                        class="h-full rounded-full transition-all duration-1000" 
                                                        :class="cat.is_over ? 'bg-rose-500 shadow-[0_0_10px_rgba(244,63,94,0.4)]' : (cat.percentage > 80 ? 'bg-amber-500' : 'bg-emerald-500')"
                                                        :style="{ width: cat.percentage + '%' }"
                                                    ></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- EMPTY DATA STATE -->
                <div v-else class="glass-card p-20 text-center flex flex-col items-center justify-center space-y-6 animate-in fade-in zoom-in duration-500">
                    <div class="w-24 h-24 bg-slate-50 dark:bg-slate-900 rounded-3xl flex items-center justify-center shadow-inner">
                        <FileText class="w-12 h-12 text-slate-300" />
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight">Belum Ada Transaksi</h2>
                        <p class="text-slate-500 font-medium max-w-sm mt-2 mx-auto">Kami tidak menemukan catatan finansial untuk bulan {{ activeMonthLabel }}. Mulai catat keuangan Anda sekarang.</p>
                    </div>
                    <button @click="router.get(route('transactions'))" class="px-8 py-4 bg-royal-600 text-white font-black rounded-2xl shadow-lg shadow-royal-500/30 hover:bg-royal-700 active:scale-95 transition-all flex items-center gap-3">
                        Buat Transaksi Baru <ArrowRight class="w-4 h-4" />
                    </button>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.glass-card {
    @apply bg-white/70 dark:bg-slate-900/60 backdrop-blur-xl border border-slate-200/50 dark:border-slate-800/50 rounded-[2rem] shadow-xl shadow-slate-200/20 dark:shadow-none;
}
</style>
