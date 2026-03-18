<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { Users, UserPlus, ArrowLeft, Loader2 } from 'lucide-vue-next';

// State untuk navigasi internal onboarding
const view = ref('selection'); // 'selection', 'create', 'join'

// Form untuk Membuat Keluarga
const createForm = useForm({
    name: '',
});

// Form untuk Bergabung Keluarga
const joinForm = useForm({
    invite_code: '',
});

// Reset error saat pindah view agar tidak membingungkan user
watch(view, () => {
    createForm.clearErrors();
    joinForm.clearErrors();
});

const submitCreate = () => {
    createForm.post(route('family.create'), {
        onFinish: () => createForm.reset('name'),
    });
};

const submitJoin = () => {
    joinForm.post(route('family.join'), {
        onFinish: () => joinForm.reset('invite_code'),
    });
};

// Fungsi helper agar input invite_code otomatis Uppercase saat diketik
const handleInviteInput = (e) => {
    joinForm.invite_code = e.target.value.toUpperCase();
};
</script>

<template>
    <GuestLayout>
        <Head title="Mulai Keluarga" />

        <div v-if="view === 'selection'" class="text-center animate-in fade-in duration-500">
            <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100 mb-2">Selamat Datang di Family Finance!</h2>
            <p class="text-slate-500 dark:text-slate-400 mb-8">Pilih cara Anda ingin memulai kolaborasi keuangan.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <button @click="view = 'create'" 
                    class="flex flex-col items-center p-6 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl hover:border-blue-500 dark:hover:border-blue-500 transition-all group text-left shadow-sm">
                    <div class="w-14 h-14 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 mb-4 group-hover:scale-110 transition-transform">
                        <Users class="w-7 h-7" />
                    </div>
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Buat Keluarga Baru</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 text-center">Saya kepala keluarga dan ingin membuat ruang kolaborasi baru.</p>
                </button>

                <button @click="view = 'join'" 
                    class="flex flex-col items-center p-6 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl hover:border-indigo-500 dark:hover:border-indigo-500 transition-all group text-left shadow-sm">
                    <div class="w-14 h-14 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 mb-4 group-hover:scale-110 transition-transform">
                        <UserPlus class="w-7 h-7" />
                    </div>
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Gabung Keluarga</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 text-center">Pasangan saya sudah memiliki akun dan saya punya Kode Undangan.</p>
                </button>
            </div>
        </div>

        <div v-else-if="view === 'create'" class="animate-in slide-in-from-right duration-300">
            <button @click="view = 'selection'" class="flex items-center text-sm text-blue-600 dark:text-blue-400 hover:underline mb-6 group">
                <ArrowLeft class="w-4 h-4 mr-1 group-hover:-translate-x-1 transition-transform" /> Kembali
            </button>
            
            <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100 mb-2">Buat Ruang Keluarga</h2>
            <p class="text-slate-500 dark:text-slate-400 mb-6">Berikan nama untuk ruang finansial keluarga Anda.</p>

            <form @submit.prevent="submitCreate" class="space-y-4">
                <div>
                    <InputLabel for="name" value="Nama Keluarga" />
                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="createForm.name"
                        required
                        autofocus
                        placeholder="Contoh: Keluarga Afrizal"
                    />
                    <InputError class="mt-2" :message="createForm.errors.name" />
                </div>

                <PrimaryButton :class="{ 'opacity-50': createForm.processing }" :disabled="createForm.processing" class="w-full h-11 justify-center bg-blue-600 hover:bg-blue-700">
                    <Loader2 v-if="createForm.processing" class="w-4 h-4 mr-2 animate-spin" />
                    {{ createForm.processing ? 'Memproses...' : 'Buat Keluarga' }}
                </PrimaryButton>
            </form>
        </div>

        <div v-else-if="view === 'join'" class="animate-in slide-in-from-right duration-300">
            <button @click="view = 'selection'" class="flex items-center text-sm text-blue-600 dark:text-blue-400 hover:underline mb-6 group">
                <ArrowLeft class="w-4 h-4 mr-1 group-hover:-translate-x-1 transition-transform" /> Kembali
            </button>

            <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100 mb-2">Gabung Keluarga</h2>
            <p class="text-slate-500 dark:text-slate-400 mb-6">Masukkan Kode Undangan dari pasangan Anda.</p>

            <form @submit.prevent="submitJoin" class="space-y-4">
                <div>
                    <InputLabel for="invite_code" value="Kode Undangan" />
                    <TextInput
                        id="invite_code"
                        type="text"
                        class="mt-1 block w-full uppercase font-mono tracking-widest"
                        v-model="joinForm.invite_code"  @input="joinForm.invite_code = $event.target.value.toUpperCase()" required
                        autofocus
                        placeholder="KODE8KARAKTER"
                    />
                    <InputError class="mt-2" :message="joinForm.errors.invite_code" />
                </div>

                <PrimaryButton :class="{ 'opacity-50': joinForm.processing }" :disabled="joinForm.processing" class="w-full h-11 justify-center bg-blue-600 hover:bg-blue-700">
                    <Loader2 v-if="joinForm.processing" class="w-4 h-4 mr-2 animate-spin" />
                    {{ joinForm.processing ? 'Mengecek Kode...' : 'Bergabung' }}
                </PrimaryButton>
            </form>
        </div>
    </GuestLayout>
</template>