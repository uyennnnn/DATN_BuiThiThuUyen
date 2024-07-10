<script setup>
import PageTitle from "@/Components/shared/PageTitle.vue";
import SecondaryButton from "@/Components/shared/SecondaryButton.vue";
import LightButton from "@/Components/shared/LightButton.vue";
import AdminAuthenticatedLayout from "@/Layouts/AdminAuthenticatedLayout.vue";
import InputError from "@/Components/shared/InputError.vue";
import TextInput from "@/Components/shared/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { getCurrentInstance, onMounted, ref, watch } from "vue";

const props = defineProps({
  positions: {
    type: Object,
    default: {},
  },
  user: {
    type: Object,
    default: null,
  },
  storeName: {
    type: String,
    default: "Shop A",
  },
});

const form = useForm({
  name: "",
  full_name: "",
  position: "",
  email: "",
  password: "",
  password_confirmation: "",
  salary_type: 1,
  salary_fixed: "",
  salary_base: {
    time_start: null,
    time_end: null,
    salary: null,
  },
  salary_night: {
    time_start: null,
    time_end: null,
    salary: null,
  },
  salary_overtime: {
    time_start: null,
    time_end: null,
    salary: null,
  },
  set_holiday_salary: true,
  set_saturday_salary: 0,
  set_sunday_salary: 0,
  set_celebrate_salary: 0,
  holiday_salary_base: {
    time_start: null,
    time_end: null,
    salary: null,
  },
  holiday_salary_night: {
    time_start: null,
    time_end: null,
    salary: null,
  },
  holiday_salary_overtime: {
    time_start: null,
    time_end: null,
    salary: null,
  },
  one_way_travel_expense: "",
  nearest_train_station: "",
  role: 2,
});

const isSalaryTimeRangeOverlap = ref(null);

const isHolidaySalaryTimeRangeOverlap = ref(null);

onMounted(() => {
  if (props.user) {
    Object.assign(form, props.user);
    formatInitialDisplay();
  }

});

function formatInitialDisplay() {
  form.salary_base.salary = formatCurrency(form.salary_base.salary);
  form.salary_night.salary = formatCurrency(form.salary_night.salary);
  form.salary_overtime.salary = formatCurrency(form.salary_overtime.salary);
  form.holiday_salary_base.salary = formatCurrency(form.holiday_salary_base.salary);
  form.holiday_salary_night.salary = formatCurrency(form.holiday_salary_night.salary);
  form.holiday_salary_overtime.salary = formatCurrency(form.holiday_salary_overtime.salary);
  form.salary_fixed = formatCurrency(form.salary_fixed);
  form.one_way_travel_expense = formatCurrency(form.one_way_travel_expense);
}


const handleTypeSalaryChange = (type) => {
  if (type === 2 || (form.set_holiday_salary == 1 && type == 1)) {
    form.salary_base = {
      time_start: null,
      time_end: null,
      salary: null,
    };
    form.salary_night = {
      time_start: null,
      time_end: null,
      salary: null,
    };
    form.salary_overtime = {
      time_start: null,
      time_end: null,
      salary: null,
    };
    form.set_holiday_salary = true;
    form.set_saturday_salary = 0;
    form.set_sunday_salary = 0;
    form.set_celebrate_salary = 0;
    form.holiday_salary_base = {
      time_start: null,
      time_end: null,
      salary: null,
    };
    form.holiday_salary_night = {
      time_start: null,
      time_end: null,
      salary: null,
    };
    form.holiday_salary_overtime = {
      time_start: null,
      time_end: null,
      salary: null,
    };
  }

  if (type === 1 || (form.set_holiday_salary == 2 && type == 2)) {
    form.salary_fixed = "";
  }

  if (type == 1 && form.salary_type == 1) {
    form.salary_type = 2;
  } else if (type == 2 && form.salary_type == 2) {
    form.salary_type = 1;
  } else {
    form.salary_type = type;
  }

  if (type === 2) {
    form.set_holiday_salary = false;
  } else {
    form.set_holiday_salary = true;
  }
};

