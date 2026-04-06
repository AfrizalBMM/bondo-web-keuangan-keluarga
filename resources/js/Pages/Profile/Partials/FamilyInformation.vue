<script setup>
import { ref } from 'vue';
import { usePage, router, useForm } from '@inertiajs/vue3';
import { Users, Copy, LogOut, CheckCircle2, ShieldCheck, Info, ShieldAlert, Edit, Calendar, Mail, UserCheck, Trash2, X } from 'lucide-vue-next';
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { useConfirm } from '@/Composables/useConfirm';

const { confirm: confirmModal } = useConfirm();

const page = usePage();
const user = page.props.auth.user;
const family = user.family;

const isEditModalOpen = ref(false);
const editForm = useForm({
    name: family?.name || '',
});

const openEditModal = () => {
    editForm.name = family.name;
    isEditModalOpen.value = true;
};

const updateFamily = () => {
    editForm.patch(route('family.update'), {
        onSuccess: () => {
            isEditModalOpen.value = false;
        },
    });
};

const canDelete = family?.users?.length <= 1;

const copied = ref(false);
const copyInviteCode = () => {
    if (!family?.invite_code) return;
    navigator.clipboard.writeText(family.invite_code);
    copied.value = true;
    setTimeout(() => copied.value = false, 2000);
};

const leaveFamily = () => {
    confirmModal({
        title: 'Keluar dari Keluarga?',
        message: 'Apakah Anda yakin ingin keluar dari keluarga? Anda akan kehilangan akses ke data keuangan bersama ini.',
        confirmText: 'Ya, Keluar',
        onConfirm: () => {
            router.post(route('family.leave'));
        }
    });
};

const deleteFamily = () => {
    confirmModal({
        title: 'Hapus & Bubarkan Keluarga?',
        message: 'PERINGATAN: Menghapus keluarga akan menghapus SEMUA data transaksi, dompet, dan anggaran terkait keluarga ini secara permanen. Lanjutkan?',
        confirmText: 'Ya, Hapus Semua',
        onConfirm: () => {
            router.delete(route('family.destroy'));
        }
    });
};

