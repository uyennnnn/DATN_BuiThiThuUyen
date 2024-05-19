<script setup>
import PageTitle from "@/Components/shared/PageTitle.vue";
import SecondaryButton from "@/Components/shared/SecondaryButton.vue";
import AdminAuthenticatedLayout from "@/Layouts/AdminAuthenticatedLayout.vue";
import ListEmployee from "@/Components/employee/ListEmployee.vue";
import { Head, Link } from "@inertiajs/vue3";
import { onMounted, ref, watch } from "vue";
import axios from "axios";

const props = defineProps({
  totalUser: {
    type: Number,
    default: 0,
  },
  positions: {
    type: Object,
    default: {},
  },
  positionType: {
    default: 'all'
  }
});

const users = ref([]);
const positionType = ref(props.positionType);

watch(positionType, (newValue, oldValue) => {
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('position', newValue);
    const newUrl = currentUrl.toString();
    history.replaceState(null, null, newUrl);

    getListUser(newValue);
})

onMounted(() => {
    getListUser(props.positionType);
})

const getListUser = async (type) => {
    const { data }  = await axios.get(`/employee/get-list?position=${type}`)
    users.value = data.data
}
</script>

<template>
  <Head title="Employee" />

  <AdminAuthenticatedLayout>
    <PageTitle title="従業員管理" />

    <Link :href="route('users.create')">
      <SecondaryButton class="!w-full text-center py-4 mt-3">
        従業員新規登録
      </SecondaryButton>
    </Link>

    <div class="flex items-center mt-8 text-sm gap-1">
      <div class="text-base text-[#286fee]">•</div>
      <div>現在の従業員</div>
    </div>

    <div
      class="bg-[#e8e8e8] mt-2 text-[12px] flex items-center justify-center py-[10px]"
    >
      総従業員数
      <div class="text-base text-[#286fee] ml-2">{{ totalUser }}</div>
      名
    </div>

    <div class="flex gap-3 items-center text-sm mt-4">
      <div class="whitespace-nowrap">役職で絞り込み</div>

      <select
        v-model="positionType"
        class="bg-gray-50 border border-gray-300 text-sm rounded-[2px] focus:ring-blue-500 focus:border-blue-500 block w-full px-2 py-3"
      >
        <option value="all" :selected ="positionType == 'all'">役職を選択してください</option>
        <option
          v-for="option in positions"
          :key="option.NAME"
          :value="option.ID"
          :selected="positionType == option.ID"
        >
          {{ option.NAME }}
        </option>
      </select>
    </div>

    <div class="py-8">
      <ListEmployee :users="users" :positions="positions" />
    </div>
  </AdminAuthenticatedLayout>
</template>
