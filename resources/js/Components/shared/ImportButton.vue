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
        class="px-4 py-2 border-2 border-[#E1E3EA] rounded-md font-semibold text-xs text-black uppercase tracking-widest shadow-sm hover:bg-gray-400"
    >
        <slot />
    </button>
</template>
