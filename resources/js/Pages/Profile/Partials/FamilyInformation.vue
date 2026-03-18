<script setup>
import { ref } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import { Users, Copy, LogOut, CheckCircle2, ShieldCheck, Info } from 'lucide-vue-next';
import DangerButton from '@/Components/DangerButton.vue';

const page = usePage();
const user = page.props.auth.user;
const family = user.family;

const copied = ref(false);
const copyInviteCode = () => {
    if (!family?.invite_code) return;
    navigator.clipboard.writeText(family.invite_code);
    copied.value = true;
    setTimeout(() => copied.value = false, 2000);
};

const leaveFamily = () => {
    if (confirm('Apakah Anda yakin ingin keluar dari keluarga? Anda akan kehilangan akses ke data keuangan bersama ini.')) {
        router.post(route('family.leave'));
    }
};

const deleteFamily = () => {
    if (confirm('PERINGATAN: Menghapus keluarga akan menghapus SEMUA data transaksi, dompet, dan anggaran terkait keluarga ini secara permanen. Lanjutkan?')) {
        router.delete(route('family.destroy', family.id));
    }
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-slate-900 dark:text-slate-100 flex items-center gap-2">
                <Users class="w-5 h-5 text-royal-500" />
                Manajemen Keluarga
            </h2>

            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                Informasi mengenai grup keluarga Anda dan opsi untuk mengelola keanggotaan.
            </p>
        </header>

        <div v-if="family" class="overflow-hidden bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm">
            <div class="px-6 py-4 bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-royal-100 dark:bg-royal-900/30 rounded-lg text-royal-600 dark:text-royal-400">
                        <Users class="w-5 h-5" />
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-tight">
                            {{ family.name }}
                        </h4>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-bold tracking-widest">Grup Keluarga</p>
                    </div>
                </div>

                <div v-if="user.role === 'Head'" class="flex items-center gap-1.5 px-3 py-1 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 rounded-full border border-emerald-100 dark:border-emerald-800/50">
                    <ShieldCheck class="w-3.5 h-3.5" />
                    <span class="text-[10px] font-bold uppercase tracking-tighter">Kepala Keluarga</span>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <div class="relative group">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2 block">
                        Kode Undangan Keluarga
                    </label>
                    
                    <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-800/50 p-1.5 rounded-xl border border-slate-200 dark:border-slate-700 transition-all focus-within:ring-2 focus-within:ring-royal-500/20">
                        <code class="flex-1 px-3 font-mono text-sm font-bold text-royal-600 dark:text-royal-400 tracking-wider">
                            {{ family.invite_code }}
                        </code>
                        <button 
                            @click="copyInviteCode"
                            type="button"
                            class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-lg shadow-sm border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all font-bold text-xs"
                        >
                            <component :is="copied ? CheckCircle2 : Copy" class="w-3.5 h-3.5" :class="copied ? 'text-emerald-500' : ''" />
                            {{ copied ? 'Tersalin' : 'Salin' }}
                        </button>
                    </div>
                    <p class="mt-2 text-[11px] text-slate-500 flex items-center gap-1">
                        <Info class="w-3 h-3" />
                        Bagikan kode ini untuk mengundang anggota keluarga baru.
                    </p>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                    <DangerButton 
                        v-if="user.role === 'Head'"
                        @click="deleteFamily"
                        class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-[11px] font-bold uppercase tracking-widest transition-all bg-rose-600 text-white hover:bg-rose-700 shadow-lg shadow-rose-500/20"
                    >
                        <ShieldAlert class="w-3.5 h-3.5" />
                        Hapus & Bubarkan Keluarga
                    </DangerButton>

                    <DangerButton 
                        v-else
                        @click="leaveFamily"
                        class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-[11px] font-bold uppercase tracking-widest transition-all"
                    >
                        <LogOut class="w-3.5 h-3.5" />
                        Keluar dari Keluarga
                    </DangerButton>
                </div>
            </div>
        </div>

        <div v-else class="p-8 text-center bg-slate-50 dark:bg-slate-800/40 rounded-3xl border-2 border-dashed border-slate-200 dark:border-slate-700">
            <Users class="w-12 h-12 text-slate-300 mx-auto mb-3" />
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Anda belum bergabung dalam grup keluarga manapun.</p>
        </div>
    </section>
</template>