<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/shared/InputError.vue';
import InputLabel from '@/Components/shared/InputLabel.vue';
import PrimaryButton from '@/Components/shared/PrimaryButton.vue';
import TextInput from '@/Components/shared/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import PageTitle from '@/Components/shared/PageTitle.vue';
import SecondaryButton from '@/Components/shared/SecondaryButton.vue';
import EyeIcon from '@/Components/icons/EyeIcon.vue';
import EyeSlashIcon from '@/Components/icons/EyeSlashIcon.vue';

const showPassword = ref(false);

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'));
};

const tooglePassword = () => {
    showPassword.value = !showPassword.value
}

</script>

<template>
    <GuestLayout>
        <Head title="Reset Password" />

        <PageTitle title="パスワード再設定"/>

        <InputError
            class="text-center text-[13px] mb-3"
            v-if="form.errors.email || form.errors.password"
            :message="form.errors.email || form.errors.password"
        />

        <form @submit.prevent="submit">
            <div class="mt-4">
                <TextInput
                    id="password"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    placeholder="新しいパスワード"
                    :error="form.errors.password"
                />
            </div>

            <div class="mt-4 relative">
                <TextInput
                    id="password_confirmation"
                    :type="showPassword ? 'text' : 'password'"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    placeholder="新しいパスワード（確認）"
                    :error="form.errors.password"
                />

                <div @click="tooglePassword" class="absolute right-2 bottom-[10px] cursor-pointer">
                    <EyeSlashIcon v-if="showPassword" />
                    <EyeIcon v-else />
                </div>
            </div>

            <div class="flex items-center justify-center mt-7">
                <SecondaryButton type="submit" class="text-center py-4 !px-16 mt-2" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    パスワード再設定
                </SecondaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
