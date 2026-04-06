<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, onMounted, nextTick, watch } from 'vue';
import { Send, Bot, Sparkles, User, Info, AlertCircle, Loader2 } from 'lucide-vue-next';
import axios from 'axios';

const messages = ref([
    {
        role: 'ai',
        content: 'Halo! Saya Bondo AI, asisten keuangan keluarga Anda. Berdasarkan data keuangan Anda saat ini, saya siap membantu menganalisa pengeluaran, memberikan saran penghematan, atau menjawab pertanyaan finansial lainnya. Ada yang ingin Anda tanyakan?'
    }
]);

const newMessage = ref('');
const isProcessing = ref(false);
const chatScroll = ref(null);

const scrollToBottom = async () => {
    await nextTick();
    if (chatScroll.value) {
        chatScroll.value.scrollTop = chatScroll.value.scrollHeight;
    }
};

onMounted(scrollToBottom);

const suggestedPrompts = [
    "Analisa keuangan saya bulan ini",
    "Beri saran penghematan",
    "Bagaimana progres goal saya?",
    "Cek pengeluaran terbesar saya"
];

const usePrompt = (prompt) => {
    newMessage.value = prompt;
    sendMessage();
};

const sendMessage = async () => {
    if (!newMessage.value.trim() || isProcessing.value) return;

    const userMsg = newMessage.value;
    messages.value.push({ role: 'user', content: userMsg });
    newMessage.value = '';
    isProcessing.value = true;
    
    await scrollToBottom();

    try {
        const response = await axios.post(route('ai.chat'), {
            message: userMsg,
            history: messages.value.slice(1, -1) // Kirim history tanpa pesan pertama AI dan pesan terakhir user
        });

        messages.value.push({
            role: 'ai',
            content: response.data.response
        });
    } catch (error) {
        messages.value.push({
            role: 'ai',
            content: error.response?.data?.error || 'Maaf, terjadi kesalahan saat menghubungi asisten AI. Pastikan API Key sudah terkonfigurasi.'
        });
    } finally {
        isProcessing.value = false;
        await scrollToBottom();
    }
};
</script>

<template>
    <Head title="AI Advisor" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-royal-600 rounded-2xl shadow-lg shadow-royal-500/30">
                    <Sparkles class="w-5 h-5 text-white" />
                </div>
                <div>
                    <h2 class="font-black text-xl text-slate-800 dark:text-white leading-tight">Bondo AI</h2>
                    <p class="text-xs text-slate-500 font-medium">Asisten Finansial Keluarga Pintar</p>
                </div>
            </div>
        </template>

        <div class="py-6 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 h-[calc(100vh-14rem)] flex flex-col">
            <!-- Chat Container -->
            <div 
                ref="chatScroll"
                class="flex-1 overflow-y-auto space-y-6 pr-2 custom-scrollbar pb-6"
            >
                <div v-for="(msg, index) in messages" :key="index" :class="['flex', msg.role === 'ai' ? 'justify-start' : 'justify-end']">
                    <div :class="[
                        'max-w-[85%] sm:max-w-[75%] rounded-3xl p-4 sm:p-5 shadow-sm transition-all animate-in fade-in slide-in-from-bottom-2',
                        msg.role === 'ai' 
                            ? 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-tl-none border border-slate-100 dark:border-slate-700/50' 
                            : 'bg-royal-600 text-white rounded-tr-none shadow-royal-500/20'
                    ]">
                        <!-- Icon & Sender -->
                        <div class="flex items-center gap-2 mb-2">
                            <component :is="msg.role === 'ai' ? Bot : User" class="w-4 h-4 opacity-70" />
                            <span class="text-[10px] font-black uppercase tracking-widest opacity-70">
                                {{ msg.role === 'ai' ? 'Bondo AI' : 'Anda' }}
                            </span>
                        </div>
                        
                        <!-- Content -->
                        <div class="text-sm sm:text-base leading-relaxed whitespace-pre-wrap font-medium">
                            {{ msg.content }}
                        </div>
                    </div>
                </div>

                <!-- Processing Message -->
                <div v-if="isProcessing" class="flex justify-start">
                    <div class="bg-white dark:bg-slate-800 rounded-3xl rounded-tl-none p-5 shadow-sm border border-slate-100 dark:border-slate-700/50 flex items-center gap-3">
                        <div class="flex gap-1">
                            <span class="w-2 h-2 bg-royal-500 rounded-full animate-bounce"></span>
                            <span class="w-2 h-2 bg-royal-500 rounded-full animate-bounce [animation-delay:0.2s]"></span>
                            <span class="w-2 h-2 bg-royal-500 rounded-full animate-bounce [animation-delay:0.4s]"></span>
                        </div>
                        <span class="text-xs text-slate-400 font-bold uppercase tracking-widest">Menganalisa data...</span>
                    </div>
                </div>
            </div>

            <!-- Suggested Prompts -->
            <div v-if="messages.length === 1" class="mb-6 overflow-x-auto no-scrollbar py-2">
                <div class="flex gap-3 min-w-max">
                    <button 
                        v-for="prompt in suggestedPrompts" 
                        :key="prompt"
                        @click="usePrompt(prompt)"
                        class="px-4 py-2 bg-royal-50 dark:bg-royal-900/20 text-royal-600 dark:text-royal-400 border border-royal-200/50 dark:border-royal-800/50 rounded-full text-xs font-bold hover:bg-royal-600 hover:text-white transition-all shadow-sm whitespace-nowrap"
                    >
                        {{ prompt }}
                    </button>
                </div>
            </div>

            <!-- Input Area -->
            <div class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-800 p-2 sm:p-3">
                <form @submit.prevent="sendMessage" class="flex items-center gap-2">
                    <input 
                        v-model="newMessage"
                        type="text"
                        placeholder="Tanyakan sesuatu tentang keuangan Anda..."
                        class="flex-1 bg-transparent border-none focus:ring-0 text-slate-700 dark:text-slate-200 text-sm sm:text-base px-4 font-medium"
                        :disabled="isProcessing"
                    />
                    <button 
                        type="submit"
                        :disabled="!newMessage.trim() || isProcessing"
                        class="p-3 bg-royal-600 text-white rounded-2xl shadow-lg shadow-royal-500/30 hover:scale-105 active:scale-95 transition-all disabled:opacity-50 disabled:scale-100 disabled:grayscale"
                    >
                        <Send v-if="!isProcessing" class="w-5 h-5" />
                        <Loader2 v-else class="w-5 h-5 animate-spin" />
                    </button>
                </form>
            </div>
            <p class="text-center mt-3 text-[10px] text-slate-400 font-medium">
                <Info class="w-3 h-3 inline-block -mt-0.5 mr-0.5" />
                AI dapat melakukan kesalahan analitik. Verifikasi kembali data penting Anda.
            </p>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
    background: #334155;
}
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
