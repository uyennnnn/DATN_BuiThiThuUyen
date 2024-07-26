<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import { Head, Link, usePage, useForm } from "@inertiajs/vue3";
import {} from "@inertiajs/vue3";

import UserAuthenticatedLayout from "@/Layouts/UserAuthenticatedLayout.vue";
import PageTitle from "@/Components/shared/PageTitle.vue";
import SecondaryButton from "@/Components/shared/SecondaryButton.vue";
import LightButton from "@/Components/shared/LightButton.vue";
import PrimaryButton from "@/Components/shared/PrimaryButton.vue";
import Clock from "@/Components/employee/Clock.vue";
import AttendanceItem from "@/Components/employee/AttendanceItem.vue";
import WarningButton from "@/Components/shared/WarningButton.vue";
import AttendanceDetail from "./AttendanceDetail.vue";
import DropdownIcon from "@/Components/icons/DropdownIcon.vue";
import DropdownBlackIcon from "@/Components/icons/DropdownBlackIcon.vue";
import MonthData from "./MonthData.vue";

const NOT_CHECKED_IN = 0;
const WORKING = 1;
const ON_BREAK = 2;
const CHECKED_OUT = 3;

const props = defineProps({
  mode: Number,
  dayWorking: Number,
  salary: Number,
  workingTime: Object,
  shopName: String,
  todayTotalBreakTime: Object,
  todayBreakTimes: Array,
  checkInTime: Object,
  tempBreakTime: Number,
  totalWorkingHoursDay: Object,
});
console.log(props.totalWorkingHoursDay)

let intervalId = ref("");
let currentTimestamp = ref("");
let pageName = ref("primary");
const mode = ref(props.mode);

let clockHours = ref("--");
let clockMinutes = ref("--");
let clockSeconds = ref("--");
let clockDate = ref("--");

let checkinHours = ref("--");
let checkinMinutes = ref("--");

let date_time = ref();
const monthStatus = ref(false);
const dayStatus = ref(false);
const breakStatus = ref(false);
const endBreak = ref(); // biến rỗng

const monthData = ref([]);
const isLoading = ref(true);

const displayMonthStatus = () => {
  monthStatus.value = !monthStatus.value;
};

const displayDayStatus = () => {
  dayStatus.value = !dayStatus.value;
};

const displayBreakStatus = () => {
  breakStatus.value = !breakStatus.value;
};

const fetchCurrentTime = async () => {
  const response = await fetch("/api/current-time");
  const data = await response.json();
  currentTimestamp.value = data.timestamp * 1000;
};

const changePage = (value = "primary") => {
  pageName.value = value;
};

const changeMode = (value) => {
  mode.value = value;
};

const isActiveButton = (buttonName) => {
  switch (buttonName) {
    case "CHECK_IN":
      return mode.value == NOT_CHECKED_IN;
    case "CHECK_OUT":
      return mode.value == WORKING;
    case "START_BREAK":
      return mode.value == WORKING;
    case "END_BREAK":
      return mode.value == ON_BREAK;
    default:
      break;
  }
};

const beforeChangePage = (nextPage) => {
  date_time.value = currentTimestamp.value;
  checkinHours.value = clockHours.value;
  checkinMinutes.value = clockMinutes.value;
  endBreak.value = Math.floor(Date.now() / 1000);
  changePage(nextPage);
  console.log("change Page: ", date_time.value);
};

const performAction = async (actionType) => {
  const data = useForm({ date_time: date_time.value, action_type: actionType });
  console.log(data);
  try {
    await data.post("/user/attendance", data);

    switch (actionType) {
      case "checkIn":
        changeMode(WORKING);
        break;
      case "checkOut":
        changeMode(CHECKED_OUT);
        break;
      case "startBreak":
        changeMode(ON_BREAK);
        break;
      case "endBreak":
        changeMode(WORKING);
        break;
      default:
        break;
    }

    changePage();
  } catch (error) {
    alert("Error");
  }
};

const updateTime = () => {
  const date = new Date(currentTimestamp.value);

  clockDate.value = date.toLocaleDateString("vi-VN", {
    timeZone: "Asia/Ho_Chi_Minh",
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
  });

  const timeString = date.toLocaleTimeString("vi-VN", {
    timeZone: "Asia/Ho_Chi_Minh",
    hour: "2-digit",
    minute: "2-digit",
    second: "2-digit",
    hour12: false,
  });

  const [hours, minutes, seconds] = timeString.split(":");
  clockHours.value = hours;
  clockMinutes.value = minutes;
  clockSeconds.value = seconds;
};

