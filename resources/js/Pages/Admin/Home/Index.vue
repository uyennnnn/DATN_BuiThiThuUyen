<script setup>
import AdminAuthenticatedLayout from '@/Layouts/AdminAuthenticatedLayout.vue';
import MenuItem from '@/Components/adminHome/MenuItem.vue';
import StoreIcon from '@/Components/icons/StoreIcon.vue';
import FaceIdIcon from '@/Components/icons/FaceIdIcon.vue';
import CurrencyIcon from '@/Components/icons/CurrencyIcon.vue';
import CalendarIcon from '@/Components/icons/CalendarIcon.vue';
import LogoutIcon from '@/Components/icons/LogoutIcon.vue';
import CommentIcon from '@/Components/icons/CommentIcon.vue';
import QRCodeIcon from '@/Components/icons/QRCodeIcon.vue';
import { Head, Link } from '@inertiajs/vue3';

const listMenuItems = [
    {
        name: '店舗管理',
        icon: StoreIcon,
        routeName: 'shop.edit',
        disable: false
    },
    {
        name: '従業員管理',
        icon: FaceIdIcon,
        routeName: 'users.index',
        disable: false
    },
    {
        name: '給与計算',
        icon: CurrencyIcon,
        routeName: '',
        disable: true
    },
    {
        name: '勤怠データ',
        icon: CalendarIcon,
        routeName: 'attendance',
        disable: false
    },
    {
        name: 'QRコード',
        icon: QRCodeIcon,
        routeName: 'qrCode',
        disable: false
    },
    {
        name: 'サポート',
        icon: CommentIcon,
        routeName: 'contact',
        disable: false
    },
    {
        name: 'ログアウト',
        icon: LogoutIcon,
        routeName: 'get_logout',
        disable: false,
        // routeMethod: 'get'
    },
]
</script>

<template>
    <Head title="Admin Home" />

    <AdminAuthenticatedLayout>
        <div class="text-gray-900 mt-[8vh] md:mt-[12vh] xl:mt-[16vh]">
            <div class="grid grid-cols-3 md:grid-cols-4 xl:grid-cols-6 gap-4 md:gap-8 xl:gap-16">
                <MenuItem v-for="item in listMenuItems" :key="item.name" :data="item"/>
            </div>
        </div>
    </AdminAuthenticatedLayout>
</template>
