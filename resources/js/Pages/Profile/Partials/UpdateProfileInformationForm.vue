<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Camera, Upload, User } from 'lucide-vue-next';

defineProps({
    mustVerifyEmail: { type: Boolean },
    status: { type: String },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});

// --- Avatar Upload Logic ---
const avatarInput = ref(null);
const avatarPreview = ref(null);
const avatarForm = useForm({ avatar: null });

const currentAvatarUrl = computed(() => {
    if (avatarPreview.value) return avatarPreview.value;
    if (user.avatar) return `/storage/avatars/${user.avatar}`;
    return null;
});

const userInitials = computed(() => {
    return user.name?.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase() || '?';
});

function selectAvatar() {
    avatarInput.value.click();
}

function onAvatarChange(e) {
    const file = e.target.files[0];
    if (!file) return;
    
    // Tampilkan preview langsung
    const reader = new FileReader();
    reader.onload = (ev) => { avatarPreview.value = ev.target.result; };
    reader.readAsDataURL(file);
    
    // Simpan file ke form dan langsung upload
    avatarForm.avatar = file;
    uploadAvatar();
}

function uploadAvatar() {
    avatarForm.post(route('profile.avatar'), {
        onSuccess: () => {
            // Update sudah sukses, preview tetap tampil
        },
        onError: () => {
            avatarPreview.value = null;
        },
    });
}
</script>

<template>
    <section>
        <!-- Avatar Upload Section -->
        <div class="flex items-center gap-6 mb-8 pb-8 border-b border-slate-100 dark:border-slate-700">
            <!-- Avatar Display -->
            <div class="relative flex-shrink-0">
                <div class="w-24 h-24 rounded-2xl overflow-hidden ring-4 ring-white dark:ring-slate-800 shadow-lg">
                    <img
                        v-if="currentAvatarUrl"
                        :src="currentAvatarUrl"
                        :alt="user.name"
                        class="w-full h-full object-cover"
                    />
                    <div
                        v-else
                        class="w-full h-full bg-gradient-to-br from-royal-500 to-royal-700 flex items-center justify-center"
                    >
                        <span class="text-white text-2xl font-bold">{{ userInitials }}</span>
                    </div>
                </div>

                <!-- Upload Trigger Button -->
                <button
                    type="button"
                    @click="selectAvatar"
                    class="absolute -bottom-2 -right-2 w-8 h-8 bg-royal-500 hover:bg-royal-600 text-white rounded-xl shadow-md flex items-center justify-center transition-colors cursor-pointer"
                    :disabled="avatarForm.processing"
                    title="Ganti Foto Profil"
                >
                    <Camera class="w-4 h-4" v-if="!avatarForm.processing" />
                    <svg v-else class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </button>

                <!-- Hidden File Input -->
                <input
                    ref="avatarInput"
                    type="file"
                    class="hidden"
                    accept="image/jpg,image/jpeg,image/png,image/webp"
                    @change="onAvatarChange"
                />
            </div>

            <!-- Info Text -->
            <div>
                <h3 class="text-base font-bold text-slate-800 dark:text-white">{{ user.name }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ user.email }}</p>
                <button
                    type="button"
                    @click="selectAvatar"
                    class="mt-2 inline-flex items-center gap-1.5 text-xs text-royal-600 dark:text-royal-400 hover:underline font-medium transition"
                    :disabled="avatarForm.processing"
                >
                    <Upload class="w-3 h-3" />
                    {{ avatarForm.processing ? 'Mengunggah...' : 'Ganti foto profil' }}
                </button>
                <p class="text-xs text-slate-400 mt-0.5">JPG, PNG, WebP. Maks 2MB.</p>
                <InputError :message="avatarForm.errors.avatar" class="mt-1" />
            </div>
        </div>

        <!-- Profile Info Form -->
        <header class="mb-6">
            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">
                Informasi Profil
            </h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Perbarui nama dan alamat email akun Anda.
            </p>
        </header>

        <form @submit.prevent="form.patch(route('profile.update'))" class="space-y-5">
            <div>
                <InputLabel for="name" value="Nama Lengkap" />
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
                <InputLabel for="email" value="Email" />
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

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-amber-700 dark:text-amber-400">
                    Email Anda belum diverifikasi.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="underline font-medium hover:text-amber-900"
                    >
                        Kirim ulang email verifikasi.
                    </Link>
                </p>
                <div v-show="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                    Link verifikasi baru telah dikirim ke email Anda.
                </div>
            </div>

            <div class="flex items-center gap-4 pt-2">
                <PrimaryButton :disabled="form.processing">Simpan Perubahan</PrimaryButton>
                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-green-600 dark:text-green-400 font-medium">
                        ✓ Tersimpan!
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
