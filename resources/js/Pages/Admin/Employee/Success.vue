<script setup>
import PageTitle from '@/Components/shared/PageTitle.vue';
import SecondaryButton from '@/Components/shared/SecondaryButton.vue';
import PrimaryButton from '@/Components/shared/PrimaryButton.vue';
import AdminAuthenticatedLayout from '@/Layouts/AdminAuthenticatedLayout.vue';
import ListEmployee from '@/Components/employee/ListEmployee.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    type: {
        type: String,
        default: 'create'
    },
});
</script>

<template>
    <Head
        :title="type === 'create' ? 'Register Success' : type === 'update' ? 'Update Success' : 'Delete Success'"
    />

    <AdminAuthenticatedLayout>
        <div class="mt-[11vh] md:mt-[15vh]">
            <PageTitle v-if="type !== 'delete'" :title="type === 'create' ? 'Đăng ký nhân viên' : 'Sửa thông tin nhân viên'"/>

            <div
                v-if="type !== 'delete'"
                class="text-center text-sm mt-10 text-gray-600 mb-7"
            >
                {{ type === 'create' ? 'Đăng ký thành công' : 'Sửa thành công' }}
            </div>

            <div
                v-else
                class="text-center text-lg font-bold mt-10 text-blue-600 mb-5"
            >
                <!-- 削除しました -->
                Đã xóa
            </div>

            <Link :href="route('users.index')" class="flex items-center justify-center">
                <PrimaryButton
                    v-if="type === 'delete'"
                    type="submit"
                    class="text-center py-4 !px-12 mt-2"
                >
                    <!-- 従業員管理画面 -->
                    Quản lý nhân viên
                </PrimaryButton>
                <SecondaryButton
                    v-else
                    class="text-center py-4 !px-14 mt-2"
                >
                    <!-- 従業員一覧 -->
                    QUản lý nhân viên
                </SecondaryButton>
            </Link>
        </div>

    </AdminAuthenticatedLayout>
</template>
