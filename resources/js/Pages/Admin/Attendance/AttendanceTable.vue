<template>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <tbody class="bg-white divide-y divide-gray-200">
                <tr>
                    <td colspan="6" class="!text-left px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-[#cfcfd0]">
                        {{ selectedDate }}
                    </td>
                </tr>
                <tr v-for="i in countRow" :key="i" :style="`background-color: ${  i == 7 ? '#cfcfd0' : i%2 != 0 ? '#e6e6e6' : '#fff'}`">
                    <template v-for="j in countColumn" :key="j">
                        <td v-if="i==1 && j>=2" class="px-6 py-4 whitespace-nowrap text-sm text-white"
                        :style="`background-color: ${positionArr[attendanceData[j-1][i-1] - 1].color ?? '#b3b3b3'}`"
                        colspan="4">{{ positionArr[attendanceData[j-1][i-1] - 1].name }}</td>

                        <template v-else-if="i > 7 & j > 1" >
                            <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900" :class="{ 'text-red-500': attendanceData[j-1][i-1][4] == 1 }" >{{ attendanceData[j-1][i-1][0] }}</td>
                            <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900" :class="{ 'text-red-500': attendanceData[j-1][i-1][5] == 1 }"> {{ attendanceData[j-1][i-1][1] }} </td>
                            <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ (attendanceData[j-1][i-1][0]) ? attendanceData[j-1][i-1][2] : null }}
                            </td>
                            <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ (attendanceData[j-1][i-1][0]) ? attendanceData[j-1][i-1][3] : null }}
                            </td>
                        </template>

                        <td v-else class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" colspan="4"
                        :style="`background-color: ${i == 5 ? '#e0ffea' : i == 6 ? '#ddf1ff' : i == 7 ? '#cfcfd0' : i > 6 && i %2  != 0 || i == 2 ? '#e6e6e6' : '#fff' }`"
                        >{{ attendanceData[j-1][i-1] }}</td>
                        
                    </template>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    name: "AttendanceTable",
    props: {
        attendanceData: {
            type: Array,
            required: true,
        },
        selectedDate: {
            type: String
        },
        positions: {
            type: Object
        }
    },
    data() {
        return {
            countRow: 0,
            countColumn: 0,
            positionArr: []
        }
    },
    created () {
        this.countRow = this.attendanceData[0].length;
        this.countColumn = this.attendanceData.length;

        Object.values(this.positions).forEach(element => {
            this.positionArr.push({
                name: element.NAME,
                color: element.TAG_COLOR
            })
        });
    },
    watch: {
        attendanceData () {
            this.countRow = this.attendanceData[0].length;
            this.countColumn = this.attendanceData.length;
        }
    },
    methods: {
        getPosition (positionId) {
            return Object.values(this.positions).find((item) => item.ID == positionId);
        }
    }
};
</script>

<style scoped>
td {
    border: 1px solid #ccc;
    text-align: center;
}
</style>
