<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { useVisibility } from '@/Composables/useVisibility';
import { useConfirm } from '@/Composables/useConfirm';
import { ref, computed } from 'vue';
import { Plus, ArrowDownRight, ArrowUpRight, Clock, CheckCircle2, AlertTriangle, AlertCircle, X, Check, User, CircleDollarSign, CalendarDays, Wallet, Edit2, Trash2 } from 'lucide-vue-next';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps({
    debts: Array,
    wallets: Array,
});

const { maskValue } = useVisibility();
const { confirm: confirmModal } = useConfirm();

// Format Currency
const IDR = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
});

const isAddModalOpen = ref(false);
const isPaymentModalOpen = ref(false);
const activeTab = ref('Hutang'); // 'Hutang' or 'Piutang'
const activeDebt = ref(null);
const editingDebtId = ref(null);

const form = useForm({
    type: 'Hutang', // Hutang (Obligation to pay), Piutang (Right to receive)
    counterparty: '',
    totalAmount: '',
    dueDate: '',
    notes: ''
});

const paymentForm = useForm({
    amount: '',
    wallet_id: ''
});

const displayedDebts = computed(() => {
    return props.debts.filter(d => d.type === activeTab.value).sort((a,b) => {
        // completed items go to bottom
        if (a.status === 'completed' && b.status !== 'completed') return 1;
        if (a.status !== 'completed' && b.status === 'completed') return -1;
        return new Date(a.dueDate) - new Date(b.dueDate);
    });
});

const getPercentage = (paid, total) => {
    const percent = (paid / total) * 100;
    return percent > 100 ? 100 : Math.round(percent);
};

// Summary metrics
const totalHutang = computed(() => {
    return props.debts.filter(d => d.type === 'Hutang' && d.status !== 'completed').reduce((sum, item) => sum + (item.totalAmount - item.paidAmount), 0);
});

const totalPiutang = computed(() => {
    return props.debts.filter(d => d.type === 'Piutang' && d.status !== 'completed').reduce((sum, item) => sum + (item.totalAmount - item.paidAmount), 0);
});

const openAddModal = (type = null) => {
    editingDebtId.value = null;
    if(type) activeTab.value = type;
    form.reset();
    form.clearErrors();
    form.type = activeTab.value;
    isAddModalOpen.value = true;
};

const openEditModal = (debt) => {
    editingDebtId.value = debt.id;
    form.type = debt.type;
    form.counterparty = debt.counterparty;
    form.totalAmount = debt.totalAmount;
    form.dueDate = debt.dueDate;
    form.notes = debt.notes || '';
    form.clearErrors();
    isAddModalOpen.value = true;
};

const deleteDebt = (id) => {
    confirmModal({
        title: 'Hapus Catatan?',
        message: 'Apakah Anda yakin ingin menghapus catatan ini beserta seluruh riwayat pembayarannya?',
        confirmText: 'Ya, Hapus',
        onConfirm: () => {
            router.delete(route('debts.destroy', id), {
                preserveScroll: true
            });
        }
    });
};

const openPaymentModal = (debt) => {
    activeDebt.value = debt;
    paymentForm.amount = '';
    paymentForm.wallet_id = '';
    isPaymentModalOpen.value = true;
};

const submitAddDebt = () => {
    if (editingDebtId.value) {
        form.put(route('debts.update', editingDebtId.value), {
            preserveScroll: true,
            onSuccess: () => {
                isAddModalOpen.value = false;
                form.reset();
            }
        });
    } else {
        form.post(route('debts.store'), {
            preserveScroll: true,
            onSuccess: () => {
                isAddModalOpen.value = false;
                form.reset();
            }
        });
    }
};

