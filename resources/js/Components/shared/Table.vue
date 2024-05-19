<template>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <tbody class="bg-white divide-y divide-gray-200">
            <tr>
                <td
                colspan="5"
                class="!text-left px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                >
                2024年3月勤怠
                </td>
            </tr>
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">役職</td>
                <td
                colspan="4"
                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                >
                {{ items[0]?.position }}
                </td>
            </tr>
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">本名</td>
                <td
                colspan="4"
                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                >
                {{ items[0]?.name }}
                </td>
            </tr>
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                スタッフ名
                </td>
                <td
                colspan="4"
                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                >
                {{ items[0]?.full_name }}
                </td>
            </tr>
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                出勤日数
                </td>
                <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ items[0]?.totalDay }}
                </td>
            </tr>
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                合計休憩時間
                </td>
                <td
                colspan="4"
                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                >
                {{ items[0]?.totalBreakHour }}
                </td>
            </tr>
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                合計勤務時間
                </td>
                <td
                colspan="4"
                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                >
                {{ items[0]?.totalWorkHour }}
                </td>
            </tr>

            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">日付</td>
                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">出勤</td>
                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">退勤</td>
                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">休憩</td>
                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">
                勤務時間
                </td>
            </tr>

            <template v-for="item in items">
                <tr v-for="daily in item.dailyData" :key="daily.date">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ daily.date }}
                </td>
                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ daily.checkin }}
                </td>
                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ daily.checkout }}
                </td>
                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ daily.break }}
                </td>
                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ daily.totalHour }}
                </td>
                </tr>
            </template>
            </tbody>
        </table>
  </div>
</template>

  <script>
export default {
  name: "Table",
  props: {
    items: {
      type: Array,
      required: true,
    },
  },
};
</script>

<style scoped>
    td {
    border: 1px solid #ccc;
    text-align: center;
    /* display: flex;
    justify-content: center;
    align-items: center;
    height: 50px;   */
    }
</style>
