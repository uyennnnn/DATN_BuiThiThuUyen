<script setup>
import PageTitle from "@/Components/shared/PageTitle.vue";
import SecondaryButton from "@/Components/shared/SecondaryButton.vue";
import LightButton from "@/Components/shared/LightButton.vue";
import AdminAuthenticatedLayout from "@/Layouts/AdminAuthenticatedLayout.vue";
import InputError from "@/Components/shared/InputError.vue";
import TextInput from "@/Components/shared/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { onMounted, ref, watch } from "vue";

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
    default: "CyBAR planet 新宿歌舞伎町店",
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
  }
});

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
  if (typeof value === "string" && /^[0-9０-９]+$/.test(value)) {
    return Number(
      value.replace(/[０-９]/g, (s) =>
        String.fromCharCode(s.charCodeAt(0) - 65248)
      )
    );
  } else {
    return value;
  }
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
</script>

<template>
  <Head title="Employee Editor" />

  <AdminAuthenticatedLayout>
    <PageTitle :title="user ? '従業員情報編集' : '従業員新規登録'" />

    <div class="mt-10" v-if="user">
      <div class="grid grid-cols-4 text-sm">
        <div class="col-span-1 text-center border border-gray-200 py-2.5">
          勤務先
        </div>
        <div
          class="col-span-3 text-center border border-gray-200 bg-white py-2.5 font-semibold"
        >
          {{ storeName }}
        </div>
      </div>

      <div class="grid grid-cols-4 text-sm">
        <div class="col-span-1 text-center border border-gray-200 py-2.5">
          従業員名
        </div>
        <div
          class="col-span-3 border border-gray-200 bg-white py-2 font-semibold flex justify-center items-center gap-2"
        >
          <div
            class="text-white text-xs w-[50px] text-center py-[3px] rounded-full"
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
        <div class="text-[#286fee]"><b>従業員情報</b></div>
      </div>

      <div class="mt-5">
        <div class="flex items-center text-sm gap-1">
          <div class="text-base text-[#286fee]">•</div>
          <div>本名</div>
        </div>
        <TextInput
          v-model="form.name"
          class="mt-1 block w-full"
          placeholder="本名"
          :error="form.errors.name"
        />
        <InputError :message="form.errors.name" class="mt-1" />
      </div>

      <div class="mt-6">
        <div class="flex items-center text-sm gap-1">
          <div class="text-base text-[#286fee]">•</div>
          <div>お店での名前（スタッフ名）</div>
        </div>
        <TextInput
          v-model="form.full_name"
          class="mt-1 block w-full"
          placeholder="お店での名前（スタッフ名）"
          :error="form.errors.full_name"
        />
        <InputError :message="form.errors.full_name" class="mt-1" />
      </div>

      <div class="mt-6">
        <div class="flex items-center text-sm gap-1">
          <div class="text-base text-[#286fee]">•</div>
          <div>お店での役割（区分）</div>
        </div>
        <select
          v-model="form.position"
          class="mt-1 block w-full border border-gray-300 rounded focus:border-blue-500 text-gray-500 text-sm"
          :class="{
            '!border-red-500 focus:!border-red-500 focus:!ring-red-500':
              form.errors.position,
          }"
        >
          <option value="" selected>お店での役割（区分）</option>
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
          <div>メールアドレス</div>
        </div>
        <TextInput
          v-model="form.email"
          class="mt-1 block w-full"
          placeholder="メールアドレス"
          :error="form.errors.email"
        />
        <InputError :message="form.errors.email" class="mt-1" />
      </div>

      <div v-if="user" class="mt-6">
        <div class="flex items-center text-sm gap-1">
          <div class="text-base text-[#286fee]">•</div>
          <div>現在のパスワード</div>
        </div>
        <TextInput class="mt-1 block w-full" value="*************" disabled />
      </div>

      <div class="mt-6">
        <div class="flex items-center text-sm gap-1">
          <div class="text-base text-[#286fee]">•</div>
          <div>{{ user ? "新しいパスワード" : "パスワード" }}</div>
        </div>
        <TextInput
          v-model="form.password"
          class="mt-1 block w-full"
          placeholder="パスワード"
          :error="form.errors.password"
        />
        <InputError :message="form.errors.password" class="mt-1" />
      </div>

      <div class="mt-6">
        <div class="flex items-center text-sm gap-1">
          <div class="text-base text-[#286fee]">•</div>
          <div>
            {{ user ? "新しいパスワード(確認用)" : "パスワード確認用" }}
          </div>
        </div>
        <TextInput
          v-model="form.password_confirmation"
          class="mt-1 block w-full"
          placeholder="パスワード"
          :error="form.errors.password_confirmation"
        />
        <InputError :message="form.errors.password_confirmation" class="mt-1" />
      </div>

      <div class="flex mt-10 text-sm">
        <div class="text-[#286fee]"><b>給与設定</b></div>
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
          <label for="hourlySalary">時間給</label>
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
          <label for="fixedSalary">固定給</label>
        </div>
      </div>

      <div v-if="form.salary_type === 2">
        <div class="bg-[#e5e5e5] flex border text-sm mt-5">
          <div class="w-1/4 flex items-center justify-center">基本給</div>
          <div class="w-3/4 bg-[#f8f8f8]">
            <div class="flex justify-end py-4 px-3 border-t gap-2">
              <div class="mt-1 block w-[70%] max-w-[230px]">
                <TextInput
                  v-model="form.salary_fixed"
                  class="w-full"
                  :error="form.errors.salary_fixed"
                />
                <InputError :message="form.errors.salary_fixed" class="mt-1" />
              </div>

              <div class="mt-3">円</div>
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
            基本時給
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
                  class="w-full"
                  :error="form.errors['salary_base.salary']"
                />
                <InputError
                  :message="form.errors['salary_base.salary']"
                  class="mt-1"
                />
              </div>
              <div class="mt-3">円</div>
            </div>
          </div>
        </div>
        <div class="bg-[#e5e5e5] flex border text-sm mt-5">
          <div class="w-1/4 flex items-center justify-center">
            基本時給 <br />（深夜）
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
                  class="w-full"
                  :error="form.errors['salary_night.salary']"
                />
                <InputError
                  :message="form.errors['salary_night.salary']"
                  class="mt-1"
                />
              </div>
              <div class="mt-3">円</div>
            </div>
          </div>
        </div>

        <div class="bg-[#e5e5e5] flex border text-sm mt-5">
          <div class="w-1/4 flex items-center justify-center">
            基本時給 <br />（残業）
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
                  class="w-full"
                  :error="form.errors['salary_overtime.salary']"
                />
                <InputError
                  :message="form.errors['salary_overtime.salary']"
                  class="mt-1"
                />
              </div>
              <div class="mt-3">円</div>
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
            <label for="salary_holiday">休日時給設定する</label>
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
            <label for="not_salary_holiday">休日時給設定しない</label>
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
              休日時給設定する曜日
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
                <label for="saturday">土</label>
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
                <label for="sunday">日</label>
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
                <label for="holiday">祝</label>
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
            <div class="w-1/4 flex items-center justify-center">休日時給</div>
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
                    class="w-full"
                    :error="form.errors['holiday_salary_base.salary']"
                  />
                  <InputError
                    :message="form.errors['holiday_salary_base.salary']"
                    class="mt-1"
                  />
                </div>
                <div class="mt-3">円</div>
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
              休日時給 <br />（深夜）
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
                    class="w-full"
                    :error="form.errors['holiday_salary_night.salary']"
                  />
                  <InputError
                    :message="form.errors['holiday_salary_night.salary']"
                    class="mt-1"
                  />
                </div>
                <div class="mt-3">円</div>
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
              休日時給 <br />（残業）
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
                    class="w-full"
                    :error="form.errors['holiday_salary_overtime.salary']"
                  />
                  <InputError
                    :message="form.errors['holiday_salary_overtime.salary']"
                    class="mt-1"
                  />
                </div>
                <div class="mt-3">円</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="flex mt-10 text-sm">
        <div class="text-[#286fee]"><b>交通費設定</b></div>
      </div>

      <div class="bg-[#e5e5e5] flex border text-sm mt-4">
        <div class="w-1/4 flex items-center justify-center">最寄駅</div>
        <div class="w-3/4 bg-[#f8f8f8]">
          <div class="flex justify-end py-4 px-3 border-t items-center gap-2">
            <div class="mt-1 block w-[80%] max-w-[270px]">
                <TextInput
                v-model="form.nearest_train_station"
                class="mt-1 block w-full md:max-w-[280px]"
                :error="form.errors.nearest_train_station"
                />
                <InputError
                    :message="form.errors.nearest_train_station"
                    class="mt-1"
                />
            </div>
          </div>
        </div>
      </div>

      <div class="bg-[#e5e5e5] flex border text-sm mt-5">
        <div class="w-1/4 flex items-center justify-center">
          交通費<br />(往復)
        </div>
        <div class="w-3/4 bg-[#f8f8f8]">
          <div class="flex justify-end py-4 px-3 border-t gap-2">
            <div class="mt-1 block w-[70%] max-w-[230px]">
              <TextInput
                v-model="form.one_way_travel_expense"
                class="w-full"
                :error="form.errors.one_way_travel_expense"
              />
              <InputError
                :message="form.errors.one_way_travel_expense"
                class="mt-1"
              />
            </div>
            <div class="mt-3">円</div>
          </div>
        </div>
      </div>

      <div class="flex mt-8 py-5 gap-3 justify-center pb-8">
        <Link :href="route('users.index')" class="w-1/2">
          <LightButton
            class="text-center h-full whitespace-nowrap py-4 !w-full"
          >
            戻る
          </LightButton>
        </Link>

        <SecondaryButton
          type="submit"
          class="text-center h-full whitespace-nowrap w-1/2 py-4"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
        >
          {{ user?.id ? "更新" : "登録" }}
        </SecondaryButton>
      </div>
    </form>
  </AdminAuthenticatedLayout>
</template>
