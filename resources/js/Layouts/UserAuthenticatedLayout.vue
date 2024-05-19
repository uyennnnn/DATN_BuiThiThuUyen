<script setup>
import ApplicationLogo from '@/Components/shared/ApplicationLogo.vue';
import Header from './Header.vue';

</script>

<template>
    <div>
        <div class="min-h-screen bg-[#f8f8f8]">
            <div class="flex justify-center py-4">
                <ApplicationLogo />
            </div>

            <main class="mt-[5vh] px-3 md:px-14 xl:px-[12%] text-gray-900">
                <slot />
            </main>
        </div>
    </div>
</template>
