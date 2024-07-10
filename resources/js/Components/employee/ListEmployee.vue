<script setup>
import EmployeeItem from './EmployeeItem.vue'

defineProps({
    users: {
        type: Array,
        default: []
    },
    positions: {
        type: Object,
    }
})
</script>

<template>
    <div class="text-center font-semibold">Danh sách nhân viên</div>
    <EmployeeItem
        v-for="user in users"
        :key="`user-${user.id}`"
        :data="user"
        :positions="positions"
    />
</template>

