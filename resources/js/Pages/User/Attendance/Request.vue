<script setup>
import UserAuthenticatedLayout from "@/Layouts/UserAuthenticatedLayout.vue";
import { MonthPickerInput } from 'vue-month-picker'
import SecondaryButton from "@/Components/shared/SecondaryButton.vue";
import { Head, Link } from "@inertiajs/vue3";


// const inputValue = ref('');

const props = defineProps({
  shopCreatedAt: String,
  positions: Object,
  employee: Array
});

const yearShopCreatedAt = new Date(props.shopCreatedAt).getFullYear();
const monthShopCreatedAt = new Date(props.shopCreatedAt).getMonth();

const changeMonth = (month) => {
  inputValue.value = month.year + '-' + month.monthIndex
  dayAttendance.value = new Date(month.year, month.monthIndex - 1, 1);
}

</script>

<template>

    <Head title="Chấm công" />

    <UserAuthenticatedLayout>
        <div class="flex justify-end">
            <Link :href="route('users.create')">
                <SecondaryButton class="max-w-fit flex text-center py-4 mt-3 gap-2">
                    <svg fill="#ffffff" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
                        width="15px" height="15px" viewBox="0 0 45.402 45.402"
                        xml:space="preserve">
                        <g>
                        <path d="M41.267,18.557H26.832V4.134C26.832,1.851,24.99,0,22.707,0c-2.283,0-4.124,1.851-4.124,4.135v14.432H4.141
                            c-2.283,0-4.139,1.851-4.138,4.135c-0.001,1.141,0.46,2.187,1.207,2.934c0.748,0.749,1.78,1.222,2.92,1.222h14.453V41.27
                            c0,1.142,0.453,2.176,1.201,2.922c0.748,0.748,1.777,1.211,2.919,1.211c2.282,0,4.129-1.851,4.129-4.133V26.857h14.435
                            c2.283,0,4.134-1.867,4.133-4.15C45.399,20.425,43.548,18.557,41.267,18.557z"/>
                        </g>
                    </svg>
                Báo cáo
                </SecondaryButton>
            </Link>
        </div>
        <div class="flex py-5 w-full items-center gap-2">
        <span>Danh sách báo cáo</span> 

        <month-picker-input class="!w-[100%] max-w-[400px] text-[0.875rem]" lang="vi"
            :min-date="new Date(yearShopCreatedAt, monthShopCreatedAt)" :max-date="new Date()"
            @change="changeMonth"></month-picker-input>
        </div>

        <div class="flex justify-between">
        <div class="filter active">
            Tất cả
        </div>

        <div class="filter">
            Chờ duyệt
        </div>

        <div class="filter">
            Đã duyệt
        </div>

        <div class="filter">
            Bị hủy
        </div>
        </div>

        <table class="attendance-table">
        <thead>
            <tr>
            <th>Ngày</th>
            <th>Số giờ</th>
            <th>Loại</th>
            <th>Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            <tr>
            <td>T2, 08/07</td>
            <td>3 giờ 0 phút</td>
            <td>Làm thêm</td>
            <td>Chờ duyệt</td>
            </tr>
            <tr>
            <td>T2, 09/07</td>
            <td>4 giờ 2 phút</td>
            <td>Làm đêm</td>
            <td>Đã duyệt</td>
            </tr>
            <tr>
            <td>T2, 09/07</td>
            <td>4 giờ 2 phút</td>
            <td>Làm đêm</td>
            <td>Bị hủy</td>
            </tr>
        </tbody>
        </table>
    </UserAuthenticatedLayout>

</template>

<style scoped>
.filter {
    background-color: #e0e0e0;
    padding: 8px 10px;
    border-radius: 20px;
}

.active {
    background-color: #286fee;
    color: white;
}

.attendance-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.attendance-table th, .attendance-table td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.attendance-table th {
    background-color: #f2f2f2;
}
</style>
