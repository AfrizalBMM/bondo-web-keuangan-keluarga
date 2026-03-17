<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { useVisibility } from '@/Composables/useVisibility';
import { ref } from 'vue';
import { Sparkles, Search, Filter, Download, Plus, Edit2, Trash2, ArrowUpRight, ArrowDownRight, X, Wallet, Tags, CircleDollarSign, Calendar, FileText } from 'lucide-vue-next';
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

// Format Currency
const IDR = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
});

const smartInput = ref('');
const isAnalyzing = ref(false);
const isManualModalOpen = ref(false);

const form = useForm({
    type: 'Expense',
    wallet_id: '',
    category_id: '',
    amount: '',
    date: new Date().toISOString().slice(0, 10),
    notes: ''
});

const submitSmartAdd = () => {
    if(!smartInput.value) return;
    isAnalyzing.value = true;
    
    router.post(route('transactions.smart'), {
        smart_input: smartInput.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            smartInput.value = '';
            isAnalyzing.value = false;
        },
        onError: () => {
            isAnalyzing.value = false;
        }
    });
};

const submitManualAdd = () => {
    form.post(route('transactions.store'), {
        preserveScroll: true,
        onSuccess: () => {
            isManualModalOpen.value = false;
            form.reset();
        }
    });
};

</script>

