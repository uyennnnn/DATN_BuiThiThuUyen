<script setup>
import ApplicationLogo from '@/Components/shared/ApplicationLogo.vue';
import Header from '@/Layouts/Header.vue';
</script>

<template>
    <div class="bg-[#f8f8f8] min-h-screen">
        <div class="flex justify-center py-4">
            <ApplicationLogo />
        </div>

        <div class="flex justify-center items-center mt-[16vh] md:mt-[20vh]">
            <div class="w-full sm:max-w-md mt-2 px-10 py-4 overflow-hidden sm:rounded-lg">
                <slot />
            </div>
        </div>
    </div>



</template>
