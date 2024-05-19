<script setup>
import UserAuthenticatedLayout from "@/Layouts/UserAuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/shared/PrimaryButton.vue";
import LightButton from "@/Components/shared/LightButton.vue";
import AttendanceItem from "@/Components/employee/AttendanceItem.vue";
</script>

<template>
  <Head title="User Checkin" />

  <UserAuthenticatedLayout>
    <div class="flex-col">
        <div class="text-center mt-10 mb-3 font-bold">
            <h4 class="text-[#286fee]">打刻に失敗しました</h4>
        </div>

        <div class="flex mt-4 py-5 gap-3 justify-center pb-8">
        <Link :href="route('user.attendance')" class="w-1/2">
           <PrimaryButton
            type="submit"
            class="text-center whitespace-nowrap !w-full py-4"
            >
            勤怠画面
            </PrimaryButton>
        </Link>
        </div>
    </div>
  </UserAuthenticatedLayout>
</template>
