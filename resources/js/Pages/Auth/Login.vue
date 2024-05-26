<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import TextInput from '@/Components/shared/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PageTitle from '@/Components/shared/PageTitle.vue';
import SecondaryButton from '@/Components/shared/SecondaryButton.vue';
import { ref } from 'vue';
import EyeIcon from '@/Components/icons/EyeIcon.vue';
import EyeSlashIcon from '@/Components/icons/EyeSlashIcon.vue';

const showPassword = ref(false);

const props = defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    isAdminLogin: {
        type: Boolean,
        default: true
    },
    userType: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    const routeName = props.isAdminLogin ? 'login' : 'user.login';

    form.post(route(routeName));
};

const tooglePassword = () => {
    showPassword.value = !showPassword.value
}

</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <PageTitle :title="isAdminLogin ? 'ĐĂNG NHẬP QUẢN TRỊ VIÊN' : 'ĐĂNG NHẬP'"/>

        <div>
            <div v-if="form.errors.email || form.errors.password" class="text-[13px] text-red-600 text-center mb-3">
                <span>Email hoặc số điện thoại không đúng</span>
            </div>

            <form @submit.prevent="submit">
                <div>
                    <TextInput
                        class="mt-1 block w-full"
                        name="email"
                        v-model="form.email"
                        placeholder="Email"
                        :error="form.errors.email"
                        autocomplete="new-email"
                        autofocus
                    />
                </div>

                <div class="mt-4 relative">
                    <TextInput
                        :type="showPassword ? 'text' : 'password'"
                        class="mt-1 block w-full"
                        name="password"
                        v-model="form.password"
                        :error="form.errors.email"
                        placeholder="Mật khẩu"
                        autocomplete="new-password"
                    />

                    <div @click="tooglePassword" class="absolute right-2 bottom-[10px] cursor-pointer">
                        <EyeSlashIcon v-if="showPassword" />
                        <EyeIcon v-else />
                    </div>
                </div>

                <div class="flex items-center justify-center mt-6">
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-xs text-[#286fee]"
                    >
                        Quên mật khẩu
                    </Link>
                </div>
                <div class="mt-7 flex justify-center">
                    <SecondaryButton type="submit" class="text-center py-4 !px-16 mt-2" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                       Đăng nhập
                    </SecondaryButton>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
