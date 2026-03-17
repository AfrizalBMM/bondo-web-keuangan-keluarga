<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="mb-8">
            <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white">Selamat Datang</h2>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Silakan masuk ke akun Anda untuk mengelola keuangan keluarga.</p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <InputLabel for="email" value="Alamat Email" class="text-slate-700 dark:text-slate-300" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
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
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between mt-4">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" class="text-royalblue-600 focus:ring-royalblue-500" />
                    <span class="ms-2 text-sm text-slate-600 dark:text-slate-400">Ingat Saya</span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm font-medium text-royalblue-600 hover:text-royalblue-500 dark:text-royalblue-400 dark:hover:text-royalblue-300"
                >
                    Lupa sandi?
                </Link>
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full flex justify-center py-3 bg-royalblue-600 hover:bg-royalblue-700 focus:bg-royalblue-700 active:bg-royalblue-800 dark:bg-royalblue-600 dark:hover:bg-royalblue-500"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Masuk ke Dashboard
                </PrimaryButton>
            </div>
            
            <p class="text-center text-sm text-slate-600 dark:text-slate-400 mt-6">
                Belum punya akun?
                <Link :href="route('register')" class="font-semibold text-royalblue-600 hover:text-royalblue-500 dark:text-royalblue-400 dark:hover:text-royalblue-300">
                    Daftar Sekarang
                </Link>
            </p>
        </form>
    </GuestLayout>
</template>
