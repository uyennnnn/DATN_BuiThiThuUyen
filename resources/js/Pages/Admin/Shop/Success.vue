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
            <PageTitle title="CÀI ĐẶT DOANH NGHIỆP"/>

            <div class="text-center text-sm mt-10 text-gray-600">
                Đã cập nhật thông tin doanh nghiệp
            </div>

            <Link :href="route('shop.edit')" class="flex items-center justify-center mt-7">
                <SecondaryButton class="text-center py-4 !px-14 mt-2">
                    Cài đặt doanh nghiệp
                </SecondaryButton>
            </Link>
        </div>

    </AdminAuthenticatedLayout>
</template>
