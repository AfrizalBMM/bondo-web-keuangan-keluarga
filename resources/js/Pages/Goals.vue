<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { useVisibility } from '@/Composables/useVisibility';
import { useConfirm } from '@/Composables/useConfirm';
import { ref, computed } from 'vue';
import { Plus, Target, CheckCircle2, TrendingUp, X, Flag, CircleDollarSign, CalendarDays, Palette, Edit2, Trash2, Wallet } from 'lucide-vue-next';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps({
    goals: Array,
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
const isDepositModalOpen = ref(false);
const activeGoal = ref(null);
const editingGoalId = ref(null);

const form = useForm({
    name: '',
    targetAmount: '',
    targetDate: '',
    color: 'from-blue-500 to-indigo-500'
});

const depositForm = useForm({
    amount: '',
    wallet_id: ''
});

const colorOptions = [
    { label: 'Blue Indigo', value: 'from-blue-500 to-indigo-500', bg: 'bg-gradient-to-r from-blue-500 to-indigo-500' },
    { label: 'Rose Orange', value: 'from-rose-500 to-orange-500', bg: 'bg-gradient-to-r from-rose-500 to-orange-500' },
    { label: 'Emerald Teal', value: 'from-emerald-400 to-teal-500', bg: 'bg-gradient-to-r from-emerald-400 to-teal-500' },
    { label: 'Purple Pink', value: 'from-purple-500 to-pink-500', bg: 'bg-gradient-to-r from-purple-500 to-pink-500' },
];

// Calculate percentages
const getPercentage = (current, target) => {
    const percent = (current / target) * 100;
    return percent > 100 ? 100 : Math.round(percent);
};

const openDepositModal = (goal) => {
    activeGoal.value = goal;
    depositForm.amount = '';
    depositForm.wallet_id = '';
    isDepositModalOpen.value = true;
};

const openAddModal = () => {
    editingGoalId.value = null;
    form.reset();
    form.clearErrors();
    isAddModalOpen.value = true;
};

const openEditModal = (goal) => {
    editingGoalId.value = goal.id;
    form.name = goal.name;
    form.targetAmount = goal.targetAmount;
    form.targetDate = goal.targetDate;
    form.color = goal.color;
    form.clearErrors();
    isAddModalOpen.value = true;
};

const deleteGoal = (id) => {
    confirmModal({
        title: 'Hapus Target?',
        message: 'Apakah Anda yakin ingin menghapus target beserta riwayat progresnya?',
        confirmText: 'Ya, Hapus',
        onConfirm: () => {
            router.delete(route('goals.destroy', id), {
                preserveScroll: true
            });
        }
    });
};

const submitAddGoal = () => {
    if (editingGoalId.value) {
        form.put(route('goals.update', editingGoalId.value), {
            preserveScroll: true,
            onSuccess: () => {
                isAddModalOpen.value = false;
                form.reset();
            }
        });
    } else {
        form.post(route('goals.store'), {
            preserveScroll: true,
            onSuccess: () => {
                isAddModalOpen.value = false;
                form.reset();
            }
        });
    }
};

const submitDeposit = () => {
    if(!activeGoal.value) return;
    
    depositForm.post(route('goals.deposit', activeGoal.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            isDepositModalOpen.value = false;
            depositForm.reset();
            activeGoal.value = null;
        }
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Financial Goals" />

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-8">
                
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-2">
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900 dark:text-slate-50">Financial Goals</h1>
                        <p class="mt-1 text-slate-500 dark:text-slate-400">Rencanakan dan pantau progres tabungan masa depan keluarga.</p>
                    </div>
                    <PrimaryButton @click="openAddModal" class="bg-royal-600 hover:bg-royal-700">
                        <Plus class="w-4 h-4 mr-2" />
                        Target Baru
                    </PrimaryButton>
                </div>

                <!-- Goals Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div v-for="goal in goals" :key="goal.id" class="glass-card p-6 flex flex-col group relative overflow-hidden">
                        
                        <!-- Success overlay for completed goals -->
                        <div v-if="goal.currentAmount >= goal.targetAmount" class="absolute inset-0 bg-emerald-500/5 dark:bg-emerald-500/10 pointer-events-none z-0"></div>
                        <div v-if="goal.currentAmount >= goal.targetAmount" class="absolute top-6 right-6 z-10 text-emerald-500 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1 shadow-sm">
                            <CheckCircle2 class="w-4 h-4" /> Tercapai
                        </div>

                        <div class="relative z-10 flex-grow pr-16 md:pr-0">
                            <div class="flex items-center gap-3 mb-6">
                                <div :class="['w-12 h-12 rounded-xl flex items-center justify-center text-white shadow-md bg-gradient-to-r', goal.color]">
                                    <Target class="w-6 h-6" />
                                </div>
                                <div>
                                    <h3 class="font-bold text-xl text-slate-800 dark:text-slate-100">{{ goal.name }}</h3>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">Target: {{ new Date(goal.targetDate).toLocaleDateString('id-ID', { year: 'numeric', month: 'long' }) }}</p>
                                </div>
                            </div>
                            
                            <!-- Progress Stats -->
                            <div class="flex justify-between items-end mb-2">
                                <div>
                                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Terkumpul</p>
                                    <p class="text-lg font-bold text-slate-800 dark:text-slate-200">{{ maskValue(IDR.format(goal.currentAmount)) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Target</p>
                                    <p class="text-sm font-semibold text-slate-600 dark:text-slate-300">{{ maskValue(IDR.format(goal.targetAmount)) }}</p>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-3.5 mb-2 overflow-hidden shadow-inner relative">
                                <div :class="['h-3.5 rounded-full transition-all duration-1000 bg-gradient-to-r', goal.color]" :style="{ width: getPercentage(goal.currentAmount, goal.targetAmount) + '%' }"></div>
                            </div>
                            <div class="flex justify-between items-center text-xs text-slate-500 dark:text-slate-400">
                                <span>{{ getPercentage(goal.currentAmount, goal.targetAmount) }}% Selesai</span>
                                <span v-if="goal.targetAmount - goal.currentAmount > 0">Sisa: {{ maskValue(IDR.format(goal.targetAmount - goal.currentAmount)) }}</span>
                            </div>
                        </div>

                        <div class="relative z-10 mt-6 pt-5 border-t border-slate-100 dark:border-slate-700/50 flex justify-between items-center">
                            <div class="flex gap-2">
                                <button @click="openEditModal(goal)" class="p-2 text-slate-400 hover:text-royal-600 dark:hover:text-royal-400 transition bg-slate-50 hover:bg-royal-50 dark:bg-slate-800 dark:hover:bg-royal-900/30 rounded-lg">
                                    <Edit2 class="w-4 h-4" />
                                </button>
                                <button @click="deleteGoal(goal.id)" class="p-2 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 transition bg-slate-50 hover:bg-rose-50 dark:bg-slate-800 dark:hover:bg-rose-900/30 rounded-lg">
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                            <button 
                                v-if="goal.currentAmount < goal.targetAmount"
                                @click="openDepositModal(goal)" 
                                class="flex items-center gap-2 px-4 py-2 bg-slate-900 dark:bg-royal-600 text-white rounded-lg text-sm font-medium hover:opacity-90 transition shadow-sm"
                            >
                                <TrendingUp class="w-4 h-4" />
                                Setor Dana
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Add Goal Modal -->
        <div v-if="isAddModalOpen" class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="isAddModalOpen = false"></div>
            
            <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/80">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ editingGoalId ? 'Edit Target' : 'Buat Target Baru' }}</h3>
                    <button @click="isAddModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                        <X class="w-5 h-5" />
                    </button>
                </div>
                
                <form @submit.prevent="submitAddGoal" class="p-6">
                    <div class="space-y-4">
                        <div>
                            <InputLabel for="goal_name" class="flex items-center gap-1.5"><Flag class="w-4 h-4 text-slate-400" /> Nama Goal</InputLabel>
                            <TextInput id="goal_name" v-model="form.name" type="text" class="mt-1 block w-full" placeholder="Cth: Liburan ke Bali" required autofocus />
                            <div v-if="form.errors.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</div>
                        </div>
                        
                        <div>
                            <InputLabel for="target_amount" class="flex items-center gap-1.5"><Target class="w-4 h-4 text-slate-400" /> Target Nominal (Rp)</InputLabel>
                            <TextInput id="target_amount" v-model="form.targetAmount" type="number" class="mt-1 block w-full" placeholder="10000000" required />
                            <div v-if="form.errors.targetAmount" class="text-sm text-red-600 mt-1">{{ form.errors.targetAmount }}</div>
                        </div>
                        
                        <div>
                            <InputLabel for="target_date" class="flex items-center gap-1.5"><CalendarDays class="w-4 h-4 text-slate-400" /> Target Deadline</InputLabel>
                            <TextInput id="target_date" v-model="form.targetDate" type="date" class="mt-1 block w-full" required />
                            <div v-if="form.errors.targetDate" class="text-sm text-red-600 mt-1">{{ form.errors.targetDate }}</div>
                        </div>

                        <div>
                            <InputLabel class="flex items-center gap-1.5"><Palette class="w-4 h-4 text-slate-400" /> Tema Warna Gradient</InputLabel>
                            <div class="mt-2 flex gap-3 flex-wrap">
                                <label v-for="color in colorOptions" :key="color.value" class="cursor-pointer relative">
                                    <input type="radio" v-model="form.color" :value="color.value" class="sr-only peer" />
                                    <div :class="[color.bg, 'w-10 h-10 rounded-full border-2 border-transparent peer-checked:ring-2 peer-checked:ring-offset-2 peer-checked:ring-royal-500 dark:peer-checked:ring-offset-slate-800 shadow-sm transition-all']"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3 text-sm border-t border-slate-100 dark:border-slate-700 pt-5">
                        <button type="button" @click="isAddModalOpen = false" class="px-4 py-2 font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-md transition-colors">Batal</button>
                        <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="bg-royal-600 hover:bg-royal-700">{{ editingGoalId ? 'Simpan Perubahan' : 'Simpan Target' }}</PrimaryButton>
                    </div>
                </form>
            </div>
        </div>

        <!-- Deposit to Goal Modal -->
        <div v-if="isDepositModalOpen" class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="isDepositModalOpen = false"></div>
            
            <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden transform transition-all">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/80">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Setor Dana</h3>
                    <button @click="isDepositModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                        <X class="w-5 h-5" />
                    </button>
                </div>
                
                <form @submit.prevent="submitDeposit" class="p-6">
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                        Tambahkan dana untuk progres <strong>{{ activeGoal?.name }}</strong>.
                    </p>
                    <div class="space-y-4">
                        <div>
                            <InputLabel for="deposit_amount" class="flex items-center gap-1.5"><CircleDollarSign class="w-4 h-4 text-slate-400" /> Nominal Setor (Rp)</InputLabel>
                            <TextInput id="deposit_amount" v-model="depositForm.amount" type="number" class="mt-1 block w-full text-lg font-semibold" placeholder="0" required autofocus />
                            <div v-if="depositForm.errors.amount" class="text-sm text-red-600 mt-1">{{ depositForm.errors.amount }}</div>
                        </div>

                        <div>
                            <InputLabel for="wallet" class="flex items-center gap-1.5"><Wallet class="w-4 h-4 text-slate-400" /> Sumber Dana (Dompet)</InputLabel>
                            <select id="wallet" v-model="depositForm.wallet_id" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 shadow-sm focus:border-royal-500 focus:ring-royal-500" required>
                                <option value="" disabled selected>Pilih Dompet...</option>
                                <option v-for="wallet in wallets" :key="wallet.id" :value="wallet.id">{{ wallet.name }} (Sisa: {{ IDR.format(wallet.balance) }})</option>
                            </select>
                            <div v-if="depositForm.errors.wallet_id" class="text-sm text-red-600 mt-1">{{ depositForm.errors.wallet_id }}</div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <PrimaryButton type="submit" :class="{ 'opacity-25': depositForm.processing }" :disabled="depositForm.processing" class="w-full justify-center bg-royal-600 hover:bg-royal-700 py-3">Setor Sekarang</PrimaryButton>
                    </div>
                </form>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
