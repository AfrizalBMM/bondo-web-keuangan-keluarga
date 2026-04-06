<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { useVisibility } from '@/Composables/useVisibility';
import { useConfirm } from '@/Composables/useConfirm';
import { ref, computed } from 'vue';
import { 
    Search, Filter, Download, Plus, Edit2, Trash2, 
    ArrowUpRight, ArrowDownRight, X, Wallet, Tags, 
    CircleDollarSign, Calendar, FileText, LayoutGrid, List
} from 'lucide-vue-next';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    transactions: Array,
    wallets: Array,
    categories: Array,
    errors: Object
});

const { maskValue } = useVisibility();
const { confirm: confirmModal } = useConfirm();

// --- STATE FILTER ---
const searchQuery = ref('');
const filterType = ref('all'); 
const filterWallet = ref('all');
const filterDateRange = ref('all');

// --- LOGIKA FILTERING ---
const filteredTransactions = computed(() => {
    if (!props.transactions) return [];

    return props.transactions.filter(trx => {
        const content = (trx.text || trx.notes || trx.description || '').toLowerCase();
        const categoryName = (trx.category || '').toLowerCase();
        const searchTerm = searchQuery.value.toLowerCase();

        const matchesSearch = content.includes(searchTerm) || categoryName.includes(searchTerm);
        
        const trxType = (trx.type || '').toLowerCase();
        const matchesType = filterType.value === 'all' || trxType === filterType.value.toLowerCase();
        
        const trxWalletId = String(trx.raw_wallet_id || trx.wallet_id || '');
        const matchesWallet = filterWallet.value === 'all' || trxWalletId === String(filterWallet.value);

        const trxDateStr = trx.raw_date || trx.date;
        const trxDate = new Date(trxDateStr);
        const today = new Date();
        let matchesDate = true;

        if (filterDateRange.value === 'today') {
            matchesDate = trxDate.toDateString() === today.toDateString();
        } else if (filterDateRange.value === 'month') {
            matchesDate = trxDate.getMonth() === today.getMonth() && trxDate.getFullYear() === today.getFullYear();
        }
        
        return matchesSearch && matchesType && matchesWallet && matchesDate;
    });
});

// --- LOGIKA RINGKASAN (TOTALS) ---
const totals = computed(() => {
    return filteredTransactions.value.reduce((acc, trx) => {
        if (trx.type === 'income') acc.income += Number(trx.amount);
        else acc.expense += Number(trx.amount);
        return acc;
    }, { income: 0, expense: 0 });
});

// --- LOGIKA GROUPING BERDASARKAN TANGGAL ---
const groupedTransactions = computed(() => {
    const groups = {};
    filteredTransactions.value.forEach(trx => {
        const dateObj = new Date(trx.raw_date || trx.date);
        const today = new Date();
        const yesterday = new Date();
        yesterday.setDate(today.getDate() - 1);

        let dateLabel = '';
        if (dateObj.toDateString() === today.toDateString()) dateLabel = 'Hari Ini';
        else if (dateObj.toDateString() === yesterday.toDateString()) dateLabel = 'Kemarin';
        else dateLabel = dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });

        if (!groups[dateLabel]) groups[dateLabel] = [];
        groups[dateLabel].push(trx);
    });
    return groups;
});

// --- LOGIKA EXPORT (DIPERBAIKI) ---
const exportToCSV = () => {
    const data = filteredTransactions.value;
    if (data.length === 0) return alert('Tidak ada data untuk di-export');

    const headers = ['Tanggal', 'Keterangan', 'Kategori', 'Dompet', 'Tipe', 'Jumlah'];
    const rows = data.map(trx => [
        trx.date || '',
        `"${(trx.text || trx.notes || '').replace(/"/g, '""')}"`,
        trx.category || '',
        trx.wallet || '',
        trx.type || '',
        trx.amount || 0
    ]);

    const csvContent = [headers.join(','), ...rows.map(e => e.join(','))].join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `Laporan_Keuangan_${new Date().getTime()}.csv`;
    link.click();
};

// --- LOGIKA FORM & CRUD ---
const IDR = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 });
const isManualModalOpen = ref(false);
const editingTransactionId = ref(null);

const form = useForm({
    type: 'Expense', 
    wallet_id: '', 
    category_id: '', 
    amount: '', 
    date: '', 
    notes: ''
});

const getLocalISOTime = () => {
    const now = new Date();
    const offset = now.getTimezoneOffset() * 60000;
    return new Date(now.getTime() - offset).toISOString().slice(0, 16);
};

const openAddModal = (type = 'Expense') => {
    editingTransactionId.value = null;
    form.reset();
    form.type = type;
    form.date = getLocalISOTime();
    isManualModalOpen.value = true;
};

