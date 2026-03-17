<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { useVisibility } from '@/Composables/useVisibility';
import { ref, computed } from 'vue';
import { Plus, Edit2, Trash2, Home, Car, Gem, Briefcase, TrendingDown, ArrowDownRight, X, Type, Tags, CalendarDays, CircleDollarSign } from 'lucide-vue-next';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps({
    assets: Array,
});

const { maskValue } = useVisibility();

// Format Currency
const IDR = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
});

const isAddModalOpen = ref(false);

const form = useForm({
    name: '',
    type: 'Kendaraan',
    initialValue: '',
    purchaseDate: '',
    depreciationRate: 10
});

const assetTypes = ['Properti', 'Kendaraan', 'Perhiasan', 'Elektronik', 'Lainnya'];

const getIconForType = (type) => {
    switch(type) {
        case 'Properti': return Home;
        case 'Kendaraan': return Car;
        case 'Perhiasan': return Gem;
        case 'Elektronik': return Briefcase;
        default: return Briefcase;
    }
};

// Calculate current value based on straight-line depreciation or appreciation
const getCurrentValue = (asset) => {
    const purchaseDate = new Date(asset.purchaseDate);
    const currentDate = new Date();
    const yearsDiff = (currentDate - purchaseDate) / (1000 * 60 * 60 * 24 * 365.25);
    
    // Formula: Initial Value - (Initial Value * (Depreciation Rate / 100) * Years)
    const depreciationAmount = asset.initialValue * (asset.depreciationRate / 100) * yearsDiff;
    let currentValue = asset.initialValue - depreciationAmount;
    
    return currentValue > 0 ? currentValue : 0; 
};

const totalAssetValue = computed(() => {
    return props.assets.reduce((acc, curr) => acc + getCurrentValue(curr), 0);
});

