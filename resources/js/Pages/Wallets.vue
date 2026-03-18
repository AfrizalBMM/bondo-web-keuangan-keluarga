<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { useVisibility } from '@/Composables/useVisibility';
import { ref } from 'vue';
import { Plus, Wallet, CreditCard, Building, Smartphone, X, CircleDollarSign, Palette, Edit2, Trash2 } from 'lucide-vue-next';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps({
    wallets: Array
});

const { maskValue } = useVisibility();

// Format Currency
const IDR = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
});

const isAddModalOpen = ref(false);
const editingWalletId = ref(null);

const form = useForm({
    name: '',
    type: 'Bank',
    initialBalance: '',
    color: 'bg-blue-600'
});

const colorOptions = [
    { label: 'BCA Blue', value: 'bg-blue-600', display: 'bg-blue-600' },
    { label: 'Mandiri Gold', value: 'bg-yellow-500', display: 'bg-yellow-500' },
    { label: 'Gopay Green', value: 'bg-emerald-500', display: 'bg-emerald-500' },
    { label: 'OVO Purple', value: 'bg-purple-600', display: 'bg-purple-600' },
    { label: 'Dana Blue', value: 'bg-sky-500', display: 'bg-sky-500' },
    { label: 'Cash/Tunai', value: 'bg-slate-700', display: 'bg-slate-700' },
];

const getIconForType = (type) => {
    switch(type) {
        case 'Bank': return Building;
        case 'E-Wallet': return Smartphone;
        case 'Tunai': return Wallet;
        default: return CreditCard;
    }
};

const openAddModal = () => {
    editingWalletId.value = null;
    form.reset();
    form.clearErrors();
    isAddModalOpen.value = true;
};

const openEditModal = (wallet) => {
    editingWalletId.value = wallet.id;
    form.name = wallet.name;
    form.type = wallet.type;
    form.initialBalance = wallet.balance; // Using balance as initialBalance in form
    form.color = wallet.color;
    form.clearErrors();
    isAddModalOpen.value = true;
};

const submitAddWallet = () => {
    if (editingWalletId.value) {
        form.put(route('wallets.update', editingWalletId.value), {
            preserveScroll: true,
            onSuccess: () => {
                isAddModalOpen.value = false;
                form.reset();
            }
        });
    } else {
        form.post(route('wallets.store'), {
            preserveScroll: true,
            onSuccess: () => {
                isAddModalOpen.value = false;
                form.reset();
            }
        });
    }
};

const deleteWallet = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus dompet ini? Seluruh transaksi terkait mungkin akan terpengaruh.')) {
        router.delete(route('wallets.destroy', id), {
            preserveScroll: true
        });
    }
};

</script>

