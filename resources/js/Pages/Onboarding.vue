<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { Users, UserPlus } from 'lucide-vue-next';

const view = ref('selection'); // 'selection', 'create', 'join'

const createForm = useForm({
    name: '',
});

const joinForm = useForm({
    invite_code: '',
});

const submitCreate = () => {
    createForm.post(route('family.create'));
};

const submitJoin = () => {
    joinForm.post(route('family.join'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Mulai Keluarga" />

        <div v-if="view === 'selection'" class="text-center">
            <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100 mb-2">Selamat Datang di Family Finance!</h2>
            <p class="text-slate-500 dark:text-slate-400 mb-8">Pilih cara Anda ingin memulai kolaborasi keuangan.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <button @click="view = 'create'" class="glass-card flex flex-col items-center p-6 hover:border-royal-500 dark:hover:border-royal-500 transition-colors group text-left">
                    <div class="w-14 h-14 rounded-full bg-royal-100 dark:bg-royal-900/50 flex items-center justify-center text-royal-600 dark:text-royal-400 mb-4 group-hover:scale-110 transition-transform">
                        <Users class="w-7 h-7" />
                    </div>
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Buat Keluarga Baru</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 text-center">Saya kepala keluarga dan ingin membuat ruang kolaborasi baru.</p>
                </button>

                <button @click="view = 'join'" class="glass-card flex flex-col items-center p-6 hover:border-royal-500 dark:hover:border-royal-500 transition-colors group text-left">
                    <div class="w-14 h-14 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 mb-4 group-hover:scale-110 transition-transform">
                        <UserPlus class="w-7 h-7" />
                    </div>
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Gabung Keluarga</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 text-center">Pasangan saya sudah membuat akun dan saya memiliki Kode Undangan.</p>
                </button>
            </div>
        </div>

        <!-- Create Family View -->
        <div v-else-if="view === 'create'">
            <button @click="view = 'selection'" class="text-sm text-royal-600 dark:text-royal-400 hover:underline mb-6">&larr; Kembali</button>
            
            <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100 mb-2">Buat Ruang Keluarga</h2>
            <p class="text-slate-500 dark:text-slate-400 mb-6">Berikan nama untuk ruang finansial keluarga Anda.</p>

            <form @submit.prevent="submitCreate">
                <div>
                    <InputLabel for="name" value="Nama Keluarga" />
                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="createForm.name"
                        required
                        autofocus
                        placeholder="Contoh: Keluarga Cemara"
                    />
                    <InputError class="mt-2" :message="createForm.errors.name" />
                </div>

                <div class="mt-6 flex items-center justify-end">
                    <PrimaryButton :class="{ 'opacity-25': createForm.processing }" :disabled="createForm.processing" class="w-full justify-center bg-royal-600 hover:bg-royal-700">
                        Buat Keluarga
                    </PrimaryButton>
                </div>
            </form>
        </div>

        <!-- Join Family View -->
        <div v-else-if="view === 'join'">
            <button @click="view = 'selection'" class="text-sm text-royal-600 dark:text-royal-400 hover:underline mb-6">&larr; Kembali</button>

            <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100 mb-2">Gabung Keluarga</h2>
            <p class="text-slate-500 dark:text-slate-400 mb-6">Masukkan Kode Undangan yang diberikan oleh pasangan Anda.</p>

            <form @submit.prevent="submitJoin">
                <div>
                    <InputLabel for="invite_code" value="Kode Undangan" />
                    <TextInput
                        id="invite_code"
                        type="text"
                        class="mt-1 block w-full uppercase"
                        v-model="joinForm.invite_code"
                        required
                        autofocus
                        placeholder="Contoh: XDASCASDAS"
                    />
                    <InputError class="mt-2" :message="joinForm.errors.invite_code" />
                </div>

                <div class="mt-6 flex items-center justify-end">
                    <PrimaryButton :class="{ 'opacity-25': joinForm.processing }" :disabled="joinForm.processing" class="w-full justify-center bg-royal-600 hover:bg-royal-700">
                        Bergabung
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
