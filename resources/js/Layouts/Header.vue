<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/shared/ApplicationLogo.vue';
import MenuIcon from '@/Components/icons/MenuIcon.vue';
import { Link } from '@inertiajs/vue3';

const drawerOpen = ref(false);

const toggleDrawer = () => {
    drawerOpen.value = !drawerOpen.value;
};

const listMenu = [
    {
        name: 'Trang chủ',
        routeName: 'home',
        outSystem: false,
    },
    {
        name: 'Quản lý cửa hàng',
        routeName: 'shop.edit',
        outSystem: false,
    },
    {
        name: 'Quản lý nhân viên',
        routeName: 'users.index',
        outSystem: false,
    },
    {
        name: 'Dữ liệu chấm công',
        routeName: 'attendance',
    },
    {
        name: 'QR Code',
        routeName: 'qrCode',
    },
    {
        name: 'Liên hệ',
        routeName: 'contact',
        outSystem: true,
    },
    {
        name: 'Điều khoản sử dụng',
        directLink: 'https://www.notion.so/nazori/607740c95cdd467a9d80008a89cf7c05?pvs=4',
        outSystem: true,
    },
    {
        name: 'Chính sách bảo mật',
        directLink: 'https://www.notion.so/nazori/5281a635bbd54c1996a4d0f79384350c?pvs=4',
        outSystem: true,
    },
    {
        name: 'Thông báo theo luật kinh doanh',
        directLink: 'https://www.notion.so/nazori/e8ef0dc76f984ac78c1525c0a6964f8d?pvs=4',
        outSystem: true,
    },
    {
        name: 'Đăng xuất',
        routeName: 'get_logout',
    },
]
</script>

<template>
    <div class="flex justify-between py-4 px-5 items-center">
        <button @click="toggleDrawer">
            <MenuIcon />
        </button>
        <ApplicationLogo />
        <div></div>
    </div>

    <div :class="{ 'translate-x-0': drawerOpen, '-translate-x-full': !drawerOpen }"
        class="transition-transform duration-300 ease-in-out fixed top-0 left-0 w-full md:max-w-[280px] h-screen overflow-hidden bg-[#161616e8] text-white z-40">
        <div>
            <button class="m-4" @click="toggleDrawer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-6 h-6 text-[#286fee]">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </button>
            <div v-for="item in listMenu" :key="item.name" class="border-t border-t-gray-700 w-full px-5 py-5 text-sm">

                <template v-if="item.directLink">
                    <a :href="item.directLink" target="_blank" class="text-white flex gap-3">
                        {{ item.name }}
                        <div v-if="item.outSystem" class="relative h-[12px] mt-[3px]">
                            <div class="border border-[#356ff5] h-[7px] w-[10px] mt-1"></div>
                            <div
                                class="border border-[#356ff5] h-[7px] w-[10px] absolute top-[1px] left-[3px] z-10 bg-[#161616c5]">
                            </div>
                        </div>
                    </a>
                </template>
                <template v-else>
                    <Link :href="item.routeName ? route(item.routeName) : ''" as="button" class="text-white flex gap-3">
                    {{ item.name }}
                    <div v-if="item.outSystem" class="relative h-[12px] mt-[3px]">
                        <div class="border border-[#356ff5] h-[7px] w-[10px] mt-1"></div>
                        <div
                            class="border border-[#356ff5] h-[7px] w-[10px] absolute top-[1px] left-[3px] z-10 bg-[#161616c5]">
                        </div>
                    </div>
                    </Link>
                </template>
            </div>
        </div>
    </div>
</template>
