<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { useVisibility } from '@/Composables/useVisibility';
import { useConfirm } from '@/Composables/useConfirm';
import { ref, computed } from 'vue';
import { Plus, Target, AlertTriangle, AlertCircle, X, Check, PieChart, Tags, CircleDollarSign, Edit2, Trash2 } from 'lucide-vue-next';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps({
    budgets: Array,
    categories: Array,
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
const editingBudgetId = ref(null);

const form = useForm({
    category_id: '',
    limitAmount: '',
});

const getPercentage = (spent, limit) => {
    if (!limit || limit == 0) return 0;
    const percent = (spent / limit) * 100;
    return percent; // allows going > 100
};

// Determines progress bar color based on spending logic (Hijau -> Kuning -> Merah)
const getProgressColor = (percent) => {
    if (percent < 50) return 'bg-emerald-500';
    if (percent < 80) return 'bg-amber-400';
    if (percent <= 100) return 'bg-rose-500';
    return 'bg-rose-600'; // Over limit
};

const getStatusText = (percent) => {
    if (percent < 50) return { text: 'Aman', class: 'text-emerald-600 bg-emerald-50 border-emerald-200 dark:text-emerald-400 dark:bg-emerald-900/30' };
    if (percent < 80) return { text: 'Waspada', class: 'text-amber-600 bg-amber-50 border-amber-200 dark:text-amber-400 dark:bg-amber-900/30' };
    if (percent <= 100) return { text: 'Hampir Habis', class: 'text-rose-600 bg-rose-50 border-rose-200 dark:text-rose-400 dark:bg-rose-900/30' };
    return { text: 'Over Budget', class: 'text-white bg-rose-600 border-rose-700 animate-pulse' };
};

const totalBudget = computed(() => props.budgets.reduce((acc, curr) => acc + parseFloat(curr.limitAmount || 0), 0));
const totalSpent = computed(() => props.budgets.reduce((acc, curr) => acc + parseFloat(curr.spentAmount || 0), 0));

const openAddModal = () => {
    editingBudgetId.value = null;
    form.reset();
    form.clearErrors();
    isAddModalOpen.value = true;
};

const openEditModal = (budget) => {
    editingBudgetId.value = budget.id;
    form.category_id = budget.category_id;
    form.limitAmount = budget.limitAmount;
    form.clearErrors();
    isAddModalOpen.value = true;
};

const deleteBudget = (id) => {
    confirmModal({
        title: 'Hapus Anggaran?',
        message: 'Apakah Anda yakin ingin menghapus anggaran untuk kategori ini?',
        confirmText: 'Ya, Hapus',
        onConfirm: () => {
            router.delete(route('budget.destroy', id), {
                preserveScroll: true
            });
        }
    });
};

const submitAddBudget = () => {
    form.post(route('budget.store'), {
        preserveScroll: true,
        onSuccess: () => {
            isAddModalOpen.value = false;
            form.reset();
        }
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Budgeting" />

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-8">
                
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-2">
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900 dark:text-slate-50">Anggaran Bulanan</h1>
                        <p class="mt-1 text-slate-500 dark:text-slate-400">Atur batas pengeluaran untuk setiap kategori keuangan Anda.</p>
                    </div>
                </div>

                <!-- Overall Budget Summary Dashboard -->
                <div class="glass p-6 rounded-2xl flex flex-col md:flex-row gap-8 items-center border border-slate-200 dark:border-slate-700">
                    <div class="flex-1 w-full relative pt-4">
                        <div class="flex justify-between items-end mb-2">
                            <div>
                                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Pengeluaran Bulan Ini</p>
                                <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ maskValue(IDR.format(totalSpent)) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Anggaran</p>
                                <p class="text-lg font-semibold text-royal-600 dark:text-royal-400">{{ maskValue(IDR.format(totalBudget)) }}</p>
                            </div>
                        </div>
                        
                        <!-- Main Progress -->
                        <div class="w-full bg-slate-200 dark:bg-slate-800 rounded-full h-4 overflow-hidden mt-4 relative">
                            <div :class="['h-full rounded-full transition-all duration-1000', getProgressColor((totalSpent / totalBudget) * 100)]" :style="{ width: ((totalSpent / totalBudget) * 100 > 100 ? 100 : (totalSpent / totalBudget) * 100) + '%' }"></div>
                        </div>
                        <p class="text-right text-xs mt-2 text-slate-500 font-medium">
                            <span v-if="totalBudget - totalSpent > 0">Sisa Anggaran: {{ maskValue(IDR.format(totalBudget - totalSpent)) }}</span>
                            <span v-else class="text-rose-600 text-sm font-bold">Defisit: {{ maskValue(IDR.format(totalSpent - totalBudget)) }}</span>
                        </p>
                    </div>
                    
                    <div class="hidden md:block w-px h-24 bg-slate-200 dark:bg-slate-700"></div>
                    
                    <div class="flex flex-col gap-3 w-full md:w-auto min-w-[200px]">
                        <PrimaryButton @click="openAddModal" class="w-full justify-center bg-royal-600 hover:bg-royal-700 py-3 text-sm">
                            <Plus class="w-4 h-4 mr-2" />
                            Buat Anggaran Area Baru
                        </PrimaryButton>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Iterating over sub-budgets -->
                    <div v-for="budget in budgets" :key="budget.id" class="glass-card p-6 border-transparent hover:border-royal-500/30 transition-shadow group relative overflow-hidden">
                        
                        <!-- Alert Backgrounds for Over Limit -->
                        <div v-if="getPercentage(budget.spentAmount, budget.limitAmount) > 100" class="absolute inset-0 bg-rose-500/5 dark:bg-rose-500/10 pointer-events-none z-0"></div>

                        <div class="relative z-10">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex items-center gap-3">
                                    <div :class="[budget.color, 'w-10 h-10 rounded-xl flex items-center justify-center text-white shadow-sm ring-4 ring-white dark:ring-slate-800']">
                                        <Target class="w-5 h-5" />
                                    </div>
                                    <h3 class="font-bold text-lg text-slate-800 dark:text-slate-100">{{ budget.category }}</h3>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div :class="['text-xs font-bold px-2 py-1 rounded-md border', getStatusText(getPercentage(budget.spentAmount, budget.limitAmount)).class]">
                                        {{ getStatusText(getPercentage(budget.spentAmount, budget.limitAmount)).text }}
                                    </div>
                                    <div class="flex opacity-0 group-hover:opacity-100 transition-opacity ml-2">
                                        <button @click="openEditModal(budget)" class="p-1.5 text-slate-400 hover:text-royal-600 dark:hover:text-royal-400 transition" title="Edit">
                                            <Edit2 class="w-4 h-4" />
                                        </button>
                                        <button @click="deleteBudget(budget.id)" class="p-1.5 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 transition" title="Hapus">
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="space-y-1 mb-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-500 dark:text-slate-400">Terpakai</span>
                                    <span class="font-medium text-slate-800 dark:text-slate-200">{{ IDR.format(budget.spentAmount) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-500 dark:text-slate-400">Batas (Limit)</span>
                                    <span class="font-medium text-slate-800 dark:text-slate-200">{{ IDR.format(budget.limitAmount) }}</span>
                                </div>
                            </div>

                            <div class="w-full bg-slate-100 dark:bg-slate-700/50 rounded-full h-2.5 mb-2 overflow-hidden shadow-inner">
                                <div :class="['h-2.5 rounded-full transition-all duration-700', getProgressColor(getPercentage(budget.spentAmount, budget.limitAmount))]" 
                                     :style="{ width: (getPercentage(budget.spentAmount, budget.limitAmount) > 100 ? 100 : getPercentage(budget.spentAmount, budget.limitAmount)) + '%' }">
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center text-xs">
                                <span class="font-medium text-slate-500 dark:text-slate-400">{{ Math.round(getPercentage(budget.spentAmount, budget.limitAmount)) }}%</span>
                                <span v-if="budget.limitAmount - budget.spentAmount >= 0" class="text-slate-500 dark:text-slate-400">Sisa: {{ IDR.format(budget.limitAmount - budget.spentAmount) }}</span>
                                <span v-else class="text-rose-600 font-bold">Over: {{ IDR.format(Math.abs(budget.limitAmount - budget.spentAmount)) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Add Budget Modal -->
        <div v-if="isAddModalOpen" class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="isAddModalOpen = false"></div>
            
            <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/80">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ editingBudgetId ? 'Edit Anggaran' : 'Tetapkan Anggaran Baru' }}</h3>
                    <button @click="isAddModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                        <X class="w-5 h-5" />
                    </button>
                </div>
                
                <form @submit.prevent="submitAddBudget" class="p-6">
                    <div class="space-y-4">
                        <div>
                            <InputLabel for="category" class="flex items-center gap-1.5"><Tags class="w-4 h-4 text-slate-400" /> Pilih Kategori Pengeluaran</InputLabel>
                            <select id="category" v-model="form.category_id" :disabled="editingBudgetId" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 shadow-sm focus:border-royal-500 focus:ring-royal-500 disabled:opacity-50" required>
                                <option value="" disabled selected>Pilih Kategori...</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                            <div v-if="form.errors.category_id" class="text-sm text-red-600 mt-1">{{ form.errors.category_id }}</div>
                        </div>
                        
                        <div>
                            <InputLabel for="limit_amount" class="flex items-center gap-1.5"><CircleDollarSign class="w-4 h-4 text-slate-400" /> Batas Maksimal Bulanan (Rp)</InputLabel>
                            <TextInput id="limit_amount" v-model="form.limitAmount" type="number" class="mt-1 block w-full text-lg font-semibold" placeholder="2000000" required />
                            <div v-if="form.errors.limitAmount" class="text-sm text-red-600 mt-1">{{ form.errors.limitAmount }}</div>
                            <p class="text-xs text-slate-500 mt-2 flex items-start gap-1">
                                <AlertCircle class="w-3 h-3 flex-shrink-0 mt-0.5" />
                                Anda akan mendapat peringatan (notifikasi) jika pengeluaran kategori ini mendekati batas anggaran.
                            </p>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3 text-sm border-t border-slate-100 dark:border-slate-700 pt-5">
                        <button type="button" @click="isAddModalOpen = false" class="px-4 py-2 font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-md transition-colors">Batal</button>
                        <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="bg-royal-600 hover:bg-royal-700 shadow-sm">{{ editingBudgetId ? 'Simpan Perubahan' : 'Simpan Anggaran' }}</PrimaryButton>
                    </div>
                </form>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
