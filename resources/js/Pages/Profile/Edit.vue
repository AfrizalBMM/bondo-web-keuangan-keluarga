<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import FamilyInformation from './Partials/FamilyInformation.vue'; // Komponen baru yang kita buat tadi
import PushSubscription from '@/Components/PushSubscription.vue';
import { Head } from '@inertiajs/vue3';
import { UserCircle, BellRing, ShieldAlert } from 'lucide-vue-next';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Pengaturan Profil" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-bold tracking-tight text-slate-800 dark:text-white">
                Pengaturan Akun
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Kelola informasi pribadi, keluarga, dan keamanan akun Anda.
            </p>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl space-y-8 sm:px-6 lg:px-8">
                
                <div class="bg-white dark:bg-slate-900/50 p-6 shadow-sm border border-slate-100 dark:border-slate-800 sm:rounded-3xl sm:p-10 transition-all">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                        <div class="lg:col-span-4">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <UserCircle class="w-5 h-5 text-royal-500" />
                                Profil & Keluarga
                            </h3>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                                Perbarui identitas publik Anda dan kelola kode undangan keluarga di sini.
                            </p>
                        </div>
                        <div class="lg:col-span-8 space-y-12">
                            <UpdateProfileInformationForm
                                :must-verify-email="mustVerifyEmail"
                                :status="status"
                            />
                            
                            <FamilyInformation />
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900/50 p-6 shadow-sm border border-slate-100 dark:border-slate-800 sm:rounded-3xl sm:p-10 transition-all">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                        <div class="lg:col-span-4">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <ShieldAlert class="w-5 h-5 text-amber-500" />
                                Keamanan Akun
                            </h3>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                                Pastikan akun Anda tetap aman dengan menggunakan password yang kuat.
                            </p>
                        </div>
                        <div class="lg:col-span-8">
                            <UpdatePasswordForm />
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900/50 p-6 shadow-sm border border-slate-100 dark:border-slate-800 sm:rounded-3xl sm:p-10 transition-all">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                        <div class="lg:col-span-4">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <BellRing class="w-5 h-5 text-royal-500" />
                                Notifikasi Push
                            </h3>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                                Dapatkan pemberitahuan langsung di perangkat Anda untuk setiap transaksi keluarga.
                            </p>
                        </div>
                        <div class="lg:col-span-8">
                            <PushSubscription />
                        </div>
                    </div>
                </div>

                <div class="bg-rose-50/50 dark:bg-rose-950/10 p-6 shadow-sm border border-rose-100 dark:border-rose-900/30 sm:rounded-3xl sm:p-10 transition-all">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                        <div class="lg:col-span-4">
                            <h3 class="text-lg font-bold text-rose-600 dark:text-rose-400 flex items-center gap-2">
                                <ShieldAlert class="w-5 h-5" />
                                Area Berbahaya
                            </h3>
                            <p class="mt-2 text-sm text-rose-500/80 dark:text-rose-400/60">
                                Menghapus akun akan melenyapkan seluruh data keuangan Anda secara permanen.
                            </p>
                        </div>
                        <div class="lg:col-span-8">
                            <DeleteUserForm />
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>