<template>
    <AuthenticatedLayout>
        <Head title="Transaksi" />

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-8">
                
                <!-- Header & Smart Add Section -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-2">
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900 dark:text-slate-50">Transaksi</h1>
                        <p class="mt-1 text-slate-500 dark:text-slate-400">Pencatatan keuangan cerdas bertenaga AI.</p>
                    </div>
                </div>

                <!-- Smart Input Bar -->
                <div class="relative max-w-3xl mx-auto z-10">
                    <div class="absolute -inset-1 rounded-2xl bg-gradient-to-r from-royal-500 via-indigo-500 to-purple-500 opacity-30 blur-lg dark:opacity-40"></div>
                    <form @submit.prevent="submitSmartAdd" class="relative glass-card flex items-center p-2 rounded-2xl overflow-hidden bg-white/90 dark:bg-slate-900/90 border-royal-200 dark:border-royal-900/50">
                        <div class="pl-4 pr-3 text-royal-500 dark:text-royal-400 animate-pulse" v-if="isAnalyzing">
                            <Sparkles class="w-6 h-6" />
                        </div>
                        <div class="pl-4 pr-3 text-slate-400 dark:text-slate-500" v-else>
                            <Sparkles class="w-6 h-6" />
                        </div>
                        
                        <input 
                            v-model="smartInput"
                            type="text" 
                            :disabled="isAnalyzing"
                            placeholder="Ketik 'Beli cilok 5rb di gopay' lalu Enter..." 
                            class="flex-1 bg-transparent border-0 focus:ring-0 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 text-lg w-full px-2"
                        />
                        
                        <button type="submit" :disabled="isAnalyzing || !smartInput" class="hidden sm:flex items-center px-6 py-3 bg-slate-900 dark:bg-royal-600 text-white rounded-xl font-medium hover:opacity-90 transition disabled:opacity-50">
                            {{ isAnalyzing ? 'Menganalisis...' : 'Simpan Cepat' }}
                        </button>
                    </form>
                    <div v-if="errors.smart_input" class="text-rose-500 font-medium text-sm mt-3 ml-2 text-center absolute -bottom-6 left-0 right-0">
                        {{ errors.smart_input }}
                    </div>
                </div>

                <!-- Filters & Table Actions -->
                <div class="glass flex flex-col sm:flex-row justify-between items-center p-4 rounded-xl gap-4">
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <div class="relative flex-1 sm:w-64">
                            <Search class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400" />
                            <input type="text" placeholder="Cari transaksi..." class="w-full pl-9 pr-3 py-2 text-sm border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-800 focus:ring-royal-500 focus:border-royal-500 dark:text-slate-200 transition-colors" />
                        </div>
                        <button class="p-2 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                            <Filter class="w-5 h-5" />
                        </button>
                    </div>
                    
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <button class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                            <Download class="w-4 h-4" />
                            Export
                        </button>
                        <PrimaryButton @click="isManualModalOpen = true" class="flex-1 sm:flex-none bg-royal-600 hover:bg-royal-700 flex justify-center items-center gap-2">
                            <Plus class="w-4 h-4" />
                            Manual Input
                        </PrimaryButton>
                    </div>
                </div>

                <!-- Transaction Table -->
                <div class="glass rounded-xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    <th class="px-6 py-4 font-medium">Transaksi</th>
                                    <th class="px-6 py-4 font-medium">Kategori</th>
                                    <th class="px-6 py-4 font-medium">Sumber Dompet</th>
                                    <th class="px-6 py-4 font-medium text-right">Jumlah</th>
                                    <th class="px-6 py-4 font-medium text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="trx in transactions" :key="trx.id" class="border-b border-slate-100 dark:border-slate-700/50 last:border-0 hover:bg-slate-50/30 dark:hover:bg-slate-800/30 transition">
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ trx.text }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ trx.date }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                            {{ trx.category }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-slate-600 dark:text-slate-300 flex items-center gap-2">
                                            <div :class="['w-2 h-2 rounded-full', trx.wallet_color]"></div>
                                            {{ trx.wallet }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end font-bold text-sm">
                                            <span v-if="trx.type === 'income'" class="text-emerald-600 dark:text-emerald-400 flex items-center">
                                                <ArrowDownRight class="w-4 h-4 mr-1"/>
                                                +{{ maskValue(IDR.format(trx.amount)) }}
                                            </span>
                                            <span v-else class="text-slate-800 dark:text-slate-200 flex items-center">
                                                <ArrowUpRight class="w-4 h-4 mr-1 text-rose-500"/>
                                                -{{ maskValue(IDR.format(trx.amount)) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button class="p-1.5 text-slate-400 hover:text-royal-600 dark:hover:text-royal-400 transition" title="Edit">
                                                <Edit2 class="w-4 h-4" />
                                            </button>
                                            <button class="p-1.5 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 transition" title="Hapus">
                                                <Trash2 class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <!-- Empty State -->
                        <div v-if="transactions.length === 0" class="p-12 text-center text-slate-500 dark:text-slate-400">
                            <p>Belum ada transaksi.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Manual Add Transaction Modal -->
        <div v-if="isManualModalOpen" class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="isManualModalOpen = false"></div>
            
            <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/80">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Input Transaksi Manual</h3>
                    <button @click="isManualModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                        <X class="w-5 h-5" />
                    </button>
                </div>
                
                <form @submit.prevent="submitManualAdd" class="p-6">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <button type="button" @click="form.type = 'Expense'" :class="['py-2 text-sm font-semibold rounded-lg border text-center transition', form.type === 'Expense' ? 'border-rose-500 text-rose-600 bg-rose-50 dark:bg-rose-900/20' : 'border-slate-200 text-slate-500 hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-700']">
                            Pengeluaran
                        </button>
                        <button type="button" @click="form.type = 'Income'" :class="['py-2 text-sm font-semibold rounded-lg border text-center transition', form.type === 'Income' ? 'border-emerald-500 text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20' : 'border-slate-200 text-slate-500 hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-700']">
                            Pemasukan
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="wallet" class="flex items-center gap-1.5"><Wallet class="w-4 h-4 text-slate-400" /> Sumber Dompet</InputLabel>
                                <select id="wallet" v-model="form.wallet_id" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 shadow-sm focus:border-royal-500 focus:ring-royal-500" required>
                                    <option value="" disabled selected>Pilih Dompet...</option>
                                    <option v-for="wallet in wallets" :key="wallet.id" :value="wallet.id">{{ wallet.name }}</option>
                                </select>
                            </div>
                            <div>
                                <InputLabel for="category" class="flex items-center gap-1.5"><Tags class="w-4 h-4 text-slate-400" /> Kategori</InputLabel>
                                <select id="category" v-model="form.category_id" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 shadow-sm focus:border-royal-500 focus:ring-royal-500" required>
                                    <option value="" disabled selected>Pilih Kategori...</option>
                                    <option v-for="cat in categories.filter(c => c.type === form.type)" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <InputLabel for="amount" class="flex items-center gap-1.5"><CircleDollarSign class="w-4 h-4 text-slate-400" /> Nominal (Rp)</InputLabel>
                            <div class="relative mt-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-slate-500 sm:text-sm">Rp</span>
                                </div>
                                <input type="number" id="amount" v-model="form.amount" class="block w-full pl-9 font-semibold text-lg rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-royal-500 focus:ring-royal-500" placeholder="0" required />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="date" class="flex items-center gap-1.5"><Calendar class="w-4 h-4 text-slate-400" /> Tanggal</InputLabel>
                                <TextInput id="date" v-model="form.date" type="date" class="mt-1 block w-full" required />
                            </div>
                            <div>
                                <InputLabel for="notes" class="flex items-center gap-1.5"><FileText class="w-4 h-4 text-slate-400" /> Catatan (Ops.)</InputLabel>
                                <TextInput id="notes" v-model="form.notes" type="text" class="mt-1 block w-full" placeholder="Keterangan..." />
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3 text-sm">
                        <button type="button" @click="isManualModalOpen = false" class="px-4 py-2 font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-md transition-colors">Batal</button>
                        <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="bg-royal-600 hover:bg-royal-700 shadow-sm">Simpan Transaksi</PrimaryButton>
                    </div>
                </form>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
