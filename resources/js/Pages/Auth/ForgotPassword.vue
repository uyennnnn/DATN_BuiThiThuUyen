<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import TextInput from '@/Components/shared/TextInput.vue';
import PageTitle from '@/Components/shared/PageTitle.vue';
import SecondaryButton from '@/Components/shared/SecondaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
    userType: String,
});


const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password" />

        <PageTitle title="パスワードリセット"/>

        <div v-if="form.errors.email" class="text-[12px] text-red-600 text-center mb-3">
            メールアドレス
            <span class="text-gray-800">を入力してください</span>
        </div>

        <form @submit.prevent="submit">
            <div>
                <TextInput
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    autofocus
                    placeholder="メールアドレス"
                    :error="form.errors.email"
                />
            </div>

            <div class="flex items-center justify-center mt-6">
                <Link
                    :href="route(userType === 'admin' ? 'login' : 'user.login')"
                    class="text-xs text-[#286fee]"
                >
                    ログイン画面に戻る
                </Link>
            </div>

            <div class="flex items-center justify-center mt-7">
                <SecondaryButton type="submit" class="text-center py-4 !px-14 mt-2" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    パスワード再発行
                </SecondaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
