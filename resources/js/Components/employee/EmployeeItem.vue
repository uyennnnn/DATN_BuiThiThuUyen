<script setup>
import SecondaryButton from '@/Components/shared/SecondaryButton.vue';
import DangerButton from '@/Components/shared/DangerButton.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    data: {
        type:Object
    },
    positions: {
        type: Object,
    }
})

const getPosition = (positionId) => {
    return Object.values(props.positions).find((item) => item.ID == positionId);
}

const goToDetail = () => {
    router.get(`/employee/${props.data.id}`);
}

const deleteEmployee = (user_id) => {
    router.get(`/delete-employee?id=${user_id}`);
}

</script>

<template>
<div class="flex items-center gap-1 md:gap-2 xl:gap-4 mt-3.5 h-[42px]">
  <div class="flex-1 border border-[#e8e8e8] flex items-center h-full px-2 gap-2">
    <div
      class="text-white text-xs w-[80px] text-center py-[3px] rounded-full"
      :style="`background-color: ${getPosition(data.position)?.TAG_COLOR || '#b3b3b3'}`"
    >
      {{ getPosition(data.position)?.NAME || 'Khác' }}
    </div>

    <div class="max-w-[70%]">
      <div v-if="data?.full_name" class="font-semibold">
        <div class="text-[13px] whitespace-nowrap text-ellipsis overflow-hidden text-[#356ff5] w-full">{{ data?.full_name }}</div>
        <div class="text-[11.5px] whitespace-nowrap text-ellipsis overflow-hidden w-full">{{ data.name }}</div>
      </div>
      <div v-else class="text-[13px] whitespace-nowrap text-ellipsis overflow-hidden w-full font-semibold">{{ data.name }}</div>
    </div>
  </div>
  <SecondaryButton class="flex-none w-auto text-center h-full whitespace-nowrap" @click="goToDetail">
    SỬA
  </SecondaryButton>

  <DangerButton class="flex-none w-auto text-center h-full whitespace-nowrap" @click="deleteEmployee(data?.id)">
    XÓA
  </DangerButton>
</div>

</template>
