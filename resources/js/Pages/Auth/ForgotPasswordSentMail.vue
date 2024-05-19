<script setup>
import PageTitle from '@/Components/shared/PageTitle.vue';
import SecondaryButton from '@/Components/shared/SecondaryButton.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    isAdmin: {
        type: Boolean,
        default: true
    },
});
</script>

<template>
    <GuestLayout>
        <Head title="Sent mail" />

        <PageTitle title="パスワードリセット"/>

        <div class="text-center text-sm mt-10 text-gray-600">
            ご入力いただいたメールアドレスに<br>
            パスワードリセットメールを送信しました
        </div>

        <Link :href="route(isAdmin ? 'login' : 'user.login')" class="flex items-center justify-center mt-7">
            <SecondaryButton class="text-center py-4 !px-14 mt-2">
                ログイン画面
            </SecondaryButton>
        </Link>
    </GuestLayout>
</template>
