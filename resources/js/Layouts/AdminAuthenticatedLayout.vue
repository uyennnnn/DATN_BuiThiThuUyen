<script setup>
import Header from './Header.vue';

</script>

<template>
    <div>
        <div class="min-h-screen bg-[#f8f8f8]">
            <Header />
            <main class="mt-[5vh] px-9 md:px-14 xl:px-[12%] text-gray-900">
                <slot />
            </main>
        </div>
    </div>
</template>
