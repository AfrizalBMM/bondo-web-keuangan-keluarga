<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import GlobalAlert from '@/Components/GlobalAlert.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { useTheme } from '@/Composables/useTheme';
import { useVisibility } from '@/Composables/useVisibility';
import { usePushNotification } from '@/Composables/usePushNotification';
import { 
    Moon, Sun, Eye, EyeOff, Bell, BellRing,
    LayoutDashboard, Wallet, ArrowRightLeft, 
    Tags, Briefcase, Target, CreditCard, 
    PieChart, BarChart3
} from 'lucide-vue-next';

// 1. State & Composables
const showingNavigationDropdown = ref(false);
const isAlreadySubscribed = ref(false);
const { isDark, initTheme, toggleTheme } = useTheme();
const { isVisible, toggleVisibility } = useVisibility();
const { subscribeUser, loading } = usePushNotification();
const page = usePage();

// 2. Computed
const isPushSupported = computed(() => 
    typeof window !== 'undefined' && 'serviceWorker' in navigator && 'PushManager' in window
);

// 3. Methods
const checkSubscription = async () => {
    if (isPushSupported.value) {
        try {
            const registration = await navigator.serviceWorker.ready;
            const subscription = await registration.pushManager.getSubscription();
            isAlreadySubscribed.value = !!subscription;
        } catch (e) {
            console.error("Gagal mengecek status langganan push:", e);
        }
    }
};

const handleSubscribe = async () => {
    await subscribeUser();
    await checkSubscription();
};

// 4. Lifecycle
onMounted(async () => {
    initTheme();
    await checkSubscription();

    if (page.props.auth?.user?.family_id) {
        window.Echo.private(`family.${page.props.auth.user.family_id}`)
            .listen('FamilyDataUpdated', (e) => {
                console.log('Real-time sync triggered:', e);
                router.reload({ preserveState: true, preserveScroll: true });
            });
    }
});

onUnmounted(() => {
    if (page.props.auth?.user?.family_id) {
        window.Echo.leave(`family.${page.props.auth.user.family_id}`);
    }
});
</script>

<template>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <nav class="border-b border-gray-100 bg-white dark:border-gray-700 dark:bg-gray-800">
            <div class="mx-auto max-w-screen-2xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between items-center">
    
                    <div class="flex items-center flex-1 min-w-0"> 
                        <div class="flex shrink-0 items-center">
                            <Link :href="route('dashboard')">
                                <ApplicationLogo class="block h-8 w-auto fill-current text-gray-800 dark:text-gray-200" />
                            </Link>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center overflow-hidden">
                            <div class="flex space-x-1 md:space-x-2 lg:space-x-3 text-[13px] whitespace-nowrap">
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                    <LayoutDashboard class="w-4 h-4 mr-1 hidden lg:block" /> Dashboard
                                </NavLink>
                                <NavLink :href="route('wallets')" :active="route().current('wallets')">
                                    <Wallet class="w-4 h-4 mr-1 hidden lg:block" /> Dompet
                                </NavLink>
                                <NavLink :href="route('transactions')" :active="route().current('transactions')">
                                    <ArrowRightLeft class="w-4 h-4 mr-1 hidden lg:block" /> Transaksi
                                </NavLink>
                                <NavLink :href="route('categories')" :active="route().current('categories')">
                                    <Tags class="w-4 h-4 mr-1 hidden lg:block" /> Kategori
                                </NavLink>
                                <NavLink :href="route('assets')" :active="route().current('assets')">
                                    <Briefcase class="w-4 h-4 mr-1 hidden lg:block" /> Aset
                                </NavLink>
                                <NavLink :href="route('goals')" :active="route().current('goals')">
                                    <Target class="w-4 h-4 mr-1 hidden lg:block" /> Goals
                                </NavLink>
                                <NavLink :href="route('debts')" :active="route().current('debts')">
                                    <CreditCard class="w-4 h-4 mr-1 hidden lg:block" /> Hutang
                                </NavLink>
                                <NavLink :href="route('budget')" :active="route().current('budget')">
                                    <PieChart class="w-4 h-4 mr-1 hidden lg:block" /> Budget
                                </NavLink>
                                <NavLink :href="route('reports')" :active="route().current('reports')">
                                    <BarChart3 class="w-4 h-4 mr-1 hidden lg:block" /> Laporan
                                </NavLink>
                            </div>
                        </div>
                    </div>

                    <div class="hidden sm:flex sm:items-center shrink-0 ms-4">
                        <div class="flex items-center space-x-1">
                            <button v-if="isPushSupported && !isAlreadySubscribed" @click="handleSubscribe" :disabled="loading" class="p-2 text-slate-500 hover:text-blue-500 transition-colors">
                                <BellRing class="w-5 h-5" :class="{'animate-bounce': loading}" />
                            </button>
                            <button @click="toggleTheme" class="p-2 text-slate-500 hover:text-slate-700 dark:text-slate-400">
                                <Sun v-if="isDark" class="w-5 h-5" />
                                <Moon v-else class="w-5 h-5" />
                            </button>
                            <Link :href="route('notifications')" class="relative p-2 text-slate-500 hover:text-slate-700 dark:text-slate-400">
                                <Bell class="w-5 h-5" />
                                <span class="absolute top-2 right-2 h-2 w-2 rounded-full bg-rose-500 border border-white"></span>
                            </Link>
                            <button @click="toggleVisibility" class="p-2 text-slate-500 hover:text-slate-700 dark:text-slate-400">
                                <Eye v-if="isVisible" class="w-5 h-5" />
                                <EyeOff v-else class="w-5 h-5" />
                            </button>
                        </div>

                        <div class="relative ms-3">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button type="button" class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium text-gray-500 transition hover:text-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                        {{ $page.props.auth.user.name }}
                                        <svg class="ms-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </template>
                                <template #content>
                                    <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>
                                    <DropdownLink :href="route('logout')" method="post" as="button"> Log Out </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>

                    <div class="-me-2 flex items-center sm:hidden">
                        <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-500 dark:hover:bg-gray-900">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700">
                <div class="space-y-1 pb-3 pt-2">
                    <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')"> Dashboard </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('wallets')" :active="route().current('wallets')"> Dompet </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('transactions')" :active="route().current('transactions')"> Transaksi </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('reports')" :active="route().current('reports')"> Laporan </ResponsiveNavLink>
                </div>
                <div class="border-t border-gray-200 pb-1 pt-4 dark:border-gray-600">
                    <div class="px-4">
                        <div class="text-base font-medium text-gray-800 dark:text-gray-200">{{ $page.props.auth.user.name }}</div>
                        <div class="text-sm font-medium text-gray-500">{{ $page.props.auth.user.email }}</div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <ResponsiveNavLink :href="route('profile.edit')"> Profile </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('logout')" method="post" as="button"> Log Out </ResponsiveNavLink>
                    </div>
                </div>
            </div>
        </nav>

        <header class="bg-white shadow dark:bg-gray-800" v-if="$slots.header">
            <div class="mx-auto max-w-screen-2xl px-4 py-6 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <main>
            <div class="mx-auto max-w-screen-2xl">
                <slot />
            </div>
        </main>
        <GlobalAlert />
    </div>
</template>