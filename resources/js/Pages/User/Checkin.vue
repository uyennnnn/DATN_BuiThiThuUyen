<script setup>
import UserAuthenticatedLayout from "@/Layouts/UserAuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import SecondaryButton from "@/Components/shared/SecondaryButton.vue";
import LightButton from "@/Components/shared/LightButton.vue";
import AttendanceItem from "@/Components/employee/AttendanceItem.vue";
import { defineProps } from 'vue';

const props = defineProps({
  time: String,
});

</script>

<template>
  <Head title="User Checkin" />

  <UserAuthenticatedLayout>
    <div class="text-center mt-7 mb-3 font-bold">
      <h4 class="text-[#286fee]">出勤打刻</h4>
    </div>

    <AttendanceItem :label="'勤務先'">CyBAR planet 新宿歌舞伎町店</AttendanceItem>
    <AttendanceItem :label="'Tên nhân viên'" :textColor="'#286fee'">星熊みなみ</AttendanceItem>
    <AttendanceItem :label="'勤務開始'" :textColor="'#286fee'">
        <div>
        2024年3月1日
      </div>
      <div>
        17:01
      </div>
      <div>{{ time }}</div>
    </AttendanceItem>


    <div class="flex mt-4 py-5 gap-3 justify-center pb-8">
        <Link :href="route('user.attendance')" class="w-1/2">
        <LightButton class="text-center whitespace-nowrap !w-full py-4">
        キャンセル
        </LightButton>
    </Link>

      <Link :href="route('user.attendance')" class="w-1/2">
        <SecondaryButton
          type="submit"
          class="text-center whitespace-nowrap !w-full py-4"
        >
          出勤する
        </SecondaryButton>
      </Link>


    </div>
  </UserAuthenticatedLayout>
</template>
