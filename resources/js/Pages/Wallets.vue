<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { useVisibility } from '@/Composables/useVisibility';
import { ref } from 'vue';
import { 
    Plus, Wallet, CreditCard, Building, Smartphone, X, 
    CircleDollarSign, Palette, Edit2, Trash2, Hash 
} from 'lucide-vue-next';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps({
    wallets: Array
});

const { maskValue } = useVisibility();

const IDR = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
});

const isAddModalOpen = ref(false);
const editingWalletId = ref(null);

const form = useForm({
    name: '',
    account_number: '', // Tambahan field baru
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
    form.account_number = wallet.account_number || ''; // Isi data no rekening
    form.type = wallet.type;
    form.initialBalance = wallet.balance; 
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
    if (confirm('Apakah Anda yakin ingin menghapus dompet ini?')) {
        router.delete(route('wallets.destroy', id), {
            preserveScroll: true
        });
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Manajemen Dompet" />

        <div class="py-12 bg-slate-50 dark:bg-slate-950 min-h-screen">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Dompet Tersimpan</h1>
                        <p class="mt-1 text-slate-500 dark:text-slate-400">Kelola semua sumber dana keluarga Anda.</p>
                    </div>
                    <PrimaryButton @click="openAddModal" class="bg-royal-600 hover:bg-royal-700 shadow-lg shadow-royal-500/30 flex items-center gap-2 rounded-xl py-3">
                        <Plus class="w-5 h-5" />
                        Tambah Dompet
                    </PrimaryButton>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div v-for="wallet in wallets" :key="wallet.id" 
                        class="relative rounded-3xl overflow-hidden shadow-2xl hover:scale-[1.02] transition-all duration-500 group cursor-pointer h-60">
                        
                        <div :class="[wallet.color, 'absolute inset-0 transition-transform duration-700 group-hover:scale-110']"></div>
                        
                        <div class="absolute inset-0 bg-black/10 backdrop-blur-[1px]"></div>
                        <div class="absolute -right-16 -top-16 w-48 h-48 bg-white/20 rounded-full blur-3xl"></div>
                        <div class="absolute -left-16 -bottom-16 w-48 h-48 bg-black/30 rounded-full blur-3xl"></div>

                        <div class="relative h-full p-8 flex flex-col justify-between text-white">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-bold text-xl tracking-wide uppercase">{{ wallet.name }}</h3>
                                    <p class="text-white/70 text-xs font-medium tracking-widest mt-1">{{ wallet.type }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="opacity-0 group-hover:opacity-100 flex gap-2 transition-all duration-300 transform translate-x-4 group-hover:translate-x-0">
                                        <button @click.stop="openEditModal(wallet)" class="bg-white/20 p-2.5 rounded-xl backdrop-blur-xl border border-white/30 hover:bg-white/40 transition-all">
                                            <Edit2 class="w-4 h-4" />
                                        </button>
                                        <button @click.stop="deleteWallet(wallet.id)" class="bg-rose-500/40 p-2.5 rounded-xl backdrop-blur-xl border border-rose-500/30 hover:bg-rose-500/60 transition-all">
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                    <div class="bg-white/20 p-3 rounded-2xl backdrop-blur-xl border border-white/30 group-hover:hidden transition-all">
                                        <component :is="getIconForType(wallet.type)" class="w-6 h-6" />
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <p class="text-white/60 text-[10px] uppercase tracking-[0.2em] font-bold">Nomor Rekening</p>
                                <p class="text-lg font-mono tracking-[0.15em] mt-0.5">
                                    {{ wallet.account_number ? wallet.account_number : '•••• •••• •••• ••••' }}
                                </p>
                            </div>
                            
                            <div class="flex justify-between items-end">
                                <div>
                                    <p class="text-white/60 text-[10px] uppercase tracking-[0.2em] font-bold">Saldo Tersedia</p>
                                    <h2 class="text-3xl font-black tracking-tight mt-1">
                                        {{ maskValue(IDR.format(wallet.balance)) }}
                                    </h2>
                                </div>
                                <div class="w-10 h-8 bg-gradient-to-br from-yellow-200 to-yellow-500 rounded-md opacity-50"></div>
                            </div>
                        </div>
                    </div>

                    <button @click="openAddModal" class="rounded-3xl border-2 border-dashed border-slate-300 dark:border-slate-800 flex flex-col items-center justify-center p-8 h-60 hover:border-royal-500 hover:bg-royal-50/50 dark:hover:bg-royal-900/10 transition-all group">
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-900 flex items-center justify-center mb-4 group-hover:bg-royal-100 dark:group-hover:bg-royal-800 transition-colors shadow-sm">
                            <Plus class="w-8 h-8 text-slate-400 group-hover:text-royal-600 transition-colors" />
                        </div>
                        <h3 class="font-bold text-lg text-slate-700 dark:text-slate-300">Tambah Dompet</h3>
                        <p class="text-sm mt-1 text-slate-500 dark:text-slate-500 text-center">Rekening Bank, E-Wallet, atau Tunai</p>
                    </button>
                </div>
            </div>
        </div>

        <div v-if="isAddModalOpen" class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-md transition-opacity" @click="isAddModalOpen = false"></div>
            
            <div class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all border border-white/10">
                <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-900/50">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-royal-100 dark:bg-royal-900/30 rounded-xl">
                            <Wallet class="w-5 h-5 text-royal-600" />
                        </div>
                        <h3 class="text-xl font-black text-slate-900 dark:text-white">{{ editingWalletId ? 'Ubah Detail Dompet' : 'Dompet Baru' }}</h3>
                    </div>
                    <button @click="isAddModalOpen = false" class="p-2 rounded-full hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors">
                        <X class="w-6 h-6 text-slate-400" />
                    </button>
                </div>
                
                <form @submit.prevent="submitAddWallet" class="p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2 md:col-span-1">
                            <InputLabel for="wallet_name" class="font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Akun/Bank</InputLabel>
                            <TextInput id="wallet_name" v-model="form.name" type="text" class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-950" placeholder="Contoh: BCA Personal" required />
                            <div v-if="form.errors.name" class="text-xs text-red-500 mt-1">{{ form.errors.name }}</div>
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <InputLabel for="account_number" class="font-bold text-slate-700 dark:text-slate-300 mb-2">Nomor Rekening</InputLabel>
                            <TextInput id="account_number" v-model="form.account_number" type="text" class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-950" placeholder="12345xxxxx" />
                            <div v-if="form.errors.account_number" class="text-xs text-red-500 mt-1">{{ form.errors.account_number }}</div>
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <InputLabel for="wallet_type" class="font-bold text-slate-700 dark:text-slate-300 mb-2">Kategori</InputLabel>
                            <select id="wallet_type" v-model="form.type" class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300 focus:ring-royal-500">
                                <option value="Bank">Giro/Bank</option>
                                <option value="E-Wallet">E-Wallet</option>
                                <option value="Tunai">Tunai</option>
                                <option value="Kartu Kredit">Kartu Kredit</option>
                            </select>
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <InputLabel for="initial_balance" class="font-bold text-slate-700 dark:text-slate-300 mb-2">Saldo Sekarang</InputLabel>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">Rp</span>
                                <input type="number" id="initial_balance" v-model="form.initialBalance" class="w-full pl-10 pr-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-950 dark:text-white focus:ring-royal-500" placeholder="0" />
                            </div>
                        </div>
                    </div>

                    <div>
                        <InputLabel class="font-bold text-slate-700 dark:text-slate-300 mb-3">Warna Kartu Virtual</InputLabel>
                        <div class="flex gap-4 flex-wrap bg-slate-50 dark:bg-slate-950 p-4 rounded-2xl border border-slate-100 dark:border-slate-800">
                            <label v-for="color in colorOptions" :key="color.value" class="cursor-pointer group relative">
                                <input type="radio" v-model="form.color" :value="color.value" class="sr-only" />
                                <div :class="[color.display, 'w-10 h-10 rounded-full border-4 transition-all duration-300', form.color === color.value ? 'border-royal-500 scale-110 shadow-lg' : 'border-transparent opacity-60 hover:opacity-100']"></div>
                                <div v-if="form.color === color.value" class="absolute -top-1 -right-1 bg-royal-500 text-white rounded-full p-0.5 shadow-sm">
                                    <X class="w-3 h-3 rotate-45" />
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="mt-8 flex gap-3">
                        <button type="button" @click="isAddModalOpen = false" class="flex-1 px-6 py-3.5 text-sm font-bold text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-2xl transition-all">
                            Batal
                        </button>
                        <PrimaryButton type="submit" :disabled="form.processing" class="flex-[2] bg-royal-600 hover:bg-royal-700 py-3.5 rounded-2xl shadow-xl shadow-royal-500/20 justify-center text-base">
                            {{ editingWalletId ? 'Simpan Perubahan' : 'Aktifkan Dompet' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Menghilangkan spin button pada input number */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
</style>