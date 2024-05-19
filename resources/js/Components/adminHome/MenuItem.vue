<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    data: {
        type: Object,
    },
});
</script>

<template>
    <Link
        :href="data?.routeName ? route(data.routeName) : ''"
        :method="data?.routeMethod ? data.routeMethod : 'get'"
        class="cursor-pointer"
        :class="{'!cursor-not-allowed' : data.disable}"
    >
        <div
            class="bg-white aspect-square menu-item rounded-[3px] flex justify-center items-center"
            :class="{'!bg-[#f8f8f8]' : data.disable}"
            >
            <component :is="data.icon" />
        </div>
        <div
            class="text-center text-sm md:text-base mt-3 font-semibold mb-3"
            :class="{'!text-[#d4d4d4]' : data.disable}"
        >
            {{ data.name }}
        </div>
    </Link>

</template>

<style scoped>
.menu-item {
    box-shadow: rgba(0, 0, 0, 0.1) 0px 1px 3px 0px, rgba(0, 0, 0, 0.06) 0px 1px 2px 0px;
}
</style>