const submitPayment = () => {
    if(!activeDebt.value) return;
    
    paymentForm.post(route('debts.payment', activeDebt.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            isPaymentModalOpen.value = false;
            paymentForm.reset();
            activeDebt.value = null;
        }
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Manajemen Hutang Piutang" />

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-8">
                
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-2">
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900 dark:text-slate-50">Hutang & Piutang</h1>
                        <p class="mt-1 text-slate-500 dark:text-slate-400">Pantau dan kelola kewajiban pembayaran serta dana yang dipinjamkan.</p>
                    </div>
                </div>

                <!-- Metrics Summary -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="glass flex items-center gap-6 px-6 py-5 rounded-2xl border-l-4 border-l-rose-500 border border-slate-200 dark:border-slate-700">
                        <div class="p-3 bg-rose-50 dark:bg-rose-900/30 rounded-full text-rose-600 dark:text-rose-400">
                            <ArrowUpRight class="w-8 h-8" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Sisa Hutang Anda</p>
                            <p class="text-2xl font-bold text-rose-600 dark:text-rose-400">{{ maskValue(IDR.format(totalHutang)) }}</p>
                        </div>
                    </div>
                    <div class="glass flex items-center gap-6 px-6 py-5 rounded-2xl border-l-4 border-l-emerald-500 border border-slate-200 dark:border-slate-700">
                        <div class="p-3 bg-emerald-50 dark:bg-emerald-900/30 rounded-full text-emerald-600 dark:text-emerald-400">
                            <ArrowDownRight class="w-8 h-8" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Sisa Piutang (Uang Anda di luar)</p>
                            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ maskValue(IDR.format(totalPiutang)) }}</p>
                        </div>
                    </div>
                </div>

                <div class="glass-card overflow-hidden">
                    <div class="flex flex-col sm:flex-row border-b border-slate-200 dark:border-slate-700">
                        <button 
                            @click="activeTab = 'Hutang'" 
                            :class="['flex-1 flex justify-center items-center gap-2 py-4 font-semibold text-sm transition-colors border-b-2', activeTab === 'Hutang' ? 'border-rose-500 text-rose-600 dark:text-rose-400 bg-rose-50/50 dark:bg-rose-900/10' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300']">
                            <ArrowUpRight class="w-5 h-5" />
                            Hutang (Yang Harus Dibayar)
                        </button>
                        <button 
                            @click="activeTab = 'Piutang'" 
                            :class="['flex-1 flex justify-center items-center gap-2 py-4 font-semibold text-sm transition-colors border-b-2', activeTab === 'Piutang' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 bg-emerald-50/50 dark:bg-emerald-900/10' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300']">
                            <ArrowDownRight class="w-5 h-5" />
                            Piutang (Yang Akan Diterima)
                        </button>
                    </div>
                    
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                                Daftar {{ activeTab }} Aktif
                            </h2>
                            <PrimaryButton @click="openAddModal()" class="bg-royal-600 hover:bg-royal-700 flex items-center gap-2 text-sm px-3 py-1.5">
                                <Plus class="w-4 h-4" />
                                <span class="hidden sm:inline">Catat {{ activeTab }}</span>
                            </PrimaryButton>
                        </div>

                        <div class="space-y-4">
                            <!-- Cards for each debt item -->
                            <div v-for="item in displayedDebts" :key="item.id" 
                                :class="['p-5 rounded-2xl border transition-all', item.status === 'completed' ? 'bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-800 opacity-60' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md']">
                                
                                <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
                                    <div class="flex-grow">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h3 class="font-bold text-lg text-slate-800 dark:text-slate-100">{{ item.counterparty }}</h3>
                                            <div v-if="item.status === 'completed'" class="flex items-center gap-1 text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/40 px-2 py-0.5 rounded">
                                                <CheckCircle2 class="w-3 h-3" /> Lunas
                                            </div>
                                            <div v-else-if="item.status === 'warning'" class="flex items-center gap-1 text-xs font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/40 px-2 py-0.5 rounded">
                                                <AlertTriangle class="w-3 h-3" /> Mendekati Tempo
                                            </div>
                                            <div class="ml-auto flex gap-1">
                                                <button @click="openEditModal(item)" class="p-1.5 text-slate-400 hover:text-royal-600 dark:hover:text-royal-400 transition" title="Edit">
                                                    <Edit2 class="w-4 h-4" />
                                                </button>
                                                <button @click="deleteDebt(item.id)" class="p-1.5 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 transition" title="Hapus">
                                                    <Trash2 class="w-4 h-4" />
                                                </button>
                                            </div>
                                        </div>
                                        <div class="flex items-center text-sm text-slate-500 dark:text-slate-400 gap-4">
                                            <span class="flex items-center gap-1">
                                                <AlertCircle class="w-4 h-4" />
                                                Total {{ activeTab === 'Hutang' ? 'Pinjaman' : 'Dipinjamkan' }}: {{ IDR.format(item.totalAmount) }}
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <Clock class="w-4 h-4" />
                                                Jatuh Tempo: {{ new Date(item.dueDate).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex flex-col items-start md:items-end min-w-[200px]">
                                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Sisa {{ activeTab === 'Hutang' ? 'Tagihan' : 'Belum Ditagih' }}</p>
                                        <p :class="['text-xl font-bold tracking-tight mb-2', item.status === 'completed' ? 'text-slate-500' : (activeTab === 'Hutang' ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400')]">
                                            {{ maskValue(IDR.format(item.totalAmount - item.paidAmount)) }}
                                        </p>
                                        
                                        <!-- Progress Bar -->
                                        <div class="w-full flex items-center justify-between text-xs text-slate-500 mb-1">
                                            <span>Terbayar {{ getPercentage(item.paidAmount, item.totalAmount) }}%</span>
                                            <span>{{ maskValue(IDR.format(item.paidAmount)) }}</span>
                                        </div>
                                        <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2 overflow-hidden">
                                            <div :class="['h-2 rounded-full transition-all duration-500', activeTab === 'Hutang' ? 'bg-amber-500' : 'bg-blue-500', item.status === 'completed' ? 'bg-emerald-500' : '']" :style="{ width: getPercentage(item.paidAmount, item.totalAmount) + '%' }"></div>
                                        </div>
                                    </div>

                                    <div class="mt-4 md:mt-0 md:ml-4 flex gap-2">
                                        <button v-if="item.status !== 'completed'" @click="openPaymentModal(item)" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium transition shadow-sm border border-slate-200 dark:border-slate-700">
                                            <Check class="w-4 h-4" />
                                            {{ activeTab === 'Hutang' ? 'Bayar Cicilan' : 'Terima Cicilan' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div v-if="displayedDebts.length === 0" class="text-center py-12">
                                <div class="w-16 h-16 rounded-full bg-slate-50 dark:bg-slate-800/50 flex flex-col items-center justify-center mx-auto mb-4 text-slate-400">
                                    <CheckCircle2 class="w-8 h-8" />
                                </div>
                                <h3 class="text-lg font-medium text-slate-900 dark:text-slate-100">Bebas {{ activeTab }}!</h3>
                                <p class="text-slate-500 mt-1">Anda tidak memiliki catatan {{ activeTab.toLowerCase() }} saat ini.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Add Debt Modal -->
        <div v-if="isAddModalOpen" class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="isAddModalOpen = false"></div>
            
            <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/80">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ editingDebtId ? 'Edit Catatan ' + form.type : 'Catat ' + form.type + ' Baru' }}</h3>
                    <button @click="isAddModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                        <X class="w-5 h-5" />
                    </button>
                </div>
                
                <form @submit.prevent="submitAddDebt" class="p-6">
                    <div class="space-y-4">
                        <div>
                            <InputLabel for="counterparty" class="flex items-center gap-1.5">
                                <User class="w-4 h-4 text-slate-400" />
                                {{ form.type === 'Hutang' ? 'Pemberi Pinjaman / Kreditor' : 'Peminjam / Debitur' }}
                            </InputLabel>
                            <TextInput id="counterparty" v-model="form.counterparty" type="text" class="mt-1 block w-full" placeholder="Cth: Bank BNI atau Saudara X" required autofocus />
                            <div v-if="form.errors.counterparty" class="text-sm text-red-600 mt-1">{{ form.errors.counterparty }}</div>
                        </div>
                        
                        <div>
                            <InputLabel for="total_amount" class="flex items-center gap-1.5"><CircleDollarSign class="w-4 h-4 text-slate-400" /> Total Nominal (Rp)</InputLabel>
                            <TextInput id="total_amount" v-model="form.totalAmount" type="number" class="mt-1 block w-full font-semibold text-lg" placeholder="0" required />
                            <div v-if="form.errors.totalAmount" class="text-sm text-red-600 mt-1">{{ form.errors.totalAmount }}</div>
                        </div>
                        
                        <div>
                            <InputLabel for="due_date" class="flex items-center gap-1.5"><CalendarDays class="w-4 h-4 text-slate-400" /> Jatuh Tempo Penuh</InputLabel>
                            <TextInput id="due_date" v-model="form.dueDate" type="date" class="mt-1 block w-full" required />
                            <div v-if="form.errors.dueDate" class="text-sm text-red-600 mt-1">{{ form.errors.dueDate }}</div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3 text-sm">
                        <button type="button" @click="isAddModalOpen = false" class="px-4 py-2 font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-md transition-colors">Batal</button>
                        <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="bg-royal-600 hover:bg-royal-700 shadow-sm">{{ editingDebtId ? 'Simpan Perubahan' : 'Simpan Catatan' }}</PrimaryButton>
                    </div>
                </form>
            </div>
        </div>

        <!-- Payment Modal -->
        <div v-if="isPaymentModalOpen" class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="isPaymentModalOpen = false"></div>
            
            <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden transform transition-all">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/80">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Pencatatan Cicilan</h3>
                    <button @click="isPaymentModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                        <X class="w-5 h-5" />
                    </button>
                </div>
                
                <form @submit.prevent="submitPayment" class="p-6">
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                        Masukkan nominal cicilan yang {{ activeDebt?.type === 'Hutang' ? 'dibayarkan ke' : 'diterima dari' }} <strong>{{ activeDebt?.counterparty }}</strong>.
                    </p>
                    <div class="p-3 bg-slate-50 dark:bg-slate-900 rounded-lg mb-4 text-sm text-center border border-slate-200 dark:border-slate-700 shadow-inner">
                        <span class="text-slate-500">Sisa Kewajiban:</span>
                        <div class="font-bold text-xl mt-1 text-slate-800 dark:text-slate-200">
                            {{ activeDebt ? IDR.format(activeDebt.totalAmount - activeDebt.paidAmount) : '0' }}
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <InputLabel for="payment_amount" class="flex items-center gap-1.5"><CircleDollarSign class="w-4 h-4 text-slate-400" /> Nominal Cicilan Baru (Rp)</InputLabel>
                            <TextInput id="payment_amount" v-model="paymentForm.amount" type="number" class="mt-1 block w-full text-lg font-semibold" placeholder="0" required autofocus />
                            <div v-if="paymentForm.errors.amount" class="text-sm text-red-600 mt-1">{{ paymentForm.errors.amount }}</div>
                        </div>

                        <div>
                            <InputLabel for="wallet" class="flex items-center gap-1.5"><Wallet class="w-4 h-4 text-slate-400" /> Gunakan Dompet</InputLabel>
                            <select id="wallet" v-model="paymentForm.wallet_id" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 shadow-sm focus:border-royal-500 focus:ring-royal-500" required>
                                <option value="" disabled selected>Pilih Dompet...</option>
                                <option v-for="wallet in wallets" :key="wallet.id" :value="wallet.id">{{ wallet.name }} (Sisa: {{ IDR.format(wallet.balance) }})</option>
                            </select>
                            <div v-if="paymentForm.errors.wallet_id" class="text-sm text-red-600 mt-1">{{ paymentForm.errors.wallet_id }}</div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <PrimaryButton type="submit" :class="{ 'opacity-25': paymentForm.processing }" :disabled="paymentForm.processing" class="w-full justify-center bg-royal-600 hover:bg-royal-700 py-3 shadow-sm">Konfirmasi Nominal</PrimaryButton>
                    </div>
                </form>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
