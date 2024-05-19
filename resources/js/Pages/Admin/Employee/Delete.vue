<script setup>
import AdminAuthenticatedLayout from "@/Layouts/AdminAuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { ref } from "vue";
import DangerButton from "@/Components/shared/DangerButton.vue";
import LightButton from "@/Components/shared/LightButton.vue";

defineProps({
  employee: {
    type: Object,
    default: {},
  },
  storeName: {
    type: String,
    default: "CyBAR planet 新宿歌舞伎町店",
  },
});

const back = () => {
  window.history.back();
};
</script>

<template>
  <AdminAuthenticatedLayout>
    <Head title="Delete employee" />

    <div class="text-center mb-3 font-bold">
      <h4 class="text-[#286fee]">従業員情報削除</h4>
    </div>

    <div>
      <div class="grid grid-cols-4 text-sm">
        <div class="col-span-1 text-center border border-gray-200 py-2.5">
          勤務先
        </div>
        <div
          class="col-span-3 text-center border border-gray-200 bg-white py-2.5 font-semibold"
        >
          {{ storeName }}
        </div>
      </div>

      <div class="grid grid-cols-4 text-sm">
        <div class="col-span-1 text-center border border-gray-200 py-2.5">
          従業員名
        </div>
        <div
          class="col-span-3 border border-gray-200 bg-white py-2 font-semibold flex justify-center items-center gap-2 text-blue-600"
        >
          <div>{{ employee?.name }}</div>
        </div>
      </div>
    </div>
    <div class="flex mt-4 py-5 gap-3 justify-center pb-8">
      <LightButton
        @click="back"
        class="text-center whitespace-nowrap w-1/2 py-4"
      >
        キャンセル
      </LightButton>

      <Link
        :href="route('users.destroy', { employee: employee?.id })"
        method="post"
        class="w-1/2"
        as="button"
      >
        <DangerButton
          type="submit"
          class="text-center whitespace-nowrap !w-full py-4"
        >
          削除
        </DangerButton>
      </Link>
    </div>
  </AdminAuthenticatedLayout>
</template>
