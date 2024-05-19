<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: {
        type: String,
        required: false,
    },
    error: {
        type: String,
        required: null
    },
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded shadow-sm text-sm"
        :class="{ 'placeholder-red-500 !border-red-500 focus:!border-red-500 focus:!ring-red-500 !text-red-500' : error }"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        ref="input"
    />
</template>
