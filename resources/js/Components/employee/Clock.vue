<script setup>

const props = defineProps(['clockHours', 'clockMinutes', 'clockSeconds', 'clockDate'])

</script>

<template>
    <div class="flex gap-2 items-center justify-center text-[11px] text-gray-600">
        <div>現在の時刻</div>
        <div>{{ clockDate }}</div>
    </div>
    <div class="flex gap-2 items-center justify-center font-semibold font">
        <div class="text-[75px] text-[#286fee] px-1">{{ clockHours }}</div>
        <div class="text-[35px] text-[#000000] px-1"> : </div>
        <div class="text-[75px] text-[#286fee] px-1">{{ clockMinutes }}</div>
        <div class="text-[35px] text-[#000000] px-1"> : </div>
        <div class="text-[75px] text-[#286fee]">{{ clockSeconds }}</div>
    </div>
    <slot></slot>
</template>

<style>
.font div{
    font-family: "Questrial", sans-serif;
    font-weight: 600;
    font-style: normal;
}
</style>