const submitAddAsset = () => {
    form.post(route('assets.store'), {
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
        <Head title="Aset Keluarga" />

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-8">
                
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-2">
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900 dark:text-slate-50">Inventaris Aset</h1>
                        <p class="mt-1 text-slate-500 dark:text-slate-400">Kelola dan pantau estimasi nilai pasar kekayaan barang keluarga Anda.</p>
                    </div>
                    <div class="glass flex items-center gap-6 px-6 py-3 rounded-2xl border border-slate-200 dark:border-slate-700">
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Estimasi Aset</p>
                            <p class="text-xl font-bold text-royal-600 dark:text-royal-400">{{ maskValue(IDR.format(totalAssetValue)) }}</p>
                        </div>
                        <PrimaryButton @click="isAddModalOpen = true" class="bg-royal-600 hover:bg-royal-700">
                            <Plus class="w-4 h-4 mr-2" />
                            Tambah
                        </PrimaryButton>
                    </div>
                </div>

                <!-- Assets Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="asset in assets" :key="asset.id" class="glass-card hover:border-royal-500/50 dark:hover:border-royal-500/50 transition-all group overflow-hidden flex flex-col">
                        <!-- Card Header -->
                        <div class="p-6 border-b border-slate-100 dark:border-slate-700/50 flex justify-between items-start bg-slate-50/50 dark:bg-slate-800/50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-white dark:bg-slate-700 shadow-sm flex items-center justify-center text-royal-600 dark:text-royal-400">
                                    <component :is="getIconForType(asset.type)" class="w-5 h-5" />
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-800 dark:text-slate-100 truncate w-40 sm:w-48" :title="asset.name">{{ asset.name }}</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ asset.type }} &bull; Beli: {{ new Date(asset.purchaseDate).toLocaleDateString('id-ID', { year: 'numeric', month: 'short' }) }}</p>
                                </div>
                            </div>
                            <div class="flex opacity-0 group-hover:opacity-100 transition-opacity">
                                <button class="p-1.5 text-slate-400 hover:text-royal-600 dark:hover:text-royal-400" title="Edit">
                                    <Edit2 class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                        
                        <!-- Card Body (Value Calculation) -->
                        <div class="p-6 flex-grow flex flex-col justify-center space-y-4">
                            <div>
                                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Nilai Beli Awal</p>
                                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ maskValue(IDR.format(asset.initialValue)) }}</p>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                <div class="h-px bg-slate-200 dark:bg-slate-700 flex-grow"></div>
                                <div :class="['flex items-center gap-1 text-xs font-medium px-2 py-1 rounded-full', asset.depreciationRate > 0 ? 'bg-rose-50 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400' : 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400']">
                                    <TrendingDown v-if="asset.depreciationRate > 0" class="w-3 h-3" />
                                    <ArrowDownRight v-else class="w-3 h-3 transform rotate-180" />
                                    {{ Math.abs(asset.depreciationRate) }}% /thn
                                </div>
                                <div class="h-px bg-slate-200 dark:bg-slate-700 flex-grow"></div>
                            </div>
                            
                            <div>
                                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Estimasi Nilai Saat Ini</p>
                                <p :class="['text-2xl font-bold tracking-tight', asset.depreciationRate > 0 ? 'text-amber-600 dark:text-amber-500' : 'text-emerald-600 dark:text-emerald-400']">
                                    {{ maskValue(IDR.format(getCurrentValue(asset))) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State Add Button -->
                    <button @click="isAddModalOpen = true" class="rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-700 flex flex-col items-center justify-center p-6 min-h-[250px] hover:border-royal-500 hover:bg-royal-50 dark:hover:bg-royal-900/20 transition-all text-slate-500 hover:text-royal-600 dark:text-slate-400 dark:hover:text-royal-400 group">
                        <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-3 group-hover:bg-royal-100 dark:group-hover:bg-royal-900/50 transition-colors">
                            <Plus class="w-6 h-6" />
                        </div>
                        <h3 class="font-semibold text-lg">Catat Aset Baru</h3>
                        <p class="text-sm mt-1 text-center px-4">Tambahkan Properti, Kendaraan, atau Logam Mulia.</p>
                    </button>
                </div>
            </div>
        </div>

        <!-- Add Asset Modal -->
        <div v-if="isAddModalOpen" class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="isAddModalOpen = false"></div>
            
            <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/80">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Tambah Aset Baru</h3>
                    <button @click="isAddModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                        <X class="w-5 h-5" />
                    </button>
                </div>
                
                <form @submit.prevent="submitAddAsset" class="p-6">
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <InputLabel for="asset_name" class="flex items-center gap-1.5"><Type class="w-4 h-4 text-slate-400" /> Nama Aset</InputLabel>
                                <TextInput id="asset_name" v-model="form.name" type="text" class="mt-1 block w-full" placeholder="Cth: MacBook Pro M2" required autofocus />
                                <div v-if="form.errors.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</div>
                            </div>
                            
                            <div>
                                <InputLabel for="asset_type" class="flex items-center gap-1.5"><Tags class="w-4 h-4 text-slate-400" /> Kategori</InputLabel>
                                <select id="asset_type" v-model="form.type" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 shadow-sm focus:border-royal-500 focus:ring-royal-500">
                                    <option v-for="type in assetTypes" :key="type" :value="type">{{ type }}</option>
                                </select>
                                <div v-if="form.errors.type" class="text-sm text-red-600 mt-1">{{ form.errors.type }}</div>
                            </div>
                            
                            <div>
                                <InputLabel for="purchase_date" class="flex items-center gap-1.5"><CalendarDays class="w-4 h-4 text-slate-400" /> Tanggal Pembelian</InputLabel>
                                <TextInput id="purchase_date" v-model="form.purchaseDate" type="date" class="mt-1 block w-full" required />
                                <div v-if="form.errors.purchaseDate" class="text-sm text-red-600 mt-1">{{ form.errors.purchaseDate }}</div>
                            </div>

                            <div class="md:col-span-2">
                                <InputLabel for="initial_value" class="flex items-center gap-1.5"><CircleDollarSign class="w-4 h-4 text-slate-400" /> Nilai Beli Awal</InputLabel>
                                <div class="relative mt-1 border-slate-300 dark:border-slate-700 shadow-sm rounded-md">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-slate-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="number" id="initial_value" v-model="form.initialValue" class="block w-full pl-10 rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-royal-500 focus:ring-royal-500 sm:text-sm" placeholder="0" required />
                                </div>
                                <div v-if="form.errors.initialValue" class="text-sm text-red-600 mt-1">{{ form.errors.initialValue }}</div>
                            </div>
                            
                            <div class="md:col-span-2">
                                <InputLabel for="depreciation_rate" class="flex items-center gap-1.5"><TrendingDown class="w-4 h-4 text-slate-400" /> Depresiasi per Tahun (%)</InputLabel>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">Gunakan angka minus (-) jika nilai aset diperkirakan naik (apresiasi).</p>
                                <div class="relative mt-1 border-slate-300 dark:border-slate-700 shadow-sm rounded-md w-1/2">
                                    <input type="number" step="0.1" id="depreciation_rate" v-model="form.depreciationRate" class="block w-full pr-8 rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-royal-500 focus:ring-royal-500 sm:text-sm" placeholder="10" required />
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-slate-500 sm:text-sm">%</span>
                                    </div>
                                </div>
                                <div v-if="form.errors.depreciationRate" class="text-sm text-red-600 mt-1">{{ form.errors.depreciationRate }}</div>
                            </div>
                        </div>

                    </div>

                    <div class="mt-8 flex justify-end gap-3 border-t border-slate-100 dark:border-slate-700 pt-5">
                        <button type="button" @click="isAddModalOpen = false" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-md transition-colors">Batal</button>
                        <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="bg-royal-600 hover:bg-royal-700">Simpan Aset</PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
