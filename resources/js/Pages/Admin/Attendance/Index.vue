<script setup>
import PageTitle from "@/Components/shared/PageTitle.vue";
import SecondaryButton from "@/Components/shared/SecondaryButton.vue";
import PrimaryButton from '@/Components/shared/PrimaryButton.vue';
import AdminAuthenticatedLayout from "@/Layouts/AdminAuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, computed, watch } from 'vue';
import AttendanceTable from './AttendanceTable.vue';
import Spinner from '@/Components/shared/Spinner.vue';
import LightButton from "@/Components/shared/LightButton.vue";
import AttendanceItem from "@/Components/employee/AttendanceItem.vue";
import { MonthPickerInput } from 'vue-month-picker'
import DropdownIcon from '@/Components/icons/DropdownIcon.vue';
import DropdownBlackIcon from '@/Components/icons/DropdownBlackIcon.vue';
import TextInput from "@/Components/shared/TextInput.vue";
import { format, getDaysInMonth, getDay, addDays } from 'date-fns';
import { ja } from 'date-fns/locale';
import * as ExcelJS from "exceljs";
import { saveAs } from "file-saver";

const props = defineProps({
  shopCreatedAt: String,
  positions: Object,
  employee: Array
});

const yearShopCreatedAt = new Date(props.shopCreatedAt).getFullYear();
const monthShopCreatedAt = new Date(props.shopCreatedAt).getMonth();
const inputValue = ref('');
const showTable = ref(false);
const tableData = ref([]);
const csvData = ref([]);
const selectedDate = ref('');
const currentPage = ref('select-month');
const textLoading = ref(null);
const dayAttendance = ref(new Date());
const showEditAttendance = ref(false);
const layout = ref('main-page');

const form = useForm({
  id: "",
  name: "",
  date_time: "",
  checkIn: "",
  checkOut: "",
})

const getAttendanceDetail = async (type = null) => {
  // currentPage.value = 'loader';
  const currentLayout = layout.value;
  layout.value = 'loader';
  textLoading.value = type == 'exportCsv' ? 'データ生成中' : 'データ読み込み中';
  const response = await fetch('/api/attendance/report?date=' + inputValue.value);
  layout.value = currentLayout;
  if (type) {
    currentPage.value = type == 'exportCsv' ? 'ready-export' : 'select-month';
  }
  const data = await response.json();
  tableData.value = data;
  showTable.value = true;
  selectedDate.value = inputValue.value;
  let date = inputValue.value;
  // selectedDate.value = date.slice(0, 4) + '年' + date.slice(5, 7) + '月';
};

const fetchCsvData = async () => {
  await getAttendanceDetail('exportCsv');
  // await exportToCSV();
}

