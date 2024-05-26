<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import PrimaryButton from "@/Components/shared/PrimaryButton.vue";
import LightButton from "@/Components/shared/LightButton.vue";
const back = () => {
    window.history.back();
};
</script>

<template>
    <GuestLayout>
        <Head title="Log out" />

        <div class="text-center mt-7 mb-3 font-bold">
            <h4 class="text-[#286fee]">Bạn có chắc chắn muốn đăng xuất không ?</h4>
        </div>

        <div class="flex mt-4 py-5 gap-3 justify-center pb-8">
                <LightButton @click="back" class="text-center whitespace-nowrap w-1/2 py-4">
                    Hủy
                </LightButton>

            <Link :href="route('logout')" method="post" class="w-1/2" as="button">
                <PrimaryButton
                    type="submit"
                    class="text-center whitespace-nowrap !w-full py-4"
                    >
                    Đăng xuất        
                </PrimaryButton>
            </Link>
      </div>
    </GuestLayout>
</template>