const page = usePage();
const user = computed(() => page.props.auth.user);

const handleVisibilityChange = () => {
  if (document.visibilityState === "visible") {
    fetchCurrentTime();
  }
};

onMounted(() => {
  document.addEventListener("visibilitychange", handleVisibilityChange);

  if (document.visibilityState === "visible") {
    fetchCurrentTime();
  }
  intervalId = setInterval(() => {
    if (currentTimestamp) {
      currentTimestamp.value = currentTimestamp.value + 1000;
      updateTime();
    }
  }, 1000);

  fetchAttendanceData();
});

onUnmounted(() => {
  clearInterval(intervalId);
});

const timeAttendance = computed(() => {
  return (
    clockDate.value + " " + checkinHours.value + ":" + checkinMinutes.value
  );
});

const totalBreak = computed(() => {
  const breakStart = new Date(props.tempBreakTime);
  const diff = endBreak.value - breakStart;
  const minutes = Math.floor(diff / 60);
  const hours = Math.floor(minutes / 60);
  const remainingMinutes = minutes % 60;
  return `${hours} giờ ${remainingMinutes} phút`;
});

const fetchAttendanceData = async () => {
  try {
    const response = await axios.get("/user/attendance/request?date=2024-7");
    monthData.value = response.data;
    console.log(response.data);
    isLoading.value = false;
  } catch (error) {
    console.error("Error fetching attendance data:", error);
    isLoading.value = false;
  }
};

const listPages = [
  {
    type: "checkIn",
    title: "Bắt đầu làm việc",
  },
  {
    type: "checkOut",
    title: "Kết thúc làm việc",
  },
  {
    type: "startBreak",
    title: "Bắt đầu nghỉ giải lao",
  },
  {
    type: "endBreak",
    title: "Kết thúc nghỉ giải lao",
  },
];
</script>

