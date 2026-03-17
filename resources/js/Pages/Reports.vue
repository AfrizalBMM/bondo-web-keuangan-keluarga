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

const timeFilter = ref('Bulan Ini');
const timeOptions = ['7 Hari Terakhir', '30 Hari Terakhir', 'Bulan Ini', 'Tahun Ini', 'Kustom'];

// Mock Metrics
const metrics = {
    avgDailyExpense: 154000,
    cashflowDifference: 4500000, // Income - Expense
    largestTransaction: {
        name: 'Pembayaran Asuransi Tahunan',
        amount: 3500000,
        date: '12 Mar 2026'
    }
};

// --- CHART DATA ---

// 1. Line Chart: Income vs Expense Trend (6 Months)
const trendChartData = {
    labels: ['Okt', 'Nov', 'Des', 'Jan', 'Feb', 'Mar'],
    datasets: [
        {
            label: 'Pemasukan',
            data: [15000000, 15500000, 25000000, 15000000, 15000000, 16000000],
            borderColor: '#10b981', // emerald-500
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            borderWidth: 2,
            tension: 0.4,
            fill: true
        },
        {
            label: 'Pengeluaran',
            data: [12000000, 13000000, 18000000, 11000000, 12500000, 11500000],
            borderColor: '#f43f5e', // rose-500
            backgroundColor: 'rgba(244, 63, 94, 0.1)',
            borderWidth: 2,
            tension: 0.4,
            fill: true
        }
    ]
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

// 2. Bar Chart: Expense by Category
const categoryChartData = {
    labels: ['Makan', 'Transport', 'Tagihan', 'Belanja', 'Hiburan'],
    datasets: [{
        label: 'Total Pengeluaran',
        data: [4500000, 1200000, 2500000, 2000000, 1300000],
        backgroundColor: [
            '#3b82f6', // blue-500
            '#f59e0b', // amber-500
            '#ef4444', // red-500
            '#8b5cf6', // violet-500
            '#10b981'  // emerald-500
        ],
        borderRadius: 4
    }]
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
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-2">
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900 dark:text-slate-50">Laporan & Analitik</h1>
                        <p class="mt-1 text-slate-500 dark:text-slate-400">Analisa mendalam mengenai tren arus kas dan kebiasaan finansial keluarga.</p>
                    </div>
                    
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <div class="relative min-w-[180px]">
                            <select v-model="timeFilter" class="w-full pl-10 pr-8 py-2.5 text-sm font-medium border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 shadow-sm focus:ring-royal-500 focus:border-royal-500 text-slate-700 dark:text-slate-200 appearance-none">
                                <option v-for="opt in timeOptions" :key="opt" :value="opt">{{ opt }}</option>
                            </select>
                            <Calendar class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-royal-500 pointer-events-none" />
                        </div>
                        
                        <button class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition shadow-sm">
                            <Download class="w-4 h-4" />
                            <span class="hidden sm:inline">Export PDF/Excel</span>
                        </button>
                    </div>
                </div>

                <!-- Strategic Metrics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="glass-card p-6 flex items-start gap-4">
                        <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-xl text-blue-600 dark:text-blue-400 mt-1">
                            <LayoutDashboard class="w-6 h-6" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Rata-rata Pengeluaran Harian</p>
                            <p class="text-2xl font-bold text-slate-800 dark:text-slate-100 mt-1">{{ maskValue(IDR.format(metrics.avgDailyExpense)) }}</p>
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
                            <p class="text-2xl font-bold text-slate-800 dark:text-slate-100 mt-1">{{ maskValue(IDR.format(metrics.cashflowDifference)) }}</p>
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
                            <p class="text-xl font-bold text-slate-800 dark:text-slate-100 mt-1 truncate" :title="metrics.largestTransaction.name">{{ metrics.largestTransaction.name }}</p>
                            <p class="text-sm font-semibold text-rose-600 dark:text-rose-400 mt-0.5">{{ maskValue(IDR.format(metrics.largestTransaction.amount)) }}</p>
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
                            <Line :data="trendChartData" :options="trendChartOptions" />
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
                            <Bar :data="categoryChartData" :options="categoryChartOptions" />
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