<template>
    <AuthenticatedLayout>
        <Head title="Manajemen Dompet" />

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                
                <!-- Header -->
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900 dark:text-slate-50">Dompet Tersimpan</h1>
                        <p class="mt-1 text-slate-500 dark:text-slate-400">Kelola semua sumber dana keluarga dalam satu tempat.</p>
                    </div>
                    <PrimaryButton @click="openAddModal" class="bg-royal-600 hover:bg-royal-700 flex items-center gap-2">
                        <Plus class="w-4 h-4" />
                        Tambah Dompet
                    </PrimaryButton>
                </div>

                <!-- Wallet Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="wallet in wallets" :key="wallet.id" 
                        class="relative rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 group cursor-pointer h-52">
                        
                        <!-- Premium Card Background -->
                        <div :class="`absolute inset-0 opacity-90 group-hover:opacity-100 transition-opacity flex`">
                             <div :class="[wallet.color, 'w-full h-full']"></div>
                        </div>
                        
                        <!-- Glass overlay & Decorative elements -->
                        <div class="absolute inset-0 bg-white/10 dark:bg-black/10 backdrop-blur-[2px]"></div>
                        <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/20 rounded-full blur-2xl"></div>
                        <div class="absolute -left-10 -bottom-10 w-32 h-32 bg-black/20 rounded-full blur-xl"></div>

                        <!-- Card Content -->
                        <div class="relative h-full p-6 flex flex-col justify-between text-white drop-shadow-sm">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-semibold text-lg tracking-wide">{{ wallet.name }}</h3>
                                    <p class="text-white/80 text-xs mt-0.5">{{ wallet.type }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="opacity-0 group-hover:opacity-100 transition-opacity flex gap-1">
                                        <button @click.stop="openEditModal(wallet)" class="bg-white/20 p-2 rounded-lg backdrop-blur-md border border-white/30 hover:bg-white/30 transition-colors" title="Edit">
                                            <Edit2 class="w-4 h-4" />
                                        </button>
                                        <button @click.stop="deleteWallet(wallet.id)" class="bg-rose-500/50 p-2 rounded-lg backdrop-blur-md border border-rose-500/30 hover:bg-rose-500/70 transition-colors" title="Hapus">
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                    <div class="bg-white/20 p-2 rounded-lg backdrop-blur-md border border-white/30 hidden sm:block group-hover:hidden transition-all">
                                        <component :is="getIconForType(wallet.type)" class="w-5 h-5" />
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <p class="text-white/70 text-sm mb-1">Saldo Tersedia</p>
                                <h2 class="text-3xl font-bold tracking-tight">
                                    {{ maskValue(IDR.format(wallet.balance)) }}
                                </h2>
                            </div>
                        </div>
                    </div>

                    <!-- Empty Add Wallet Card -->
                    <button @click="openAddModal" class="rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-700 flex flex-col items-center justify-center p-6 h-52 hover:border-royal-500 hover:bg-royal-50 dark:hover:bg-royal-900/20 transition-all text-slate-500 hover:text-royal-600 dark:text-slate-400 dark:hover:text-royal-400 group">
                        <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-3 group-hover:bg-royal-100 dark:group-hover:bg-royal-900/50 transition-colors">
                            <Plus class="w-6 h-6" />
                        </div>
                        <h3 class="font-semibold text-lg">Tambah Dompet Baru</h3>
                        <p class="text-sm mt-1 text-center">Hubungkan rekening bank atau E-wallet lainnya.</p>
                    </button>
                </div>

            </div>
        </div>

        <!-- Add Wallet Modal -->
        <div v-if="isAddModalOpen" class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="isAddModalOpen = false"></div>
            
            <!-- Modal Box -->
            <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/80">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ editingWalletId ? 'Edit Dompet' : 'Tambah Dompet Baru' }}</h3>
                    <button @click="isAddModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                        <X class="w-5 h-5" />
                    </button>
                </div>
                
                <form @submit.prevent="submitAddWallet" class="p-6">
                    <div class="space-y-4">
                        <div>
                            <InputLabel for="wallet_name" class="flex items-center gap-1.5"><CreditCard class="w-4 h-4 text-slate-400" /> Nama Dompet</InputLabel>
                            <TextInput id="wallet_name" v-model="form.name" type="text" class="mt-1 block w-full" placeholder="Cth: BCA Personal" required autofocus />
                            <div v-if="form.errors.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="wallet_type" class="flex items-center gap-1.5"><Wallet class="w-4 h-4 text-slate-400" /> Tipe</InputLabel>
                                <select id="wallet_type" v-model="form.type" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 shadow-sm focus:border-royal-500 focus:ring-royal-500">
                                    <option value="Bank">Bank</option>
                                    <option value="E-Wallet">E-Wallet</option>
                                    <option value="Tunai">Tunai</option>
                                    <option value="Kartu Kredit">Kartu Kredit</option>
                                </select>
                                <div v-if="form.errors.type" class="text-sm text-red-600 mt-1">{{ form.errors.type }}</div>
                            </div>
                            <div>
                                <InputLabel for="initial_balance" class="flex items-center gap-1.5"><CircleDollarSign class="w-4 h-4 text-slate-400" /> Saldo Awal</InputLabel>
                                <div class="relative mt-1 border-slate-300 dark:border-slate-700 shadow-sm rounded-md">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-slate-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="number" id="initial_balance" v-model="form.initialBalance" class="block w-full pl-9 rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-royal-500 focus:ring-royal-500 sm:text-sm" placeholder="0" />
                                </div>
                                <div v-if="form.errors.initialBalance" class="text-sm text-red-600 mt-1">{{ form.errors.initialBalance }}</div>
                            </div>
                        </div>

                        <div>
                            <InputLabel class="flex items-center gap-1.5"><Palette class="w-4 h-4 text-slate-400" /> Warna Identitas (Kartu)</InputLabel>
                            <div class="mt-2 flex gap-3 flex-wrap">
                                <label v-for="color in colorOptions" :key="color.value" class="cursor-pointer relative">
                                    <input type="radio" v-model="form.color" :value="color.value" class="sr-only peer" />
                                    <div :class="[color.display, 'w-8 h-8 rounded-full border-2 border-transparent peer-checked:ring-2 peer-checked:ring-offset-2 peer-checked:ring-royal-500 dark:peer-checked:ring-offset-slate-800 transition-all']" :title="color.label"></div>
                                </label>
                            </div>
                            <div v-if="form.errors.color" class="text-sm text-red-600 mt-1">{{ form.errors.color }}</div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" @click="isAddModalOpen = false" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-md transition-colors">Batal</button>
                        <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="bg-royal-600 hover:bg-royal-700">{{ editingWalletId ? 'Simpan Perubahan' : 'Simpan Dompet' }}</PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
