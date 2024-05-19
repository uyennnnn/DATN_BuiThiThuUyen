<script setup>
import PageTitle from '@/Components/shared/PageTitle.vue';
import SecondaryButton from '@/Components/shared/SecondaryButton.vue';
import AdminAuthenticatedLayout from '@/Layouts/AdminAuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

</script>

<template>
    <Head title="Update shop success" />

    <AdminAuthenticatedLayout>
        <div class="mt-[11vh] md:mt-[15vh]">
            <PageTitle title="店舗管理"/>

            <div class="text-center text-sm mt-10 text-gray-600">
                店舗情報を更新しました。
            </div>

            <Link :href="route('shop.edit')" class="flex items-center justify-center mt-7">
                <SecondaryButton class="text-center py-4 !px-14 mt-2">
                    店舗管理
                </SecondaryButton>
            </Link>
        </div>

    </AdminAuthenticatedLayout>
</template>
