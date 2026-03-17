<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Plus, Edit2, Trash2, Tag, ArrowUpRight, ArrowDownRight, X, Check, Type, Palette } from 'lucide-vue-next';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps({
    categories: Array
});

const isAddModalOpen = ref(false);
const activeTab = ref('Pengeluaran'); // 'Pengeluaran' or 'Pemasukan'

const form = useForm({
    name: '',
    type: 'Pengeluaran',
    color: 'bg-rose-500' // default color
});

const colorOptions = [
    { label: 'Red', value: 'bg-rose-500' },
    { label: 'Orange', value: 'bg-orange-500' },
    { label: 'Amber', value: 'bg-amber-500' },
    { label: 'Emerald', value: 'bg-emerald-500' },
    { label: 'Cyan', value: 'bg-cyan-500' },
    { label: 'Blue', value: 'bg-blue-500' },
    { label: 'Indigo', value: 'bg-indigo-500' },
    { label: 'Violet', value: 'bg-violet-500' },
    { label: 'Fuchsia', value: 'bg-fuchsia-500' },
    { label: 'Slate', value: 'bg-slate-500' },
];

// Filtered categories
const displayedCategories = computed(() => {
    return props.categories.filter(c => c.type === activeTab.value);
});

// Format Currency
const IDR = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
});

const submitAddCategory = () => {
    form.post(route('categories.store'), {
        preserveScroll: true,
        onSuccess: () => {
            isAddModalOpen.value = false;
            form.reset();
        }
    });
};