const handleHolidaySalaryChange = (value) => {
  if (!value || form.set_holiday_salary == value) {
    form.set_saturday_salary = 0;
    form.set_sunday_salary = 0;
    form.set_celebrate_salary = 0;
    form.holiday_salary_base = {
      time_start: null,
      time_end: null,
      salary: null,
    };
    form.holiday_salary_night = {
      time_start: null,
      time_end: null,
      salary: null,
    };
    form.holiday_salary_overtime = {
      time_start: null,
      time_end: null,
      salary: null,
    };
  }

  if (form.set_holiday_salary == value) {
    form.set_holiday_salary = !value;
  } else {
    form.set_holiday_salary = value;
  }
};

const getPosition = (positionId) => {
  return Object.values(props.positions).find((item) => item.ID == positionId);
};

const convertSalaryFieldsToNumeric = (salaryFields) => {
  if (salaryFields?.salary) {
    salaryFields.salary = convertToNumeric(salaryFields.salary);
  }
};

const convertToNumeric = (value) => {
  console.log(value);
  if (typeof value === "string") {
    // Loại bỏ các dấu chấm dùng để phân cách hàng ngàn
    let cleanedValue = value.replace(/\./g, '');

    // Chuyển đổi các chữ số Ả Rập đầy đủ thành chữ số thông thường
    let normalized = cleanedValue.replace(/[０-９]/g, (s) =>
      String.fromCharCode(s.charCodeAt(0) - 65248)
    );

    // Kiểm tra lại sau khi loại bỏ dấu chấm và chuyển đổi chữ số
    if (/^[0-9]+$/.test(normalized)) {
      return Number(normalized);
    }
  }
  return value;
};


const submit = () => {
  convertSalaryFieldsToNumeric(form.salary_base);
  convertSalaryFieldsToNumeric(form.salary_night);
  convertSalaryFieldsToNumeric(form.salary_overtime);
  convertSalaryFieldsToNumeric(form.holiday_salary_base);
  convertSalaryFieldsToNumeric(form.holiday_salary_night);
  convertSalaryFieldsToNumeric(form.holiday_salary_overtime);
  form.one_way_travel_expense = convertToNumeric(form.one_way_travel_expense);
  form.salary_fixed = convertToNumeric(form.salary_fixed);

  if (props.user?.id) {
    form.post(route("users.update", props.user?.id));
  } else {
    form.post(route("users.store"));
  }
};

watch(
  [
    () => form.salary_base.time_start,
    () => form.salary_base.time_end,
    () => form.salary_night.time_start,
    () => form.salary_night.time_end,
    () => form.salary_overtime.time_start,
    () => form.salary_overtime.time_end,
  ],
  (
    [
      newBaseTimeStart,
      newBaseTimeEnd,
      newNightTimeStart,
      newNightTimeEnd,
      newOverTimeStart,
      newOverTimeEnd,
    ],
    []
  ) => {
    handleValidateTimeRange(
      newBaseTimeStart,
      newBaseTimeEnd,
      newNightTimeStart,
      newNightTimeEnd,
      newOverTimeStart,
      newOverTimeEnd
    );
  }
);

watch(
  [
    () => form.holiday_salary_base.time_start,
    () => form.holiday_salary_base.time_end,
    () => form.holiday_salary_night.time_start,
    () => form.holiday_salary_night.time_end,
    () => form.holiday_salary_overtime.time_start,
    () => form.holiday_salary_overtime.time_end,
  ],
  (
    [
      newBaseTimeStart,
      newBaseTimeEnd,
      newNightTimeStart,
      newNightTimeEnd,
      newOverTimeStart,
      newOverTimeEnd,
    ],
    []
  ) => {
    handleValidateTimeRange(
      newBaseTimeStart,
      newBaseTimeEnd,
      newNightTimeStart,
      newNightTimeEnd,
      newOverTimeStart,
      newOverTimeEnd,
      true
    );
  }
);

const handleValidateTimeRange = async (
  newBaseTimeStart,
  newBaseTimeEnd,
  newNightTimeStart,
  newNightTimeEnd,
  newOverTimeStart,
  newOverTimeEnd,
  isHoliday = false
) => {
  if (
    (!newBaseTimeStart && newBaseTimeEnd) ||
    (newBaseTimeStart && !newBaseTimeEnd) ||
    (!newNightTimeStart && newNightTimeEnd) ||
    (newNightTimeStart && !newNightTimeEnd) ||
    (!newOverTimeStart && newOverTimeEnd) ||
    (newOverTimeStart && !newOverTimeEnd)
  ) {
    return;
  }

  const { data } = await axios.post("/api/validate/no-overlap", {
    salary_base: {
      time_start: newBaseTimeStart,
      time_end: newBaseTimeEnd,
    },
    salary_night: {
      time_start: newNightTimeStart,
      time_end: newNightTimeEnd,
    },
    salary_overtime: {
      time_start: newOverTimeStart,
      time_end: newOverTimeEnd,
    },
  });

  if (isHoliday) {
    if (data.result) {
      form.errors.salary_base = null;
    }

    isHolidaySalaryTimeRangeOverlap.value = data?.message;
  } else {
    if (data.result) {
      form.errors.holiday_salary_base = null;
    }

    isSalaryTimeRangeOverlap.value = data?.message;
  }
};

