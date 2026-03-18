<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useVisibility } from '@/Composables/useVisibility';
import { ref } from 'vue';
import { Bar, Line, Doughnut } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, PointElement, LineElement, ArcElement, Filler } from 'chart.js';
import { FileText, Download, Calendar, Filter, TrendingUp, TrendingDown, LayoutDashboard } from 'lucide-vue-next';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, PointElement, LineElement, ArcElement, Filler);

const { maskValue } = useVisibility();

// Format Currency
const IDR = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
});

const props = defineProps({
    metrics: Object,
    trendChartData: Object,
    categoryChartData: Object,
    currentFilter: String
});

const timeFilter = ref(props.currentFilter || 'Bulan Ini');
const timeOptions = ['7 Hari Terakhir', '30 Hari Terakhir', 'Bulan Ini', 'Tahun Ini', 'Kustom'];

const applyFilter = () => {
    // Reload page with new filter
    import('@inertiajs/vue3').then(({ router }) => {
        router.get(route('reports'), { filter: timeFilter.value }, { preserveState: true });
    });
};

const trendChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8 } }
    },
    scales: {
        y: { beginAtZero: true, grid: { color: 'rgba(148, 163, 184, 0.1)' } },
        x: { grid: { display: false } }
    }
};

const categoryChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false }
    },
    scales: {
        y: { beginAtZero: true, grid: { color: 'rgba(148, 163, 184, 0.1)' } },
        x: { grid: { display: false } }
    }
};

</script>

<template>
    <AuthenticatedLayout>
        <Head title="Laporan & Analitik" />

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-8">
                
                <!-- Header & Filters -->
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <div class="relative min-w-[180px]">
                        <select @change="applyFilter" v-model="timeFilter" class="w-full pl-10 pr-8 py-2.5 text-sm font-medium border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 shadow-sm focus:ring-royal-500 focus:border-royal-500 text-slate-700 dark:text-slate-200 appearance-none">
                            <option v-for="opt in timeOptions" :key="opt" :value="opt">{{ opt }}</option>
                        </select>
                        <Calendar class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-royal-500 pointer-events-none" />
                    </div>
                    
                    <a 
                        :href="route('reports.export-pdf', { filter: timeFilter })" 
                        target="_blank"
                        class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition shadow-sm cursor-pointer"
                    >
                        <Download class="w-4 h-4" />
                        <span class="hidden sm:inline">Export PDF</span>
                    </a>
                </div>

                <!-- Strategic Metrics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="glass-card p-6 flex items-start gap-4">
                        <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-xl text-blue-600 dark:text-blue-400 mt-1">
                            <LayoutDashboard class="w-6 h-6" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Rata-rata Pengeluaran Harian</p>
                            <p class="text-2xl font-bold text-slate-800 dark:text-slate-100 mt-1">{{ maskValue(IDR.format(props.metrics?.avgDailyExpense || 0)) }}</p>
                            <p class="text-xs text-rose-500 mt-1 flex items-center gap-1">
                                <TrendingUp class="w-3 h-3" />
                                +12% dari bulan lalu
                            </p>
                        </div>
                    </div>
                    
                    <div class="glass-card p-6 flex items-start gap-4">
                        <div class="p-3 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl text-emerald-600 dark:text-emerald-400 mt-1">
                            <TrendingUp class="w-6 h-6" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Selisih Kas Bersih (Net Cashflow)</p>
                            <p class="text-2xl font-bold text-slate-800 dark:text-slate-100 mt-1">{{ maskValue(IDR.format(props.metrics?.cashflowDifference || 0)) }}</p>
                            <p class="text-xs text-emerald-500 mt-1 flex items-center gap-1">
                                Sisa uang menganggur yang bisa ditabung
                            </p>
                        </div>
                    </div>

                    <div class="glass-card p-6 flex items-start gap-4">
                        <div class="p-3 bg-amber-50 dark:bg-amber-900/30 rounded-xl text-amber-600 dark:text-amber-400 mt-1">
                            <FileText class="w-6 h-6" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Transaksi Terbesar</p>
                            <p class="text-xl font-bold text-slate-800 dark:text-slate-100 mt-1 truncate" :title="props.metrics?.largestTransaction?.name">{{ props.metrics?.largestTransaction?.name }}</p>
                            <p class="text-sm font-semibold text-rose-600 dark:text-rose-400 mt-0.5">{{ maskValue(IDR.format(props.metrics?.largestTransaction?.amount || 0)) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Main Charts Area -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Trend Chart (Spans 2 columns) -->
                    <div class="lg:col-span-2 glass-card p-6">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="font-bold text-lg text-slate-800 dark:text-slate-100">Tren Pemasukan vs Pengeluaran</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Perbandingan arus kas selama 6 bulan terakhir</p>
                            </div>
                        </div>
                        <div class="h-[300px] w-full">
                            <Line v-if="props.trendChartData" :data="props.trendChartData" :options="trendChartOptions" />
                        </div>
                    </div>
                    
                    <!-- Category Bar Chart -->
                    <div class="glass-card p-6">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="font-bold text-lg text-slate-800 dark:text-slate-100">Distribusi Kategori</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Pos pengeluaran tertinggi {{ timeFilter.toLowerCase() }}</p>
                            </div>
                        </div>
                        <div class="h-[300px] w-full">
                            <Bar v-if="props.categoryChartData" :data="props.categoryChartData" :options="categoryChartOptions" />
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
