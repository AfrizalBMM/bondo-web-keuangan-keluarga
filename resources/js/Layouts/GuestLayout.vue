<script setup>
import { Link } from '@inertiajs/vue3';
import { Wallet, ShieldCheck, TrendingUp, Sparkles, Activity } from 'lucide-vue-next';
import GlobalAlert from '@/Components/GlobalAlert.vue';
</script>

<template>
    <div class="flex min-h-screen bg-white dark:bg-slate-900 selection:bg-royalblue-500 selection:text-white">
        <!-- Left Side: Animated Hero / Illustration -->
        <div class="relative hidden w-0 flex-1 lg:block bg-gradient-to-br from-royalblue-900 via-royalblue-800 to-indigo-900 overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxwYXRoIGQ9Ik0zNiAzNHYtbDItMiAyaDQyYmExNiAxNiAwIDAgMCAxNi0xNnoiIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSIvPjwvZz48L3N2Zz4=')] opacity-20"></div>
            
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-royalblue-500/30 rounded-full blur-[120px] mix-blend-screen mix-blend-overlay"></div>
            
            <div class="absolute inset-0 flex flex-col justify-center items-center p-16 text-white text-center z-10">
                
                <!-- Animated Floating Hero SVG -->
                <div class="relative w-80 h-80 mb-12">
                    <!-- Main Platform -->
                    <svg class="absolute inset-0 w-full h-full text-white/10" viewBox="0 0 200 200" fill="currentColor">
                        <path d="M100 150 L20 100 L100 50 L180 100 Z" />
                    </svg>

                    <!-- Main Shield (Floating) -->
                    <div class="absolute inset-0 flex items-center justify-center animate-bounce-slow">
                        <div class="relative">
                            <div class="absolute -inset-4 bg-royalblue-400/30 rounded-full blur-xl animate-pulse"></div>
                            <ShieldCheck class="w-32 h-32 text-white drop-shadow-2xl" stroke-width="1.5" />
                        </div>
                    </div>

                    <!-- Orbiting Elements -->
                    <div class="absolute inset-0 animate-spin-slow">
                        <div class="absolute top-10 left-10 p-3 bg-emerald-500 rounded-2xl shadow-xl transform -rotate-12 backdrop-blur-sm bg-opacity-90">
                            <TrendingUp class="w-6 h-6 text-white" />
                        </div>
                    </div>

                    <div class="absolute inset-0 animate-spin-reverse-slow">
                        <div class="absolute bottom-10 right-10 p-3 bg-amber-500 rounded-2xl shadow-xl transform rotate-12 backdrop-blur-sm bg-opacity-90">
                            <Wallet class="w-6 h-6 text-white" />
                        </div>
                    </div>
                    
                    <div class="absolute top-1/2 right-0 -translate-y-1/2 translate-x-1/2 animate-float">
                         <div class="p-2 bg-indigo-500 rounded-xl shadow-lg backdrop-blur-sm bg-opacity-90">
                            <Sparkles class="w-5 h-5 text-indigo-100" />
                        </div>
                    </div>
                    
                    <div class="absolute bottom-20 left-0 -translate-x-1/2 animate-float-delayed">
                         <div class="p-2 bg-rose-500 rounded-xl shadow-lg backdrop-blur-sm bg-opacity-90">
                            <Activity class="w-5 h-5 text-rose-100" />
                        </div>
                    </div>
                </div>

                <h1 class="text-4xl font-extrabold tracking-tight mb-4 text-transparent bg-clip-text bg-gradient-to-r from-white to-royalblue-200">
                    Bondo - Keuangan Keluarga 
                </h1>
                <p class="text-lg text-royalblue-100/80 max-w-md font-medium">
                    Kolaborasi finansial yang elegan, aman, dan transparan untuk keluarga modern.
                </p>
            </div>
        </div>

        <!-- Right Side: Auth Form Container -->
        <div class="flex flex-1 flex-col justify-center px-4 py-12 sm:px-6 lg:flex-none lg:px-20 xl:px-24">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                <!-- Mobile Logo (Hidden on Desktop) -->
                <div class="lg:hidden flex justify-center mb-8">
                    <Link href="/" class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-royalblue-600 flex items-center justify-center shadow-lg shadow-royalblue-500/30">
                            <ShieldCheck class="w-7 h-7 text-white" />
                        </div>
                        <span class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-royalblue-600 to-indigo-600 dark:from-royalblue-400 dark:to-indigo-400">Hub</span>
                    </Link>
                </div>

                <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700/50">
                    <slot />
                </div>
                
                <p class="mt-8 text-center text-sm text-slate-500 dark:text-slate-400">
                    &copy; 2026 Bondo. All rights reserved. - Powered by Reditech
                </p>
            </div>
        </div>
        <GlobalAlert />
    </div>
</template>

<style>
.animate-bounce-slow {
    animation: bounce 4s infinite ease-in-out;
}
.animate-spin-slow {
    animation: spin 15s linear infinite;
}
.animate-spin-reverse-slow {
    animation: spin-reverse 20s linear infinite;
}
.animate-float {
    animation: float 5s ease-in-out infinite;
}
.animate-float-delayed {
    animation: float 5s ease-in-out 2.5s infinite;
}

@keyframes spin-reverse {
    from { transform: rotate(360deg); }
    to { transform: rotate(0deg); }
}
@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}
</style>
