<script setup>
    import UserAuthenticatedLayout from "@/Layouts/UserAuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
</script>

<template>
    <Head title="Link Expired" />
    <UserAuthenticatedLayout>
        <div class="bg-[#f8f8f8] flex justify-center items-center text-[13px] md:text-base min-h-[calc(100vh-300px)] whitespace-nowrap">
            リンクが無効です。再度NFCを読み取ってください。
        </div>
    </UserAuthenticatedLayout>
</template>
