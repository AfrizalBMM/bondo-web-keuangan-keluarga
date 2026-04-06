<template>
    <svg viewBox="0 0 128 128" fill="none" xmlns="http://www.w3.org/2000/svg">
        <!-- Main Circular Container with Gradient Feel -->
        <rect x="16" y="16" width="96" height="96" rx="24" fill="url(#bondo_grad)" />
        
        <!-- Stylized "B" for Bondo -->
        <path d="M48 40H66C72.6274 40 78 45.3726 78 52C78 58.6274 72.6274 64 66 64H48V40Z" fill="white" fill-opacity="0.9" />
        <path d="M48 64H70C76.6274 64 82 69.3726 82 76C82 82.6274 76.6274 88 70 88H48V64Z" fill="white" />
        
        <!-- Coin/Money Detail -->
        <circle cx="64" cy="64" r="40" stroke="white" stroke-width="4" stroke-opacity="0.1" />
        <circle cx="64" cy="64" r="32" stroke="white" stroke-width="1" stroke-opacity="0.2" />

        <defs>
            <linearGradient id="bondo_grad" x1="16" y1="16" x2="112" y2="112" gradientUnits="userSpaceOnUse">
                <stop stop-color="#2563eb" />
                <stop offset="1" stop-color="#1d4ed8" />
            </linearGradient>
        </defs>
    </svg>
</template>
