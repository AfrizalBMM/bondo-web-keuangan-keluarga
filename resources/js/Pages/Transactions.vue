<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { useVisibility } from '@/Composables/useVisibility';
import { ref, computed } from 'vue';
import { 
    Sparkles, Search, Filter, Download, Plus, Edit2, Trash2, 
    ArrowUpRight, ArrowDownRight, X, Wallet, Tags, 
    CircleDollarSign, Calendar, FileText, Loader2
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

// --- STATE FILTER ---
const searchQuery = ref('');
const filterType = ref('all'); 
const filterWallet = ref('all');
const filterDateRange = ref('all');

// --- LOGIKA FILTERING (DIPERBAIKI) ---
const filteredTransactions = computed(() => {
    // Jika props.transactions kosong/undefined, kembalikan array kosong
    if (!props.transactions) return [];

    return props.transactions.filter(trx => {
        // Ambil nilai keterangan (text/notes/description)
        const content = (trx.text || trx.notes || trx.description || '').toLowerCase();
        const categoryName = (trx.category || '').toLowerCase();
        const searchTerm = searchQuery.value.toLowerCase();

        // 1. Logika Pencarian
        const matchesSearch = content.includes(searchTerm) || categoryName.includes(searchTerm);
        
        // 2. Logika Tipe (Income/Expense)
        const trxType = (trx.type || '').toLowerCase();
        const matchesType = filterType.value === 'all' || trxType === filterType.value.toLowerCase();
        
        // 3. Logika Dompet
        const trxWalletId = String(trx.raw_wallet_id || trx.wallet_id || '');
        const matchesWallet = filterWallet.value === 'all' || trxWalletId === String(filterWallet.value);

        // 4. Logika Tanggal
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
const smartInput = ref('');
const isAnalyzing = ref(false);
const isManualModalOpen = ref(false);
const editingTransactionId = ref(null);

const form = useForm({
    type: 'Expense', wallet_id: '', category_id: '', amount: '', date: new Date().toISOString().slice(0, 16), notes: ''
});

const submitSmartAdd = () => {
    if(!smartInput.value) return;
    isAnalyzing.value = true;
    router.post(route('transactions.smart'), { smart_input: smartInput.value }, {
        onSuccess: () => { smartInput.value = ''; isAnalyzing.value = false; },
        onError: () => isAnalyzing.value = false
    });
};

const openAddModal = () => {
    editingTransactionId.value = null;
    form.reset();
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
    if (confirm('Hapus transaksi ini?')) router.delete(route('transactions.destroy', id));
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Transaksi" />
        <div class="py-12 bg-slate-50 dark:bg-slate-950 min-h-screen">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">
                
                <div class="max-w-3xl mx-auto">
                    <form @submit.prevent="submitSmartAdd" class="flex items-center p-2 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl">
                        <div class="px-4"><Sparkles v-if="!isAnalyzing" class="w-6 h-6 text-royal-500" /><Loader2 v-else class="w-6 h-6 animate-spin text-royal-500" /></div>
                        <input v-model="smartInput" type="text" placeholder="Beli bakso 15rb di gopay..." class="flex-1 bg-transparent border-0 focus:ring-0 dark:text-white" />
                        <button type="submit" class="bg-royal-600 text-white px-6 py-2 rounded-xl font-bold">Simpan</button>
                    </form>
                </div>

                <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-wrap gap-4 items-center">
                    <div class="relative flex-1 min-w-[200px]">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                        <input v-model="searchQuery" type="text" placeholder="Cari keterangan..." class="w-full pl-10 pr-4 py-2 rounded-xl border-slate-200 dark:bg-slate-800 dark:border-slate-700 dark:text-white" />
                    </div>

                    <select v-model="filterType" class="rounded-xl border-slate-200 dark:bg-slate-800 dark:text-white">
                        <option value="all">Semua Tipe</option>
                        <option value="income">Pemasukan</option>
                        <option value="expense">Pengeluaran</option>
                    </select>

                    <select v-model="filterWallet" class="rounded-xl border-slate-200 dark:bg-slate-800 dark:text-white">
                        <option value="all">Semua Dompet</option>
                        <option v-for="w in wallets" :key="w.id" :value="w.id">{{ w.name }}</option>
                    </select>

                    <button @click="exportToCSV" class="p-2 border rounded-xl hover:bg-slate-100 dark:border-slate-700 dark:text-white dark:hover:bg-slate-800"><Download class="w-5 h-5" /></button>
                    <PrimaryButton @click="openAddModal" class="bg-royal-600"><Plus class="w-4 h-4 mr-1" /> Tambah</PrimaryButton>
                </div>

                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 dark:bg-slate-800 text-slate-500 font-bold text-xs uppercase">
                            <tr>
                                <th class="px-6 py-4">Keterangan</th>
                                <th class="px-6 py-4">Kategori</th>
                                <th class="px-6 py-4 text-right">Jumlah</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr v-for="trx in filteredTransactions" :key="trx.id" class="dark:text-slate-300">
                                <td class="px-6 py-4">
                                    <div class="font-bold">{{ trx.text || trx.notes }}</div>
                                    <div class="text-xs text-slate-400">{{ trx.date }} • {{ trx.wallet }}</div>
                                </td>
                                <td class="px-6 py-4"><span class="px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded text-xs">{{ trx.category }}</span></td>
                                <td class="px-6 py-4 text-right font-black" :class="trx.type === 'income' ? 'text-emerald-500' : 'text-slate-900 dark:text-white'">
                                    {{ maskValue(IDR.format(trx.amount)) }}
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button @click="openEditModal(trx)" class="text-blue-500"><Edit2 class="w-4 h-4" /></button>
                                    <button @click="deleteTransaction(trx.id)" class="text-rose-500"><Trash2 class="w-4 h-4" /></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div v-if="isManualModalOpen" class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-black/50" @click="isManualModalOpen = false"></div>
            <form @submit.prevent="submitManualAdd" class="relative bg-white dark:bg-slate-900 p-6 rounded-2xl w-full max-w-md shadow-2xl">
                <h3 class="text-xl font-bold mb-4 dark:text-white">{{ editingTransactionId ? 'Edit' : 'Tambah' }} Transaksi</h3>
                <div class="space-y-4">
                    <div class="flex gap-2">
                        <button type="button" @click="form.type = 'Expense'" :class="form.type === 'Expense' ? 'bg-rose-500 text-white' : 'bg-slate-100'" class="flex-1 py-2 rounded-lg font-bold">Keluar</button>
                        <button type="button" @click="form.type = 'Income'" :class="form.type === 'Income' ? 'bg-emerald-500 text-white' : 'bg-slate-100'" class="flex-1 py-2 rounded-lg font-bold">Masuk</button>
                    </div>
                    <select v-model="form.wallet_id" class="w-full rounded-lg dark:bg-slate-800 dark:text-white" required>
                        <option value="">Pilih Dompet</option>
                        <option v-for="w in wallets" :key="w.id" :value="w.id">{{ w.name }}</option>
                    </select>
                    <select v-model="form.category_id" class="w-full rounded-lg dark:bg-slate-800 dark:text-white" required>
                        <option value="">Pilih Kategori</option>
                        <option v-for="c in categories.filter(cat => cat.type === form.type)" :key="c.id" :value="c.id">{{ c.name }}</option>
                    </select>
                    <input v-model="form.amount" type="number" placeholder="Nominal" class="w-full rounded-lg dark:bg-slate-800 dark:text-white" required />
                    <input v-model="form.notes" type="text" placeholder="Keterangan" class="w-full rounded-lg dark:bg-slate-800 dark:text-white" />
                </div>
                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" @click="isManualModalOpen = false" class="px-4 py-2">Batal</button>
                    <PrimaryButton type="submit">Simpan</PrimaryButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>