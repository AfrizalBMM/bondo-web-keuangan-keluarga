<script setup>
import { ref, onMounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import GlobalAlert from '@/Components/GlobalAlert.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { useTheme } from '@/Composables/useTheme';
import { useVisibility } from '@/Composables/useVisibility';
import { 
    Moon, Sun, Eye, EyeOff, Bell,
    LayoutDashboard, Wallet, ArrowRightLeft, 
    Tags, Briefcase, Target, CreditCard, 
    PieChart, BarChart3
} from 'lucide-vue-next';

const showingNavigationDropdown = ref(false);

const { isDark, initTheme, toggleTheme } = useTheme();
const { isVisible, toggleVisibility } = useVisibility();
const page = usePage();

import { onUnmounted } from 'vue';

onMounted(() => {
    initTheme();

    // Listen to real-time events for family updates
    if (page.props.auth && page.props.auth.user && page.props.auth.user.family_id) {
        window.Echo.private(`family.${page.props.auth.user.family_id}`)
            .listen('FamilyDataUpdated', (e) => {
                console.log('Real-time sync triggered: Family data updated', e);
                router.reload({
                    preserveState: true,
                    preserveScroll: true,
                });
            });
    }
});

onUnmounted(() => {
    if (page.props.auth && page.props.auth.user && page.props.auth.user.family_id) {
        window.Echo.leave(`family.${page.props.auth.user.family_id}`);
    }
});
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <nav
                class="border-b border-gray-100 bg-white dark:border-gray-700 dark:bg-gray-800"
            >
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-6 sm:-my-px sm:ms-10 sm:flex text-sm">
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                    <LayoutDashboard class="w-4 h-4 mr-2" /> Command Center
                                </NavLink>
                                <NavLink :href="route('wallets')" :active="route().current('wallets')">
                                    <Wallet class="w-4 h-4 mr-2" /> Dompet
                                </NavLink>
                                <NavLink :href="route('transactions')" :active="route().current('transactions')">
                                    <ArrowRightLeft class="w-4 h-4 mr-2" /> Transaksi
                                </NavLink>
                                <NavLink :href="route('categories')" :active="route().current('categories')">
                                    <Tags class="w-4 h-4 mr-2" /> Kategori
                                </NavLink>
                                <NavLink :href="route('assets')" :active="route().current('assets')">
                                    <Briefcase class="w-4 h-4 mr-2" /> Aset
                                </NavLink>
                                <NavLink :href="route('goals')" :active="route().current('goals')">
                                    <Target class="w-4 h-4 mr-2" /> Goals
                                </NavLink>
                                <NavLink :href="route('debts')" :active="route().current('debts')">
                                    <CreditCard class="w-4 h-4 mr-2" /> Hutang/Piutang
                                </NavLink>
                                <NavLink :href="route('budget')" :active="route().current('budget')">
                                    <PieChart class="w-4 h-4 mr-2" /> Budget
                                </NavLink>
                                <NavLink :href="route('reports')" :active="route().current('reports')">
                                    <BarChart3 class="w-4 h-4 mr-2" /> Laporan
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Theme Toggle -->
                            <button @click="toggleTheme" class="p-2 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300 transition-colors">
                                <Sun v-if="isDark" class="w-5 h-5" />
                                <Moon v-else class="w-5 h-5" />
                            </button>
                            
                            <!-- Notification Bell -->
                            <Link :href="route('notifications')" class="relative p-2 ml-1 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300 transition-colors">
                                <Bell class="w-5 h-5" />
                                <span class="absolute top-1 right-1 h-2 w-2 rounded-full bg-rose-500 border-2 border-white dark:border-gray-800"></span>
                            </Link>
                            
                            <!-- Visibility Toggle -->
                            <button @click="toggleVisibility" class="p-2 ml-1 mr-3 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300 transition-colors">
                                <Eye v-if="isVisible" class="w-5 h-5" />
                                <EyeOff v-else class="w-5 h-5" />
                            </button>

                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none dark:bg-gray-800 dark:text-gray-400 dark:hover:text-gray-300"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none dark:text-gray-500 dark:hover:bg-gray-900 dark:hover:text-gray-400 dark:focus:bg-gray-900 dark:focus:text-gray-400"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            <span class="flex items-center"><LayoutDashboard class="w-5 h-5 mr-3" /> Command Center</span>
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('wallets')" :active="route().current('wallets')">
                            <span class="flex items-center"><Wallet class="w-5 h-5 mr-3" /> Dompet</span>
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('transactions')" :active="route().current('transactions')">
                            <span class="flex items-center"><ArrowRightLeft class="w-5 h-5 mr-3" /> Transaksi</span>
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('categories')" :active="route().current('categories')">
                            <span class="flex items-center"><Tags class="w-5 h-5 mr-3" /> Kategori</span>
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('assets')" :active="route().current('assets')">
                            <span class="flex items-center"><Briefcase class="w-5 h-5 mr-3" /> Aset</span>
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('goals')" :active="route().current('goals')">
                            <span class="flex items-center"><Target class="w-5 h-5 mr-3" /> Goals</span>
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('debts')" :active="route().current('debts')">
                            <span class="flex items-center"><CreditCard class="w-5 h-5 mr-3" /> Hutang/Piutang</span>
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('budget')" :active="route().current('budget')">
                            <span class="flex items-center"><PieChart class="w-5 h-5 mr-3" /> Budget</span>
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('reports')" :active="route().current('reports')">
                            <span class="flex items-center"><BarChart3 class="w-5 h-5 mr-3" /> Laporan</span>
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div
                        class="border-t border-gray-200 pb-1 pt-4 dark:border-gray-600"
                    >
                        <div class="px-4">
                            <div
                                class="text-base font-medium text-gray-800 dark:text-gray-200"
                            >
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="flex flex-col px-4 mt-4 space-y-3">
                            <!-- Responsive Theme Toggle -->
                            <button @click="toggleTheme" class="flex items-center text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300 transition-colors">
                                <Sun v-if="isDark" class="w-5 h-5 mr-3" />
                                <Moon v-else class="w-5 h-5 mr-3" />
                                <span class="text-sm font-medium">{{ isDark ? 'Light Mode' : 'Dark Mode' }}</span>
                            </button>
                            
                            <!-- Responsive Visibility Toggle -->
                            <button @click="toggleVisibility" class="flex items-center text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300 transition-colors">
                                <Eye v-if="isVisible" class="w-5 h-5 mr-3" />
                                <EyeOff v-else class="w-5 h-5 mr-3" />
                                <span class="text-sm font-medium">{{ isVisible ? 'Hide Balances' : 'Show Balances' }}</span>
                            </button>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header
                class="bg-white shadow dark:bg-gray-800"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
            
            <GlobalAlert />
        </div>
    </div>
</template>
