<script setup>
import UserAuthenticatedLayout from "@/Layouts/UserAuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import WarningButton from "@/Components/shared/WarningButton.vue";
import LightButton from "@/Components/shared/LightButton.vue";
import AttendanceItem from "@/Components/employee/AttendanceItem.vue";
</script>

<template>
  <Head title="User Checkin" />

  <UserAuthenticatedLayout>
    <div class="text-center mt-7 mb-3 font-bold">
      <h4 class="text-[#286fee]">休憩打刻</h4>
    </div>

    <AttendanceItem :label="'勤務先'">CyBAR planet 新宿歌舞伎町店</AttendanceItem>
    <AttendanceItem :label="'従業員名'" :textColor="'#286fee'">星熊みなみ</AttendanceItem>
    <AttendanceItem :label="'休憩開始'" :textColor="'#286fee'">
        <div>
        2024年3月1日
      </div>
      <div>
        17:01
      </div>
    </AttendanceItem>

    <div class="flex mt-4 py-5 gap-3 justify-center pb-8">
        <Link :href="route('user.attendance')" class="w-1/2">
        <LightButton class="text-center whitespace-nowrap !w-full py-4">
        キャンセル
        </LightButton>
    </Link>

      <Link :href="route('user.attendance')" class="w-1/2">
        <WarningButton
          type="submit"
          class="text-center whitespace-nowrap !w-full py-4"
        >
          休憩開始
        </WarningButton>
      </Link>


    </div>
  </UserAuthenticatedLayout>
</template>