const formatCurrency = (value) => {
  if (!value) return '';
  value = value.toString().replace(/\D/g, '');
  return new Intl.NumberFormat('vi-VN').format(value);
};

const stripCurrency = (value) => {
  if (value === null || value === undefined) return '';
  return value.toString().replace(/[.,\s]/g, ''); // Chuyển đổi value thành chuỗi và xóa bỏ dấu phẩy, chấm và khoảng trắng
};


const currencyDirective = {
  beforeMount(el, binding, vnode) {
    el.addEventListener('input', function (e) {
      let input = e.target.value;
      input = stripCurrency(input);
      if (input) {
        const formattedInput = formatCurrency(input);
        if (formattedInput !== e.target.value) {
          e.target.value = formattedInput;
          vnode.el.dispatchEvent(new Event('input', { bubbles: true }));
        }
      } else {
        e.target.value = '';
        vnode.el.dispatchEvent(new Event('input', { bubbles: true }));
      }
    });

    el.style.textAlign = 'right';
  }
};

// Registering directive
const { appContext } = getCurrentInstance();
appContext.app.directive('currency', currencyDirective);

</script>

<template>
  <Head title="Employee Editor" />

  <AdminAuthenticatedLayout>
    <PageTitle :title="user ? 'SỬA THÔNG TIN NHÂN VIÊN' : 'ĐĂNG KÝ NHÂN VIÊN MỚI'" />

    <div class="mt-10" v-if="user">
      <div class="grid grid-cols-4 text-sm">
        <div class="col-span-1 text-center border border-gray-200 py-2.5">
          Nơi làm việc
        </div>
        <div
          class="col-span-3 text-center border border-gray-200 bg-white py-2.5 font-semibold"
        >
          {{ storeName }}
        </div>
      </div>

      <div class="grid grid-cols-4 text-sm">
        <div class="col-span-1 text-center border border-gray-200 py-2.5">
          Tên nhân viên
        </div>
        <div
          class="col-span-3 border border-gray-200 bg-white py-2 font-semibold flex justify-center items-center gap-2"
        >
          <div
            class="text-white text-xs w-[100px] text-center py-[3px] rounded-full"
            :style="`background-color: ${
              getPosition(user.position)?.TAG_COLOR || '#b3b3b3'
            }`"
          >
            {{ getPosition(user.position)?.NAME || "その他" }}
          </div>
          <div>{{ user?.name }}</div>
        </div>
      </div>
    </div>

    <form @submit.prevent="submit">
      <div class="flex mt-8 text-sm">
        <div class="text-[#286fee]"><b>Thông tin nhân viên</b></div>
      </div>

      <div class="mt-5">
        <div class="flex items-center text-sm gap-1">
          <div class="text-base text-[#286fee]">•</div>
          <div>Họ và tên</div>
        </div>
        <TextInput
          v-model="form.name"
          class="mt-1 block w-full"
          placeholder="Họ và tên"
          :error="form.errors.name"
        />
        <InputError :message="form.errors.name" class="mt-1" />
      </div>

      <div class="mt-6">
        <div class="flex items-center text-sm gap-1">
          <div class="text-base text-[#286fee]">•</div>
          <div>Biệt danh</div>
        </div>
        <TextInput
          v-model="form.full_name"
          class="mt-1 block w-full"
          placeholder="Biệt danh"
          :error="form.errors.full_name"
        />
        <InputError :message="form.errors.full_name" class="mt-1" />
      </div>

      <div class="mt-6">
        <div class="flex items-center text-sm gap-1">
          <div class="text-base text-[#286fee]">•</div>
          <div>Chức vụ</div>
        </div>
        <select
          v-model="form.position"
          class="mt-1 block w-full border border-gray-300 rounded focus:border-blue-500 text-gray-500 text-sm"
          :class="{
            '!border-red-500 focus:!border-red-500 focus:!ring-red-500':
              form.errors.position,
          }"
        >
          <option value="" selected>Chức vụ</option>
          <option
            v-for="option in positions"
            :key="option.NAME"
            :value="option.ID"
          >
            {{ option.NAME }}
          </option>
        </select>
        <InputError :message="form.errors.position" class="mt-1" />
      </div>

      <div class="mt-6">
        <div class="flex items-center text-sm gap-1">
          <div class="text-base text-[#286fee]">•</div>
          <div>Email</div>
        </div>
        <TextInput
          v-model="form.email"
          class="mt-1 block w-full"
          placeholder="Email"
          :error="form.errors.email"
        />
        <InputError :message="form.errors.email" class="mt-1" />
      </div>

      <div v-if="user" class="mt-6">
        <div class="flex items-center text-sm gap-1">
          <div class="text-base text-[#286fee]">•</div>
          <div>Mật khẩu hiện tại</div>
        </div>
        <TextInput class="mt-1 block w-full" value="*************" disabled />
      </div>

      <div class="mt-6">
        <div class="flex items-center text-sm gap-1">
          <div class="text-base text-[#286fee]">•</div>
          <div>{{ user ? "Mật khẩu mới" : "Mật khẩu" }}</div>
        </div>
        <TextInput
          v-model="form.password"
          class="mt-1 block w-full"
          placeholder="Mật khẩu"
          :error="form.errors.password"
        />
        <InputError :message="form.errors.password" class="mt-1" />
      </div>

      <div class="mt-6">
        <div class="flex items-center text-sm gap-1">
          <div class="text-base text-[#286fee]">•</div>
          <div>
            {{ user ? "Mật khẩu mới(Xác nhận)" : "Xác nhận mật khẩu" }}
          </div>
        </div>
        <TextInput
          v-model="form.password_confirmation"
          class="mt-1 block w-full"
          placeholder="Mật khẩu"
          :error="form.errors.password_confirmation"
        />
        <InputError :message="form.errors.password_confirmation" class="mt-1" />
      </div>

      <div class="flex mt-10 text-sm">
        <div class="text-[#286fee]"><b>Cài đặt lương</b></div>
      </div>

      <div class="flex gap-20">
        <div class="flex items-center text-sm mt-5 gap-1">
          <input
            class="border border-gray-300"
            id="hourlySalary"
            type="checkbox"
            value="1"
            :checked="form.salary_type === 1"
            @change="handleTypeSalaryChange(1)"
          />
          <label for="hourlySalary">Lương theo giờ</label>
        </div>

        <div class="flex items-center text-sm mt-5 gap-1">
          <input
            class="border border-gray-300"
            id="fixedSalary"
            type="checkbox"
            value="2"
            :checked="form.salary_type === 2"
            @change="handleTypeSalaryChange(2)"
          />
          <label for="fixedSalary">Lương cố định theo ngày</label>
        </div>
      </div>

      <div v-if="form.salary_type === 2">
        <div class="bg-[#e5e5e5] flex border text-sm mt-5">
          <div class="w-1/4 flex items-center justify-center">Lương 1 ngày</div>
          <div class="w-3/4 bg-[#f8f8f8]">
            <div class="flex justify-end py-4 px-3 border-t gap-2">
              <div class="mt-1 block w-[70%] max-w-[230px]">
                <TextInput
                  v-model="form.salary_fixed"
                  v-currency
                  class="w-full"
                  :error="form.errors.salary_fixed"
                />
                <InputError :message="form.errors.salary_fixed" class="mt-1" />
              </div>

              <div class="mt-3">VND</div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="form.salary_type === 1">
        <div class="mt-5">
          <InputError
            :message="form.errors.salary_base || isSalaryTimeRangeOverlap"
          />
        </div>
        <div class="bg-[#e5e5e5] flex border text-sm">
          <div class="w-1/4 flex items-center justify-center text-sm">
            Lương cơ bản
          </div>
          <div class="w-3/4 bg-[#f8f8f8]">
            <div class="flex items-center gap-1 sm:gap-3 py-5 px-3 justify-end">
              <TextInput
                v-model="form.salary_base.time_start"
                type="time" class="min-w-[80px]"
                :error="
                  form.errors['salary_base.time_start'] ||
                  form.errors.salary_base ||
                  isSalaryTimeRangeOverlap
                "
              />
              ~
              <TextInput
                v-model="form.salary_base.time_end"
                type="time" class="min-w-[80px]"
                :error="
                  form.errors['salary_base.time_end'] ||
                  form.errors.salary_base ||
                  isSalaryTimeRangeOverlap
                "
              />
            </div>
            <div class="flex justify-end py-4 px-3 border-t gap-2">
              <div class="mt-1 block w-[70%] max-w-[230px]">
                <TextInput
                  v-model="form.salary_base.salary"
                  v-currency
                  class="w-full"
                  :error="form.errors['salary_base.salary']"
                />
                <InputError
                  :message="form.errors['salary_base.salary']"
                  class="mt-1"
                />
              </div>
              <div class="mt-3">VND</div>
            </div>
          </div>
        </div>
        <div class="bg-[#e5e5e5] flex border text-sm mt-5">
          <div class="w-1/4 flex items-center justify-center">
            <div class="flex flex-col items-center justify-center">
              <p>
              Lương theo giờ
              </p> 
              <b>
                làm đêm
              </b>
            </div>
          </div>
          <div class="w-3/4 bg-[#f8f8f8]">
            <div class="flex items-center gap-1 sm:gap-3 py-5 px-3 justify-end">
              <TextInput
                v-model="form.salary_night.time_start"
                type="time" class="min-w-[80px]"
                :error="
                  form.errors['salary_night.time_start'] ||
                  form.errors.salary_base ||
                  isSalaryTimeRangeOverlap
                "
              />
              ~
              <TextInput
                v-model="form.salary_night.time_end"
                type="time" class="min-w-[80px]"
                :error="
                  form.errors['salary_night.time_end'] ||
                  form.errors.salary_base ||
                  isSalaryTimeRangeOverlap
                "
              />
            </div>
            <div class="flex justify-end py-4 px-3 border-t gap-2">
              <div class="mt-1 block w-[70%] max-w-[230px]">
                <TextInput
                  v-model="form.salary_night.salary"
                  v-currency
                  class="w-full"
                  :error="form.errors['salary_night.salary']"
                />
                <InputError
                  :message="form.errors['salary_night.salary']"
                  class="mt-1"
                />
              </div>
              <div class="mt-3">VND</div>
            </div>
          </div>
        </div>

        <div class="bg-[#e5e5e5] flex border text-sm mt-5">
          <div class="w-1/4 flex items-center justify-center">
            <div class="flex flex-col items-center justify-center">
              <p>
              Lương theo giờ
              </p> 
              <b>
                tăng ca
              </b>
            </div>
          </div>
          <div class="w-3/4 bg-[#f8f8f8]">
            <div class="flex items-center gap-1 sm:gap-3 py-5 px-3 justify-end">
              <TextInput
                v-model="form.salary_overtime.time_start"
                type="time" class="min-w-[80px]"
                :error="
                  form.errors['salary_overtime.time_start'] ||
                  form.errors.salary_base ||
                  isSalaryTimeRangeOverlap
                "
              />
              ~
              <TextInput
                v-model="form.salary_overtime.time_end"
                type="time" class="min-w-[80px]"
                :error="
                  form.errors['salary_overtime.time_end'] ||
                  form.errors.salary_base ||
                  isSalaryTimeRangeOverlap
                "
              />
            </div>
            <div class="flex justify-end py-4 px-3 border-t gap-2">
              <div class="mt-1 block w-[70%] max-w-[230px]">
                <TextInput
                  v-model="form.salary_overtime.salary"
                  v-currency
                  class="w-full"
                  :error="form.errors['salary_overtime.salary']"
                />
                <InputError
                  :message="form.errors['salary_overtime.salary']"
                  class="mt-1"
                />
              </div>
              <div class="mt-3">VND</div>
            </div>
          </div>
        </div>

        <div class="flex mt-8 text-sm justify-start gap-4">
          <div class="flex items-center gap-1 text-sm">
            <input
              class="border border-gray-300"
              type="checkbox"
              name="salary_holiday"
              id="salary_holiday"
              :checked="form.set_holiday_salary"
              @click="handleHolidaySalaryChange(true)"
            />
            <label for="salary_holiday">Cài đặt lương đặc biệt</label>
          </div>

          <div class="flex items-center gap-1 text-sm">
            <input
              class="border border-gray-300"
              type="checkbox"
              name="not_salary_holiday"
              id="not_salary_holiday"
              :checked="!form.set_holiday_salary"
              @click="handleHolidaySalaryChange(false)"
            />
            <label for="not_salary_holiday">Không cài đặt lương đặc biệt</label>
          </div>
        </div>

        <div class="relative">
          <div
            v-if="!form.set_holiday_salary"
            class="absolute inset-0 bg-[#8b8b8b] opacity-50 pointer-events-none"
          ></div>
          <div
            class="bg-[#e5e5e5] flex border text-sm mt-5 justify-between py-5 px-3"
            :class="{
              'pointer-events-none': !form.set_holiday_salary,
              '!border-red-500': form.errors.set_holiday_salary,
            }"
            :error="form.errors.set_holiday_salary"
          >
            <div class="flex items-center justify-center">
              Ngày áp dụng mức lương đặc biệt
            </div>

            <div class="flex gap-6">
              <div class="flex items-center gap-1 text-sm">
                <input
                  v-model="form.set_saturday_salary"
                  class="border border-gray-300"
                  type="checkbox"
                  name="saturday"
                  id="saturday"
                  :checked="form.set_saturday_salary"
                  :error="form.errors.set_saturday_salary"
                />
                <label for="saturday">Thứ 7</label>
              </div>
              <div class="flex items-center gap-1 text-sm">
                <input
                  v-model="form.set_sunday_salary"
                  class="border border-gray-300"
                  type="checkbox"
                  name="sunday"
                  id="sunday"
                  :checked="form.set_sunday_salary"
                  :error="form.errors.set_sunday_salary"
                />
                <label for="sunday">Chủ Nhật</label>
              </div>
              <div class="flex items-center gap-1 text-sm">
                <input
                  v-model="form.set_celebrate_salary"
                  class="border border-gray-300"
                  type="checkbox"
                  name="holiday"
                  id="holiday"
                  :checked="form.set_celebrate_salary"
                  :error="form.errors.set_celebrate_salary"
                />
                <label for="holiday">Ngày lễ</label>
              </div>
            </div>
          </div>
          <InputError :message="form.errors.set_holiday_salary" class="mt-1" />
        </div>

        <div class="mt-5">
          <InputError
            :message="
              form.errors.holiday_salary_base || isHolidaySalaryTimeRangeOverlap
            "
          />
        </div>
        <div class="relative">
          <div
            v-if="!form.set_holiday_salary"
            class="absolute inset-0 bg-[#8b8b8b] opacity-50 pointer-events-none"
          ></div>
          <div
            class="bg-[#e5e5e5] flex border text-sm"
            :class="{ 'pointer-events-none': !form.set_holiday_salary }"
          >
            <div class="w-1/4 flex items-center justify-center">Lương cơ bản theo giờ</div>
            <div class="w-3/4 bg-[#f8f8f8]">
              <div class="flex items-center gap-1 sm:gap-3 py-5 px-3 justify-end">
                <TextInput
                  v-model="form.holiday_salary_base.time_start"
                  type="time" class="min-w-[80px]"
                  :error="
                    form.errors['holiday_salary_base.time_start'] ||
                    form.errors.holiday_salary_base ||
                    isHolidaySalaryTimeRangeOverlap
                  "
                />
                ~
                <TextInput
                  v-model="form.holiday_salary_base.time_end"
                  type="time" class="min-w-[80px]"
                  :error="
                    form.errors['holiday_salary_base.time_end'] ||
                    form.errors.holiday_salary_base ||
                    isHolidaySalaryTimeRangeOverlap
                  "
                />
              </div>
              <div class="flex justify-end py-4 px-3 border-t gap-2">
                <div class="mt-1 block w-[70%] max-w-[230px]">
                  <TextInput
                    v-model="form.holiday_salary_base.salary"
                    v-currency
                    class="w-full"
                    :error="form.errors['holiday_salary_base.salary']"
                  />
                  <InputError
                    :message="form.errors['holiday_salary_base.salary']"
                    class="mt-1"
                  />
                </div>
                <div class="mt-3">VND</div>
              </div>
            </div>
          </div>
        </div>

        <div class="relative">
          <div
            v-if="!form.set_holiday_salary"
            class="absolute inset-0 bg-[#8b8b8b] opacity-50 pointer-events-none"
          ></div>
          <div
            class="bg-[#e5e5e5] flex border text-sm mt-5"
            :class="{ 'pointer-events-none': !form.set_holiday_salary }"
          >
            <div class="w-1/4 flex items-center justify-center">
              <div class="flex flex-col items-center justify-center">
              <p>
              Lương theo giờ
              </p> 
              <b>
                làm đêm
              </b>
            </div>
            </div>
            <div class="w-3/4 bg-[#f8f8f8]">
              <div class="flex items-center gap-1 sm:gap-3 py-5 px-3 justify-end">
                <TextInput
                  v-model="form.holiday_salary_night.time_start"
                  type="time" class="min-w-[80px]"
                  :error="
                    form.errors['holiday_salary_night.time_start'] ||
                    form.errors.holiday_salary_base ||
                    isHolidaySalaryTimeRangeOverlap
                  "
                />
                ~
                <TextInput
                  v-model="form.holiday_salary_night.time_end"
                  type="time" class="min-w-[80px]"
                  :error="
                    form.errors['holiday_salary_night.time_end'] ||
                    form.errors.holiday_salary_base ||
                    isHolidaySalaryTimeRangeOverlap
                  "
                />
              </div>
              <div class="flex justify-end py-4 px-3 border-t gap-2">
                <div class="mt-1 block w-[70%] max-w-[230px]">
                  <TextInput
                    v-model="form.holiday_salary_night.salary"
                    v-currency
                    class="w-full"
                    :error="form.errors['holiday_salary_night.salary']"
                  />
                  <InputError
                    :message="form.errors['holiday_salary_night.salary']"
                    class="mt-1"
                  />
                </div>
                <div class="mt-3">VND</div>
              </div>
            </div>
          </div>
        </div>

        <div class="relative">
          <div
            v-if="!form.set_holiday_salary"
            class="absolute inset-0 bg-[#8b8b8b] opacity-50 pointer-events-none"
          ></div>
          <div
            class="bg-[#e5e5e5] flex border text-sm mt-5"
            :class="{ 'pointer-events-none': !form.set_holiday_salary }"
          >
            <div class="w-1/4 flex items-center justify-center">
              <div class="flex flex-col items-center justify-center">
              <p>
              Lương theo giờ
              </p> 
              <b>
                tăng ca
              </b>
            </div>
            </div>
            <div class="w-3/4 bg-[#f8f8f8]">
              <div class="flex items-center gap-1 sm:gap-3 py-5 px-3 justify-end">
                <TextInput
                  v-model="form.holiday_salary_overtime.time_start"
                  type="time" class="min-w-[80px]"
                  :error="
                    form.errors['holiday_salary_overtime.time_start'] ||
                    form.errors.holiday_salary_base ||
                    isHolidaySalaryTimeRangeOverlap
                  "
                />
                ~
                <TextInput
                  v-model="form.holiday_salary_overtime.time_end"
                  type="time" class="min-w-[80px]"
                  :error="
                    form.errors['holiday_salary_overtime.time_end'] ||
                    form.errors.holiday_salary_base ||
                    isHolidaySalaryTimeRangeOverlap
                  "
                />
              </div>
              <div class="flex justify-end py-4 px-3 border-t gap-2">
                <div class="mt-1 block w-[70%] max-w-[230px]">
                  <TextInput
                    v-model="form.holiday_salary_overtime.salary"
                    v-currency
                    class="w-full"
                    :error="form.errors['holiday_salary_overtime.salary']"
                  />
                  <InputError
                    :message="form.errors['holiday_salary_overtime.salary']"
                    class="mt-1"
                  />
                </div>
                <div class="mt-3">VND</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="flex mt-8 py-5 gap-3 justify-center pb-8">
        <Link :href="route('users.index')" class="w-1/2">
          <LightButton
            class="text-center h-full whitespace-nowrap py-4 !w-full"
          >
            QUAY LẠI
          </LightButton>
        </Link>

        <SecondaryButton
          type="submit"
          class="text-center h-full whitespace-nowrap w-1/2 py-4"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
        >
          {{ user?.id ? "LƯU" : "ĐĂNG KÝ" }}
        </SecondaryButton>
      </div>
    </form>
  </AdminAuthenticatedLayout>
</template>
