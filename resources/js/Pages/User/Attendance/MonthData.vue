<template>
    <div>
        <div v-for="(dayData, index) in filteredDayEntries" :key="index" class="flex pt-5">
            <div class="block w-1/5 relative day-container text-sm">
                <div class="day">
                    <div class="text-slate-500">{{ dayData.dayName }}</div>
                    <div class="font-bold text-xl">{{ dayData.dayNumber }}</div>
                    <div class="text-3xl text-[#286fee] dot">•</div>
                </div>
            </div>
            <div class="w-4/5 detailData text-sm">
                <div class="flex justify-between border-b-2">
                    <div>Thời gian chấm công</div>
                    <div class="font-bold">
                        <span v-if="dayData.checkInTime">
                            {{ dayData.checkInTime }}
                        </span>
                        <span v-else class="text-red-500">
                            ✖
                        </span>
                        
                        <span> - </span>  

                        <span v-if="dayData.checkOutTime">
                            {{ dayData.checkOutTime }}
                        </span>
                        <span v-else class="text-red-500">
                            ✖
                        </span>
                    </div>
                </div>
                <div class="flex justify-between border-b-2">
                    <div>Thời gian làm</div>
                    <div class="font-bold">{{ dayData.workingTime }}</div>
                </div>
                <div class="flex justify-between">
                    <div>Thời gian nghỉ</div>
                    <div class="font-bold">{{ dayData.breakTime }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { computed } from 'vue';
import moment from 'moment';

export default {
    name: "MonthData",
    props: {
        data: {
            type: Array,
            required: true,
        },
    },
    computed: {
        dayEntries() {
            const dayNames = this.data[0].slice(7); // Lấy tên các ngày trong tháng từ phần tử đầu tiên của mảng data
            const userData = this.data[1].slice(7); // Lấy dữ liệu của các ngày trong tháng từ phần tử thứ hai của mảng data

            return dayNames.map((dayName, index) => {
                const dayNumberMatch = dayName.match(/(\d+)/); // Lấy số ngày từ chuỗi bằng regex
                const dayNumber = dayNumberMatch ? parseInt(dayNumberMatch[0]) : ''; // Nếu tìm thấy số ngày thì chuyển nó thành số nguyên, nếu không thì để trống
                const dayData = userData[index];

                return {
                    dayName: dayName.split(',')[0], // Lấy tên ngày (e.g., "Thứ hai")
                    dayNumber: dayNumber,
                    checkInTime: dayData[0] || '',
                    checkOutTime: dayData[1] || '',
                    breakTime: dayData[2] || '0 giờ 0 phút',
                    workingTime: dayData[3] || '0 giờ 0 phút'
                };
            });
        },
        filteredDayEntries() {
            const yesterday = moment().subtract(1, 'days').date(); // Lấy ngày hôm qua
            // const yesterday = moment().date(); // Lấy ngày hôm qua
            return this.dayEntries
                .filter(entry => entry.dayNumber <= yesterday) // Lọc các ngày từ đầu tháng đến hôm qua
                .sort((a, b) => b.dayNumber - a.dayNumber); // Sắp xếp theo thứ tự giảm dần về ngày
        }
    }
};
</script>

<style scoped>
.day {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 5px;
    position: relative;
}
.dot {
    position: relative;
    z-index: 1;
}
.day-container::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translateX(-50%);
    width: 2px;
    height: 62%;
    background-color: #286fee;
    z-index: 0;
}
.detailData {
    padding: 5px;
    background-color: white;
    border-radius: 20px;
}
.detailData div {
    padding: 5px;
}
.text-red-500 {
    color: #ef4444; /* Màu đỏ */
}
</style>