const openEditModal = (trx) => {
    editingTransactionId.value = trx.id;
    form.type = (trx.type === 'income') ? 'Income' : 'Expense';
    form.wallet_id = trx.raw_wallet_id || trx.wallet_id;
    form.category_id = trx.raw_category_id || trx.category_id;
    form.amount = trx.amount;
    form.date = (trx.raw_date || '').slice(0, 16);
    form.notes = trx.text || trx.notes;
    isManualModalOpen.value = true;
};

const submitManualAdd = () => {
    const action = editingTransactionId.value ? route('transactions.update', editingTransactionId.value) : route('transactions.store');
    form[editingTransactionId.value ? 'put' : 'post'](action, {
        onSuccess: () => isManualModalOpen.value = false
    });
};

const deleteTransaction = (id) => {
    confirmModal({
        title: 'Hapus Transaksi?',
        message: 'Apakah Anda yakin ingin menghapus data transaksi ini? Tindakan ini tidak dapat dibatalkan.',
        confirmText: 'Ya, Hapus',
        onConfirm: () => {
            router.delete(route('transactions.destroy', id), {
                preserveScroll: true
            });
        }
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Transaksi" />
        <div class="py-12 bg-slate-50 dark:bg-slate-950 min-h-screen">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">
                


                <!-- SUMMARY & INPUT CARDS -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Input Transaksi</p>
                        <div class="flex gap-2">
                            <button @click="openAddModal('Income')" class="flex-1 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition-colors shadow-sm">Pemasukan</button>
                            <button @click="openAddModal('Expense')" class="flex-1 py-2 bg-rose-500 hover:bg-rose-600 text-white rounded-xl text-xs font-bold transition-colors shadow-sm">Pengeluaran</button>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Pemasukan</p>
                        <p class="text-lg font-black text-emerald-500">{{ maskValue(IDR.format(totals.income)) }}</p>
                    </div>
                    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Pengeluaran</p>
                        <p class="text-lg font-black text-rose-500">{{ maskValue(IDR.format(totals.expense)) }}</p>
                    </div>
                    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Sisa Cashflow</p>
                        <p class="text-lg font-black text-royal-600 dark:text-royal-400">{{ maskValue(IDR.format(totals.income - totals.expense)) }}</p>
                    </div>
                </div>

                <!-- STICKY FILTER BAR -->
                <div class="sticky top-2 z-30 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md p-3 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-lg flex flex-col md:flex-row gap-3">
                    <div class="relative flex-1">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                        <input v-model="searchQuery" type="text" placeholder="Cari transaksi..." class="w-full pl-10 pr-4 py-2 bg-transparent border-0 focus:ring-0 text-sm dark:text-white" />
                    </div>
                    <div class="flex items-center gap-2 overflow-x-auto pb-1 md:pb-0 no-scrollbar">
                        <select v-model="filterType" class="text-xs rounded-xl border-slate-200 dark:bg-slate-800 dark:border-slate-700 dark:text-white focus:ring-royal-500">
                            <option value="all">Semua Tipe</option>
                            <option value="income">Pemasukan</option>
                            <option value="expense">Pengeluaran</option>
                        </select>
                        <select v-model="filterWallet" class="text-xs rounded-xl border-slate-200 dark:bg-slate-800 dark:border-slate-700 dark:text-white focus:ring-royal-500">
                            <option value="all">Semua Dompet</option>
                            <option v-for="w in wallets" :key="w.id" :value="w.id">{{ w.name }}</option>
                        </select>
                        <div class="flex gap-1 ml-auto">
                            <button @click="exportToCSV" class="p-2 bg-slate-100 dark:bg-slate-800 rounded-xl hover:bg-slate-200 transition-colors"><Download class="w-4 h-4 text-slate-600 dark:text-slate-300" /></button>
                        </div>
                    </div>
                </div>

                <!-- TRANSACTION LIST (MOBILE & DESKTOP Optimized) -->
                <div class="space-y-8">
                    <div v-for="(trxs, date) in groupedTransactions" :key="date" class="space-y-3">
                        <div class="flex items-center gap-4">
                            <h4 class="text-xs font-black uppercase tracking-widest text-slate-400 whitespace-nowrap">{{ date }}</h4>
                            <div class="h-[1px] w-full bg-slate-200 dark:bg-slate-800"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            <div v-for="trx in trxs" :key="trx.id" class="group bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 hover:border-royal-300 dark:hover:border-royal-800 transition-all shadow-sm hover:shadow-md relative overflow-hidden">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-4">
                                        <div :class="[
                                            'w-12 h-12 rounded-2xl flex items-center justify-center transition-colors shadow-inner',
                                            trx.type === 'income' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20' : 'bg-rose-50 text-rose-600 dark:bg-rose-900/20'
                                        ]">
                                            <ArrowDownRight v-if="trx.type === 'income'" class="w-6 h-6" />
                                            <ArrowUpRight v-else class="w-6 h-6" />
                                        </div>
                                        <div>
                                            <p class="text-base font-black text-slate-800 dark:text-white line-clamp-1 group-hover:text-royal-600 transition-colors uppercase tracking-tight">{{ trx.text || trx.notes }}</p>
                                            <div class="flex items-center gap-3 mt-1">
                                                <span class="text-xs font-bold px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 capitalize">{{ trx.category }}</span>
                                                <span class="text-xs font-bold text-slate-400 flex items-center gap-1.5">
                                                    <Wallet class="w-3.5 h-3.5" /> {{ trx.wallet }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p :class="['text-xl font-black tracking-tighter', trx.type === 'income' ? 'text-emerald-500' : 'text-rose-600']">
                                            {{ trx.type === 'income' ? '+' : '' }}{{ maskValue(IDR.format(trx.amount)) }}
                                        </p>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ (trx.raw_date || trx.date).slice(11, 16) }}</p>
                                    </div>
                                </div>
                                
                                <!-- Hover Actions -->
                                <div class="flex justify-end gap-2 pt-2 border-t border-slate-50 dark:border-slate-800 mt-2">
                                    <button @click="openEditModal(trx)" class="p-2 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors">
                                        <Edit2 class="w-4 h-4" />
                                    </button>
                                    <button @click="deleteTransaction(trx.id)" class="p-2 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition-colors">
                                        <Trash2 class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="filteredTransactions.length === 0" class="py-20 text-center">
                        <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                            <Search class="w-8 h-8 text-slate-300" />
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Tidak ada transaksi</h3>
                        <p class="text-sm text-slate-400">Cukupi datamu atau ubah filternya</p>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="isManualModalOpen" class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-black/50" @click="isManualModalOpen = false"></div>
            <form @submit.prevent="submitManualAdd" class="relative bg-white dark:bg-slate-900 p-6 rounded-2xl w-full max-w-md shadow-2xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-black dark:text-white flex items-center gap-2">
                        <div :class="form.type === 'Income' ? 'bg-emerald-500' : 'bg-rose-500'" class="w-2 h-8 rounded-full"></div>
                        {{ editingTransactionId ? 'Edit' : 'Tambah' }} {{ form.type === 'Income' ? 'Pemasukan' : 'Pengeluaran' }}
                    </h3>
                    <button type="button" @click="isManualModalOpen = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <X class="w-6 h-6" />
                    </button>
                </div>

                <div class="space-y-4">

                    <div>
                        <InputLabel for="wallet">Dompet</InputLabel>
                        <select v-model="form.wallet_id" class="w-full mt-1 rounded-xl border-slate-200 dark:bg-slate-800 dark:border-slate-700 dark:text-white focus:ring-royal-500 focus:border-royal-500" required>
                            <option value="">Pilih Dompet</option>
                            <option v-for="w in wallets" :key="w.id" :value="w.id">{{ w.name }}</option>
                        </select>
                        <div v-if="form.errors.wallet_id" class="text-rose-500 text-xs mt-1">{{ form.errors.wallet_id }}</div>
                    </div>

                    <div>
                        <InputLabel for="category">Kategori</InputLabel>
                        <select v-model="form.category_id" class="w-full mt-1 rounded-xl border-slate-200 dark:bg-slate-800 dark:border-slate-700 dark:text-white focus:ring-royal-500 focus:border-royal-500" required>
                            <option value="">Pilih Kategori</option>
                            <option v-for="c in categories.filter(cat => cat.type.toLowerCase() === form.type.toLowerCase())" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                        <div v-if="form.errors.category_id" class="text-rose-500 text-xs mt-1">{{ form.errors.category_id }}</div>
                    </div>

                    <div>
                        <InputLabel for="amount">Nominal</InputLabel>
                        <input v-model="form.amount" type="number" placeholder="0" class="w-full mt-1 rounded-xl border-slate-200 dark:bg-slate-800 dark:border-slate-700 dark:text-white focus:ring-royal-500 focus:border-royal-500" required />
                        <div v-if="form.errors.amount" class="text-rose-500 text-xs mt-1">{{ form.errors.amount }}</div>
                    </div>

                    <div>
                        <InputLabel for="date">Tanggal & Jam</InputLabel>
                        <input v-model="form.date" type="datetime-local" class="w-full mt-1 rounded-xl border-slate-200 dark:bg-slate-800 dark:border-slate-700 dark:text-white focus:ring-royal-500 focus:border-royal-500" required />
                        <div v-if="form.errors.date" class="text-rose-500 text-xs mt-1">{{ form.errors.date }}</div>
                    </div>

                    <div>
                        <InputLabel for="notes">Keterangan (Opsional)</InputLabel>
                        <input v-model="form.notes" type="text" placeholder="Misal: Makan malam bersama keluarga" class="w-full mt-1 rounded-xl border-slate-200 dark:bg-slate-800 dark:border-slate-700 dark:text-white focus:ring-royal-500 focus:border-royal-500" />
                        <div v-if="form.errors.notes" class="text-rose-500 text-xs mt-1">{{ form.errors.notes }}</div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" @click="isManualModalOpen = false" class="px-4 py-2">Batal</button>
                    <PrimaryButton type="submit">Simpan</PrimaryButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>