const exportToCSV = () => {
  const csvRows = [];
  const csvData = tableData.value;
  // Tiêu đề cho mỗi cột
  const headers = Object.keys(csvData[0]);
  csvRows.push(headers.join(','));

  // Dữ liệu
  for (const row of csvData) {
    const values = headers.map(header => {
      const escaped = ('' + row[header]).replace(/"/g, '\\"');
      return `"${escaped}"`;
    });
    csvRows.push(values.join(','));
  }

  const csvString = csvRows.join('\n');
  const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.setAttribute('download', 'data.csv');
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

const exportToExcel = async () => {
  const workbook = new ExcelJS.Workbook();
  const worksheet = workbook.addWorksheet('Sheet1');

  const data = tableData.value;
  const countRow = data[0].length;
  const countColumn = data.length;

  for (let i = 0; i < countRow; i++) {
    for (let j = 0; j < countColumn; j++) {
      const value = data[j][i];
      if (j === 0) {
        worksheet.getCell(i + 1, j + 1).value = value;
      } else if (i < 6) {
        let cell = worksheet.getCell(i + 1, j * 4 - 2);
        cell.value = value;
        worksheet.mergeCells(i + 1, j * 4 - 2, i + 1, j * 4 + 1);
      } else {
        worksheet.getCell(i + 1, j * 4 - 2).value = value[0];
        worksheet.getCell(i + 1, j * 4 - 1).value = value[1];
        worksheet.getCell(i + 1, j * 4).value = value[2];
        worksheet.getCell(i + 1, j * 4 + 1).value = value[3];

        if (value[4] === 1) { 
          worksheet.getCell(i + 1, j * 4 - 2).font = { color: { argb: 'FFFF0000' } };
        }
        if (value[5] === 1) {
          worksheet.getCell(i + 1, j * 4 - 1).font = { color: { argb: 'FFFF0000' } };
        }
      }
    }
  }

  const buffer = await workbook.xlsx.writeBuffer();
  const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
  saveAs(blob, 'report.xlsx');
};



const changeMonth = (month) => {
  inputValue.value = month.year + '-' + month.monthIndex
  dayAttendance.value = new Date(month.year, month.monthIndex - 1, 1);
}

const displayEditAttendance = () => {
  showEditAttendance.value = !showEditAttendance.value
}

const dateOptions = computed(() => {
  const dayNamesJapanese = ['日', '月', '火', '水', '木', '金', '土'];
  const startOfMonth = new Date(dayAttendance.value.getFullYear(), dayAttendance.value.getMonth(), 1);
  const currentDate = new Date();
  const isCurrentMonth = currentDate.getFullYear() === dayAttendance.value.getFullYear() && currentDate.getMonth() === dayAttendance.value.getMonth();
  const daysInMonth = isCurrentMonth ? currentDate.getDate()-1 : getDaysInMonth(dayAttendance.value);
  let options = [];
  for (let i = 0; i < daysInMonth; i++) {
    const currentDate = addDays(startOfMonth, i);
    const dayOfWeek = currentDate.getDay();
    options.push({
      date: format(currentDate, 'yyyy-MM-dd'),
      label: format(currentDate, 'M月d日', { locale: ja }) + `（${dayNamesJapanese[dayOfWeek]}）`
    });
  }

  return options;
});

const getAttendancesByUserIdAndDay = async () => {
  if (form.id && form.date_time) {
    try {
      const response = await axios.get(`/user/${form.id}/day/${form.date_time}/attendances`);
      form.checkIn = response.data.checkIn;
      form.checkOut = response.data.checkOut;
    } catch (error) {
      console.error("Error", error);
    }
  }
};

const updateAttendancesByUserIdAndDay = async () => {
  if (form.id && form.date_time) {
    try {
      const response = await axios.put(`/user/${form.id}/day/${form.date_time}/attendances`, {
        'checkIn': form.checkIn,
        'checkOut': form.checkOut,
      });
      if (response.data) {
        layout.value = 'success-page';
        getAttendanceDetail();
      } else {
        layout.value = 'error-page';
      }
    } catch (error) {
      console.error("Error", error);
    }
  }
};

const changeLayout = (value) => {
  layout.value = value;
}

watch(
  [
    () => form.id,
    () => form.date_time,
  ],
  (
  ) => {
    getAttendancesByUserIdAndDay()
  }
);

</script>

<template>

  <Head title="Attendance" />

  <AdminAuthenticatedLayout>
    <template v-if="currentPage === 'select-month'">
      <div v-show="layout == 'main-page'">
        <PageTitle title="勤怠データ" />

        <div class="flex items-center text-sm gap-1 mt-8 mb-3">
          <div class="text-base text-[#286fee]">•</div>
          <div>集計月</div>
        </div>


        <month-picker-input class="!w-[100%] max-w-[400px] text-[0.875rem]" lang="ja"
          :min-date="new Date(yearShopCreatedAt, monthShopCreatedAt)" :max-date="new Date()"
          @change="changeMonth"></month-picker-input>

        <template v-if="inputValue">
          <div class="flex space-x-4 my-4">
            <PrimaryButton class="flex-1 text-center py-4" @click="getAttendanceDetail('showData')">
              勤怠データ表示
            </PrimaryButton>

            <SecondaryButton class="w-1/2 text-center h-full whitespace-nowrap py-4" @click="fetchCsvData">
              CSV ダウンロード
            </SecondaryButton>
          </div>

          <div v-if="showTable" class="my-9">
            <div class="flex items-center justify-start mt-7 mb-2 font-bold cursor-default"
              @click="displayEditAttendance">
              <h4 class="text-[#286fee]">勤怠データ修正</h4>
              <div v-if="showEditAttendance">
                <DropdownIcon />
              </div>
              <div v-else>
                <DropdownBlackIcon />
              </div>
            </div>

            <div v-if="showEditAttendance">
              <div class="bg-[#e5e5e5] flex border text-sm mt-4">
                <div class="w-1/4 flex items-center justify-center">従業員名</div>
                <div class="w-3/4 bg-[#f8f8f8]">
                  <div class="flex border-[#dddddd] items-center gap-1 sm:gap-3 py-5 px-3 justify-end">
                    <select class="border-[#dddddd] w-2/3 max-w-[240px]" v-model="form.id">
                      <option v-for="(item, index) in employee" :key="item.id" :value="item.id">
                        {{ item.name + '／' + item.full_name }}
                      </option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="bg-[#e5e5e5] flex border text-sm mt-5">
                <div class="w-1/4 flex items-center justify-center">
                  変更日時
                </div>
                <div class="w-3/4 bg-[#f8f8f8]">
                  <div class="flex items-center justify-end py-5 px-3 border-t gap-2">
                    <div class="w-1/3 text-end">
                      日にち
                    </div>
                    <select class="border-[#dddddd] w-2/3 max-w-[240px]" v-model="form.date_time">
                      <option v-for="option in dateOptions" :key="option.label" :value="option.date">
                        {{ option.label }}
                      </option>
                    </select>
                  </div>

                  <div class="flex items-center justify-end py-5 px-3 border-t gap-2">
                    <div class="w-1/3 text-end">
                      出勤時間
                    </div>
                    <TextInput type="time" class="w-2/3 max-w-[240px]" v-model="form.checkIn" />
                  </div>

                  <div class="flex items-center justify-end py-5 px-3 border-t gap-2">
                    <div class="w-1/3 text-end">
                      退勤時間
                    </div>
                    <TextInput type="time" class="w-2/3 max-w-[240px]" v-model="form.checkOut" />
                  </div>
                </div>
              </div>

              <div class="mt-7 flex items-center justify-center text-sm pb-8">
                <SecondaryButton type="submit" class="text-center h-full whitespace-nowrap w-1/2 py-4"
                  @click="updateAttendancesByUserIdAndDay()">
                  変更を保存
                </SecondaryButton>
              </div>
            </div>
          </div>

          <AttendanceTable class="mb-5" v-if="showTable" :attendance-data="tableData" :selected-date="selectedDate"
            :positions="positions">
          </AttendanceTable>
        </template>
      </div>

      <div class="mt-[11vh] md:mt-[15vh]" v-show="layout == 'success-page'">
        <PageTitle title="勤怠データ" />

        <div class="text-center text-sm mt-10 text-gray-600">
          勤怠データを更新しました。
        </div>

        <div class="flex items-center justify-center mt-7">
          <SecondaryButton class="text-center py-4 !px-14 mt-2" @click="changeLayout('main-page')">
            勤怠データ
          </SecondaryButton>
        </div>
      </div>

      <div class="mt-[11vh] md:mt-[15vh]" v-show="layout == 'error-page'">
            <PageTitle title="エラー"/>

            <div class="text-center text-sm mt-10 text-gray-600">
                問題が発生しました <br />
                再度はじめからお試しください
            </div>

            <div class="flex items-center justify-center mt-7">
                <SecondaryButton class="text-center py-4 !px-14 mt-2" @click="changeLayout('main-page')">
                    戻る
                </SecondaryButton>
              </div>
        </div>

      <div v-show="layout == 'loader' " class="mt-[11vh] md:mt-[15vh] flex flex-col items-center">
        <Spinner class="mb-10" :text="textLoading"></Spinner>
        <LightButton class="text-center h-full py-4 w-1/4" @click="layout = 'main-page'">
          キャンセル
        </LightButton>
      </div>
    </template>

    <div v-if="currentPage == 'loader' && layout == 'main-page' " class="mt-[11vh] md:mt-[15vh] flex flex-col items-center">
      <Spinner class="mb-10" :text="textLoading"></Spinner>
      <LightButton class="text-center h-full py-4 w-1/4" @click="currentPage = 'select-month'">
        キャンセル
      </LightButton>
    </div>

    <template v-if="currentPage === 'ready-export'">
      <PageTitle title="勤怠データCSVダウンロード" />

      <AttendanceItem label="勤怠月" text-color="#286fee">{{ selectedDate }}</AttendanceItem>

      <div class="flex space-x-4 mt-5">
        <LightButton class="text-center h-full py-4 w-1/2" @click="currentPage = 'select-month'">
          キャンセル
        </LightButton>

        <SecondaryButton class="text-center h-full whitespace-nowrap py-4 w-1/2" @click="exportToExcel">
          CSV ダウンロード
        </SecondaryButton>
      </div>
    </template>
  </AdminAuthenticatedLayout>
</template>