const removeMember = (member) => {
    confirmModal({
        title: 'Keluarkan Anggota?',
        message: `Apakah Anda yakin ingin mengeluarkan ${member.name} dari grup keluarga?`,
        confirmText: 'Ya, Keluarkan',
        onConfirm: () => {
            router.delete(route('family.members.destroy', member.id));
        }
    });
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

                <div v-if="user.role === 'Head'" class="flex items-center gap-2">
                    <button 
                        @click="openEditModal"
                        class="p-2 text-slate-400 hover:text-royal-500 transition-colors"
                        title="Edit Nama Keluarga"
                    >
                        <Edit class="w-4 h-4" />
                    </button>
                    <div class="flex items-center gap-1.5 px-3 py-1 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 rounded-full border border-emerald-100 dark:border-emerald-800/50">
                        <ShieldCheck class="w-3.5 h-3.5" />
                        <span class="text-[10px] font-bold uppercase tracking-tighter">Kepala Keluarga</span>
                    </div>
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

                <!-- Family Members Table -->
                <div class="pt-6 border-t border-slate-100 dark:border-slate-800">
                    <div class="flex items-center justify-between mb-4">
                        <h5 class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <UserCheck class="w-3.5 h-3.5 text-royal-500" />
                            Anggota Keluarga yang Bergabung
                        </h5>
                        <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-500 rounded text-[10px] font-bold">
                            {{ family.users?.length || 0 }} Anggota
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-tighter border-b border-slate-100 dark:border-slate-800">
                                    <th class="pb-3 pl-2">Nama</th>
                                    <th class="pb-3">Role</th>
                                    <th class="pb-3 text-right pr-2">Bergabung</th>
                                    <th v-if="user.role === 'Head'" class="pb-3 text-right pr-2">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                                <tr v-for="member in family.users" :key="member.id" class="group">
                                    <td class="py-3 pl-2">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-slate-700 dark:text-slate-200">
                                                {{ member.name }}
                                                <span v-if="member.id === user.id" class="ml-1 text-[10px] text-royal-500">(Anda)</span>
                                            </span>
                                            <span class="text-[10px] text-slate-400 flex items-center gap-1">
                                                <Mail class="w-2.5 h-2.5" /> {{ member.email }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <span 
                                            class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest"
                                            :class="member.role === 'Head' ? 'bg-royal-100 text-royal-600 dark:bg-royal-900/30 dark:text-royal-400' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'"
                                        >
                                            {{ member.role === 'Head' ? 'Kepala' : 'Anggota' }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-right pr-2 text-[10px] text-slate-500 font-medium">
                                        {{ new Date(member.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) }}
                                    </td>
                                    <td v-if="user.role === 'Head'" class="py-3 text-right pr-2">
                                        <button 
                                            v-if="member.id !== user.id"
                                            @click="removeMember(member)"
                                            class="p-1.5 text-slate-400 hover:text-rose-500 transition-colors"
                                            title="Keluarkan Anggota"
                                        >
                                            <Trash2 class="w-3.5 h-3.5" />
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                    <div v-if="user.role === 'Head'" class="flex flex-col items-end gap-2">
                        <DangerButton 
                            @click="deleteFamily"
                            :disabled="!canDelete"
                            class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-[11px] font-bold uppercase tracking-widest transition-all bg-rose-600 text-white hover:bg-rose-700 shadow-lg shadow-rose-500/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:grayscale"
                        >
                            <ShieldAlert class="w-3.5 h-3.5" />
                            Hapus & Bubarkan Keluarga
                        </DangerButton>
                        <p v-if="!canDelete" class="text-[10px] text-rose-500 italic font-bold">
                            * Keluarkan semua anggota lain sebelum membubarkan grup.
                        </p>
                    </div>

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

        <!-- Edit Family Modal -->
        <Modal :show="isEditModalOpen" @close="isEditModalOpen = false">
            <div class="bg-white dark:bg-slate-900 rounded-3xl overflow-hidden shadow-2xl border border-white/10">
                <!-- Modal Header -->
                <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-900/50">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-royal-100 dark:bg-royal-900/30 rounded-xl">
                            <Edit class="w-5 h-5 text-royal-600" />
                        </div>
                        <h3 class="text-xl font-black text-slate-900 dark:text-white">Edit Nama Keluarga</h3>
                    </div>
                    <button @click="isEditModalOpen = false" class="p-2 rounded-full hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors">
                        <X class="w-6 h-6 text-slate-400" />
                    </button>
                </div>

                <!-- Modal Body -->
                <form @submit.prevent="updateFamily" class="p-8 space-y-6">
                    <p class="text-xs text-slate-500 dark:text-slate-400 -mt-2">
                        Sesuaikan nama identitas untuk grup keluarga Anda. Semua biaya dan aset akan terikat pada nama ini.
                    </p>

                    <div>
                        <InputLabel for="family_name" value="Nama Keluarga" class="font-bold text-slate-700 dark:text-slate-300 mb-2" />
                        <TextInput
                            id="family_name"
                            v-model="editForm.name"
                            type="text"
                            class="w-full rounded-2xl border-slate-200 dark:border-slate-700 dark:bg-slate-950 dark:text-white focus:ring-royal-500 py-3 px-4"
                            placeholder="Contoh: Keluarga Budi (Admin)"
                            required
                        />
                        <InputError :message="editForm.errors.name" class="mt-2" />
                    </div>

                    <div class="pt-4 flex gap-3">
                        <SecondaryButton 
                            type="button"
                            @click="isEditModalOpen = false"
                            class="flex-1 justify-center py-3.5 rounded-2xl font-bold uppercase tracking-widest text-xs"
                        >
                            Batal
                        </SecondaryButton>
                        <PrimaryButton 
                            class="flex-[2] bg-royal-600 hover:bg-royal-700 py-3.5 rounded-2xl shadow-xl shadow-royal-500/20 justify-center text-xs font-bold uppercase tracking-widest" 
                            :class="{ 'opacity-25': editForm.processing }" 
                            :disabled="editForm.processing"
                        >
                            Simpan Perubahan
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </section>
</template>