const openAddModal = (type = null) => {
    if (type) {
        form.type = type;
        form.color = type === 'Pemasukan' ? 'bg-emerald-500' : 'bg-rose-500';
    }
    isAddModalOpen.value = true;
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Kategori" />

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-8">
                
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-2">
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900 dark:text-slate-50">Kategori Transaksi</h1>
                        <p class="mt-1 text-slate-500 dark:text-slate-400">Kelola pos penerimaan dan pengeluaran keuangan keluarga.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                    <!-- Sidebar Tabs -->
                    <div class="lg:col-span-1 space-y-2">
                        <button 
                            @click="activeTab = 'Pengeluaran'"
                            :class="[
                                'w-full flex items-center justify-between p-4 rounded-xl text-left transition-all',
                                activeTab === 'Pengeluaran' 
                                    ? 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-500/30 font-semibold' 
                                    : 'glass text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800'
                            ]"
                        >
                            <span class="flex items-center gap-3">
                                <ArrowUpRight class="w-5 h-5" />
                                Pengeluaran
                            </span>
                            <span class="text-xs py-1 px-2 rounded-full bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700">
                                {{ categories.filter(c => c.type === 'Pengeluaran').length }}
                            </span>
                        </button>
                        
                        <button 
                            @click="activeTab = 'Pemasukan'"
                            :class="[
                                'w-full flex items-center justify-between p-4 rounded-xl text-left transition-all',
                                activeTab === 'Pemasukan' 
                                    ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/30 font-semibold' 
                                    : 'glass text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800'
                            ]"
                        >
                            <span class="flex items-center gap-3">
                                <ArrowDownRight class="w-5 h-5" />
                                Pemasukan
                            </span>
                            <span class="text-xs py-1 px-2 rounded-full bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700">
                                {{ categories.filter(c => c.type === 'Pemasukan').length }}
                            </span>
                        </button>
                    </div>

                    <!-- Category List -->
                    <div class="lg:col-span-3">
                        <div class="glass-card overflow-hidden">
                            <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                                <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                                    Daftar {{ activeTab }}
                                </h2>
                                <PrimaryButton @click="openAddModal(activeTab)" class="bg-royal-600 hover:bg-royal-700 flex items-center gap-2 text-sm px-3 py-1.5">
                                    <Plus class="w-4 h-4" />
                                    <span class="hidden sm:inline">Tambah</span>
                                </PrimaryButton>
                            </div>
                            
                            <div class="p-0">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-px bg-slate-100 dark:bg-slate-700/50">
                                    <div v-for="category in displayedCategories" :key="category.id" class="bg-white dark:bg-slate-800 p-6 hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-colors group">
                                        <div class="flex justify-between items-start mb-4">
                                            <div class="flex items-center gap-3">
                                                <div :class="[category.color, 'w-10 h-10 rounded-xl flex items-center justify-center text-white shadow-sm ring-4 ring-white dark:ring-slate-800 group-hover:scale-110 transition-transform']">
                                                    <Tag class="w-5 h-5" />
                                                </div>
                                                <div>
                                                    <h3 class="font-semibold text-slate-800 dark:text-slate-200 text-lg">{{ category.name }}</h3>
                                                </div>
                                            </div>
                                            <div class="flex opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button class="p-1.5 text-slate-400 hover:text-royal-600 dark:hover:text-royal-400 transition" title="Edit">
                                                    <Edit2 class="w-4 h-4" />
                                                </button>
                                                <button class="p-1.5 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 transition" title="Hapus">
                                                    <Trash2 class="w-4 h-4" />
                                                </button>
                                            </div>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <p class="text-slate-500 dark:text-slate-400">{{ category.tx_count }} Transaksi</p>
                                            <p class="font-medium text-slate-700 dark:text-slate-300">{{ IDR.format(category.total_amount) }}</p>
                                        </div>
                                    </div>

                                    <!-- Empty State Add Button -->
                                    <div @click="openAddModal(activeTab)" class="bg-slate-50 dark:bg-slate-800/50 p-6 flex flex-col items-center justify-center cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors text-slate-500 dark:text-slate-400 hover:text-royal-600 min-h-[140px]">
                                        <div class="w-10 h-10 rounded-full border-2 border-dashed border-current flex items-center justify-center mb-2">
                                            <Plus class="w-5 h-5" />
                                        </div>
                                        <span class="font-medium">Kategori Baru</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Add Category Modal -->
        <div v-if="isAddModalOpen" class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="isAddModalOpen = false"></div>
            
            <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/80">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Tambah Kategori</h3>
                    <button @click="isAddModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                        <X class="w-5 h-5" />
                    </button>
                </div>
                
                <form @submit.prevent="submitAddCategory" class="p-6">
                    <div class="space-y-5">
                        <div class="flex rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 p-1">
                            <label class="flex-1 text-center cursor-pointer">
                                <input type="radio" v-model="form.type" value="Pengeluaran" class="sr-only peer" />
                                <div class="py-2 px-3 rounded-md text-sm font-medium text-slate-500 dark:text-slate-400 peer-checked:bg-white dark:peer-checked:bg-slate-700 peer-checked:text-royal-600 dark:peer-checked:text-white peer-checked:shadow-sm transition-all">
                                    Pengeluaran
                                </div>
                            </label>
                            <label class="flex-1 text-center cursor-pointer">
                                <input type="radio" v-model="form.type" value="Pemasukan" class="sr-only peer" />
                                <div class="py-2 px-3 rounded-md text-sm font-medium text-slate-500 dark:text-slate-400 peer-checked:bg-white dark:peer-checked:bg-slate-700 peer-checked:text-royal-600 dark:peer-checked:text-white peer-checked:shadow-sm transition-all">
                                    Pemasukan
                                </div>
                            </label>
                        </div>

                        <div>
                            <InputLabel for="cat_name" class="flex items-center gap-1.5"><Type class="w-4 h-4 text-slate-400" /> Nama Kategori</InputLabel>
                            <TextInput id="cat_name" v-model="form.name" type="text" class="mt-1 block w-full" placeholder="Cth: Belanja Dapur" required autofocus />
                            <div v-if="form.errors.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</div>
                        </div>
                        
                        <div>
                            <InputLabel class="flex items-center gap-1.5"><Palette class="w-4 h-4 text-slate-400" /> Warna Indikator</InputLabel>
                            <div class="mt-3 grid grid-cols-5 gap-3">
                                <label v-for="color in colorOptions" :key="color.value" class="cursor-pointer relative aspect-square">
                                    <input type="radio" v-model="form.color" :value="color.value" class="sr-only peer" />
                                    <div :class="[color.value, 'w-full h-full rounded-xl border-2 border-transparent peer-checked:ring-2 peer-checked:ring-offset-2 peer-checked:ring-royal-500 dark:peer-checked:ring-offset-slate-800 transition-all flex items-center justify-center text-white']">
                                        <Check v-if="form.color === color.value" class="w-5 h-5 drop-shadow-md" />
                                    </div>
                                    <span class="sr-only">{{ color.label }}</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" @click="isAddModalOpen = false" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-md transition-colors">Batal</button>
                        <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="bg-royal-600 hover:bg-royal-700">Simpan Kategori</PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
