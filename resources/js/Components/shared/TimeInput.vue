<script setup>
import { onMounted, ref, watch } from 'vue';

const selectedHour = ref('');
const selectedMinute = ref('');
const hours = Array.from({ length: 30 }, (_, i) => String(i + 1).padStart(2, '0'))
const minutes = Array.from({ length: 60 }, (_, i) => String(i).padStart(2, '0'))

const props = defineProps({
    modelValue: {
        type: String,
        required: true,
    },
    error: {
        type: String,
        required: null
    },
});

const emit = defineEmits(['update:modelValue']);

watch([selectedHour, selectedMinute], () => {
    emit('update:modelValue', `${selectedHour.value}:${selectedMinute.value}`);
})

onMounted(() => {
  console.log(props.modelValue);
})

</script>

<template>
<div class="flex items-center gap-1">
    <select
        class="mt-1 block w-[55px] border border-gray-300 rounded focus:border-blue-500 text-gray-700 text-[13px] px-2 py-1"
        v-model="selectedHour">
        <option v-for="hour in hours" :key="hour" :value="hour">{{ hour }}</option>
    </select>
    <span>:</span>
    <select
        class="mt-1 block w-[55px] border border-gray-300 rounded focus:border-blue-500 text-gray-700 text-[13px] px-2 py-1"
        v-model="selectedMinute">
        <option v-for="minute in minutes" :key="minute" :value="minute">{{ minute }}</option>
    </select>
</div>
</template>
