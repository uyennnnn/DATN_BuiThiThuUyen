<script setup>
const props = defineProps({
    title: {
        type: String,
        default: null
    },
});

</script>

<template>
    <div class="text-center text-lg font-semibold">{{ title }}</div>
    <div class="flex justify-center items-center gap-1 mt-3 mb-5">
        <div v-for="i in 6" :key="i">
            <div
                class="w-[5px] h-[5px] rounded-full"
                :class="(i % 3 === 0) ? 'bg-blue-600' : 'bg-black'"
            />
        </div>
    </div>

</template>
