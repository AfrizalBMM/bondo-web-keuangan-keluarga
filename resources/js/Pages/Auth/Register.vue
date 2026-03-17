<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <div class="mb-8">
            <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white">Buat Akun Baru</h2>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Mulai kelola finansial keluarga Anda dengan lebih cerdas.</p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="name" value="Nama Lengkap" class="text-slate-700 dark:text-slate-300" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Alamat Email" class="text-slate-700 dark:text-slate-300" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Kata Sandi" class="text-slate-700 dark:text-slate-300" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div>
                <InputLabel for="password_confirmation" value="Konfirmasi Kata Sandi" class="text-slate-700 dark:text-slate-300" />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="pt-4">
                <PrimaryButton
                    class="w-full flex justify-center py-3 bg-royalblue-600 hover:bg-royalblue-700 focus:bg-royalblue-700 active:bg-royalblue-800 dark:bg-royalblue-600 dark:hover:bg-royalblue-500"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Daftar Akun
                </PrimaryButton>
            </div>
            
            <p class="text-center text-sm text-slate-600 dark:text-slate-400 mt-6">
                Sudah memiliki akun?
                <Link :href="route('login')" class="font-semibold text-royalblue-600 hover:text-royalblue-500 dark:text-royalblue-400 dark:hover:text-royalblue-300">
                    Masuk di sini
                </Link>
            </p>
        </form>
    </GuestLayout>
</template>