<template>
  <Head title="Chấm công" />

  <UserAuthenticatedLayout>
    <div v-show="pageName == 'primary'">
      <!-- <PageTitle title="CHẤM CÔNG" /> -->

      <div class="border-solid border-2 border-indigo-600 p-5 rounded-lg">
        <Clock
          :clockHours="clockHours"
          :clockMinutes="clockMinutes"
          :clockSeconds="clockSeconds"
          :clockDate="clockDate"
        >
        </Clock>

        <div class="mt-5">
          <AttendanceItem :label="'Nơi làm việc'">{{
            shopName
          }}</AttendanceItem>
          <AttendanceItem :label="'Tên nhân viên'" :textColor="'#286fee'">{{
            user.name
          }}</AttendanceItem>
        </div>

        <div class="flex flex-col lg:flex-row mt-4 py-5 gap-3 justify-center">
          <SecondaryButton
            @click="beforeChangePage('checkIn')"
            class="text-center h-full whitespace-nowrap w-full py-6"
            v-if="isActiveButton('CHECK_IN')"
          >
            Bắt đầu làm việc
          </SecondaryButton>

          <!-- <LightButton class="text-center h-full whitespace-nowrap w-1/2 py-6" v-else>
        Bắt đầu làm việc
    </LightButton> -->

          <WarningButton
            @click="beforeChangePage('startBreak')"
            class="text-center h-full whitespace-nowrap w-full lg:w-1/2 py-6"
            v-if="isActiveButton('START_BREAK')"
          >
            Bắt đầu nghỉ giải lao
          </WarningButton>

          <PrimaryButton
            @click="beforeChangePage('checkOut')"
            class="text-center h-full whitespace-nowrap w-full lg:w-1/2 py-6"
            v-if="isActiveButton('CHECK_OUT')"
          >
            Kết thúc làm việc
          </PrimaryButton>

          <PrimaryButton
            @click="beforeChangePage('endBreak')"
            class="text-center h-full whitespace-nowrap w-full py-6"
            v-else-if="isActiveButton('END_BREAK')"
          >
            Kết thúc nghỉ giải lao
          </PrimaryButton>
          <!-- 
    <LightButton class="text-center h-full whitespace-nowrap w-1/2 py-6" v-else>
        Kết thúc làm việc
    </LightButton> -->
        </div>
      </div>

      <div class="mb-9">
        <div
          class="flex items-center justify-center mt-7 mb-2 font-bold cursor-default"
          @click="displayDayStatus"
        >
          <h4 class="text-[#286fee]">Tình trạng làm việc trong ngày</h4>
          <div v-if="dayStatus">
            <DropdownIcon />
          </div>
          <div v-else>
            <DropdownBlackIcon />
          </div>
        </div>

        <div v-show="dayStatus">
          <div class="grid grid-cols-3 text-sm">
            <div
              class="col-span-1 flex items-center justify-center border border-gray-200 py-2.5"
            >
              Thời gian bắt đầu làm việc
            </div>

            <div
              v-if="mode == NOT_CHECKED_IN"
              class="col-span-2 border border-gray-200 bg-white py-2 flex justify-center items-center gap-1"
            >
              <div class="text-lg font-semibold">0</div>
              <div class="text-[10px] mt-2 text-gray-600">phút</div>
            </div>

            <div
              v-else
              class="col-span-2 border border-gray-200 bg-white py-2 flex justify-center items-center gap-1"
            >
              <div class="text-lg font-semibold">
                {{ checkInTime.hours }}:{{ checkInTime.minutes }}
              </div>
            </div>
          </div>

          <div class="grid grid-cols-3 text-sm">
        <div class="col-span-1 flex items-center justify-center border border-gray-200 py-2.5">
            Thời gian làm việc dự kiến
        </div>

        <div
            v-if="totalWorkingHoursDay.hours > 0"
            class="col-span-2 border border-gray-200 bg-white py-2 flex justify-center items-center gap-1"
        >
            <div class="text-lg font-semibold">
                {{ totalWorkingHoursDay.hours }}
            </div>
            <div class="text-[10px] mt-2 text-gray-600">giờ</div>
            <div class="text-lg font-semibold">
                {{ totalWorkingHoursDay.minutes }}
            </div>
            <div class="text-[10px] mt-2 text-gray-600">phút</div>
        </div>

        <div
            v-else-if="totalWorkingHoursDay.minutes > 0"
            class="col-span-2 border border-gray-200 bg-white py-2 flex justify-center items-center gap-1"
        >
            <div class="text-lg font-semibold">
                {{ totalWorkingHoursDay.minutes }}
            </div>
            <div class="text-[10px] mt-2 text-gray-600">phút</div>
        </div>

        <div
            v-else
            class="col-span-2 border border-gray-200 bg-white py-2 flex justify-center items-center gap-1"
        >
            <div class="text-lg font-semibold">0</div>
            <div class="text-[10px] mt-2 text-gray-600">phút</div>
        </div>
    </div>
          
    <div>
    <div class="grid grid-cols-3 text-sm" v-if="todayBreakTimes.length">
      <div
        class="col-span-1 flex items-center justify-center border border-gray-200 py-2.5 cursor-default"
        @click="displayBreakStatus"
      >
        Tổng thời gian nghỉ
      </div>
      <div
        class="col-span-2 border border-gray-200 bg-white py-2 flex items-center"
        @click="displayBreakStatus"
      >
        <div class="flex justify-center items-center flex-grow pl-[24px]">
          <div v-if="todayTotalBreakTime.hours > 0" class="flex items-center gap-1">
            <div class="text-lg font-semibold px-1">{{ todayTotalBreakTime.hours }}</div>
            <div class="text-[10px] mt-2 text-gray-600">giờ</div>
            <div class="text-lg font-semibold px-1">{{ todayTotalBreakTime.minutes }}</div>
            <div class="text-[10px] mt-2 text-gray-600">phút</div>
          </div>
          <div v-else class="flex items-center gap-1">
            <div class="text-lg font-semibold px-1">{{ todayTotalBreakTime.minutes }}</div>
            <div class="text-[10px] mt-2 text-gray-600">phút</div>
          </div>
        </div>
        <div v-if="breakStatus">
          <DropdownIcon />
        </div>
        <div v-else>
          <DropdownBlackIcon />
        </div>
      </div>
    </div>

    <div
      v-for="(breakTime, index) in todayBreakTimes"
      :key="index"
      v-show="breakStatus"
      class="grid grid-cols-3 text-sm"
    >
      <div
        class="col-span-1 flex items-center justify-center border border-gray-200 py-2.5"
      >
        Nghỉ giải lao
        <small
          class="border border-black flex items-center justify-center w-4 h-4 rounded-full ml-2"
        >{{ index + 1 }}</small>
      </div>
      <div
        class="col-span-2 border border-gray-200 bg-white py-2 flex justify-between items-center gap-1"
      >
        <div v-if="breakTime.hours > 0" class="flex items-center justify-center flex-grow gap-1">
          <div class="text-lg font-semibold px-1">{{ breakTime.hours }}</div>
          <div class="text-[10px] mt-2 text-gray-600">giờ</div>
          <div class="text-lg font-semibold px-1">{{ breakTime.minutes }}</div>
          <div class="text-[10px] mt-2 text-gray-600">phút</div>
        </div>
        <div v-else class="flex items-center flex-grow justify-center gap-1">
          <div class="text-lg font-semibold px-1">{{ breakTime.minutes }}</div>
          <div class="text-[10px] mt-2 text-gray-600">phút</div>
        </div>
      </div>
    </div>
  </div>
        </div>
      </div>

      <div>
        <div
          class="flex items-center justify-center pt-7 mb-2 font-bold cursor-default"
          @click="displayMonthStatus"
        >
          <h4 class="text-[#286fee]">Tình trạng làm việc trong tháng</h4>
          <div v-if="monthStatus">
            <DropdownIcon />
          </div>
          <div v-else>
            <DropdownBlackIcon />
          </div>
        </div>
        <div v-show="monthStatus">
          <div class="grid grid-cols-3 text-sm">
            <div
              class="col-span-1 flex items-center justify-center border border-gray-200 py-2.5"
            >
              Số ngày làm việc
            </div>
            <div
              class="col-span-2 border border-gray-200 bg-white py-2 flex justify-center items-center gap-1"
            >
              <div class="text-lg font-semibold">{{ dayWorking }}</div>
              <div class="text-[10px] mt-2 text-gray-600">ngày</div>
            </div>
          </div>

          <div class="grid grid-cols-3 text-sm">
            <div
              class="col-span-1 flex items-center justify-center border border-gray-200 py-2.5"
            >
              Thời gian làm việc
            </div>
            <div
              class="col-span-2 border border-gray-200 bg-white py-2 flex justify-center items-center gap-1"
            >
              <div class="text-lg font-semibold">
                {{ workingTime.hours }}
              </div>
              <div class="text-[10px] mt-2 text-gray-600">giờ</div>
              <div class="text-lg font-semibold">
                {{ workingTime.minutes }}
              </div>
              <div class="text-[10px] mt-2 text-gray-600">phút</div>
            </div>
          </div>

          <div class="grid grid-cols-3 text-sm">
            <div
              class="col-span-1 flex items-center justify-center border border-gray-200 py-2.5"
            >
              Lương dự kiến
            </div>
            <div
              class="col-span-2 border border-gray-200 bg-white py-2 flex justify-center items-center gap-1"
            >
              <div class="text-lg font-semibold">{{ salary }}</div>
              <div class="text-[11px] mt-2 text-gray-600">VND</div>
            </div>
          </div>

          <div v-if="isLoading">Loading...</div>
            <div v-else>
                <MonthData :data="monthData" />
            </div>
        </div>
      </div>

      <div>
        
      </div>

      <div class="pb-10">
        <!-- <WarningButton @click="beforeChangePage('startBreak')"
                    class="text-center h-full whitespace-nowrap w-full py-6" v-if="isActiveButton('START_BREAK')">
                    Bắt đầu nghỉ giải lao
                </WarningButton>

                <PrimaryButton @click="beforeChangePage('endBreak')"
                    class="text-center h-full whitespace-nowrap w-full py-6" v-else-if="isActiveButton('END_BREAK')">
                    Kết thúc nghỉ giải lao
                </PrimaryButton> -->

        <!-- <LightButton class="text-center h-full whitespace-nowrap w-full py-6" v-else>
                    Bắt đầu nghỉ giải lao
                </LightButton> -->
      </div>
    </div>

    <template v-for="(page, index) in listPages" :key="index">
      <AttendanceDetail
        v-if="pageName == page.type"
        :type="page.type"
        :user-name="user.name"
        :time-attendance="timeAttendance"
        :shop-name="shopName"
        @change-page="changePage"
        :total-break="totalBreak"
        @perform-action="performAction"
      />
    </template>
  </UserAuthenticatedLayout>
</template>
