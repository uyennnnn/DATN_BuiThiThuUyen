<script setup>
import PageTitle from '@/Components/shared/PageTitle.vue';
import SecondaryButton from '@/Components/shared/SecondaryButton.vue';
import AdminAuthenticatedLayout from '@/Layouts/AdminAuthenticatedLayout.vue';
import ListEmployee from '@/Components/employee/ListEmployee.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Contact" />

    <AdminAuthenticatedLayout>
        <div class="mt-[11vh] md:mt-[15vh]">
            <PageTitle title="お問い合わせ・サポート"/>

            <div class="text-center text-sm mt-10 text-gray-600">
                お問い合わせ・サポート<br>
                SPISPI勤怠サポートLINE@までご連絡ください
            </div>

            <a href="https://lin.ee/zHMVL6u" target="_blank" class="flex items-center justify-center mt-7">
                <SecondaryButton class="text-center py-4 !px-14 mt-2">
                    LINE@
                </SecondaryButton>
            </a>
        </div>

    </AdminAuthenticatedLayout>
</template>
