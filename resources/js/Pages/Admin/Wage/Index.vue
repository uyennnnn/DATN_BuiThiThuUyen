<script setup>

const props = defineProps({
    attendanceData: Array,
});

console.log(props.attendanceData);
</script>

<template>
    <Head title="Contact" />

    <AdminAuthenticatedLayout>
        <div>
            <PageTitle title="QR CODE"/>

           

        </div>

    </AdminAuthenticatedLayout>
</template>
