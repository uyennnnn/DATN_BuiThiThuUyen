<script setup>

defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
<button
    :type="type"
    class="px-4 py-2 bg-[#286fee] rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-blue-500"
>
    <slot />
</button>

</template>
