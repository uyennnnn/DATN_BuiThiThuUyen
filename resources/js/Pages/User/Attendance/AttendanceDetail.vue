<script setup>
import { ref, computed } from "vue";
import AttendanceItem from "@/Components/employee/AttendanceItem.vue";
import LightButton from "@/Components/shared/LightButton.vue";
import SecondaryButton from "@/Components/shared/SecondaryButton.vue";
import PrimaryButton from "@/Components/shared/PrimaryButton.vue";
import WarningButton from "@/Components/shared/WarningButton.vue";

const emit = defineEmits(["changePage", "performAction"]);

const props = defineProps({
  title: String,
  type: String,
  timeAttendance: String,
  userName: String,
  shopName: String,
  totalBreak: String,
});

const isButtonDisabled = ref(false);

const timeAttendanceLabel = computed(() => {
  if(props.type === 'startBreak') return '休憩開始'

  if(props.type === 'endBreak') return '休憩終了'

  if(props.type === 'checkOut') return '勤務終了'

  return '勤務開始'
});

const handleSubmit = (action) => {
  isButtonDisabled.value = true;
  emit("performAction", action);
};
</script>

<template>
  <div class="text-center mt-7 mb-3 font-bold">
    <h4 class="text-[#286fee]">{{ title }}</h4>
  </div>

  <AttendanceItem :label="'勤務先'">{{ shopName }}</AttendanceItem>
  <AttendanceItem :label="'従業員名'" :textColor="'#286fee'">{{
      userName
    }}</AttendanceItem>
  <AttendanceItem v-if="type == 'endBreak'" :label="'休憩時間'" :textColor="'#286fee'">
    <div>
      {{ totalBreak }}
    </div>
  </AttendanceItem>
  <AttendanceItem :label="timeAttendanceLabel" :textColor="'#286fee'">
    <div>
      {{ timeAttendance }}
    </div>
  </AttendanceItem>

  <div class="flex mt-4 py-5 gap-3 justify-center pb-8">
    <LightButton class="text-center whitespace-nowrap w-1/2 py-6" @click="$emit('changePage')">
      キャンセル
    </LightButton>

    <SecondaryButton v-show="type == 'checkIn'" type="submit" class="text-center whitespace-nowrap w-1/2 py-6"
      :class="{ 'opacity-25': isButtonDisabled }" @click="handleSubmit('checkIn')" :disabled="isButtonDisabled">
      出勤する
    </SecondaryButton>

    <PrimaryButton v-show="type == 'checkOut'" type="submit" class="text-center whitespace-nowrap w-1/2 py-6"
      :class="{ 'opacity-25': isButtonDisabled }" @click="handleSubmit('checkOut')" :disabled="isButtonDisabled">
      退勤
    </PrimaryButton>

    <WarningButton v-show="type == 'startBreak'" type="submit" class="text-center whitespace-nowrap w-1/2 py-6"
      :class="{ 'opacity-25': isButtonDisabled }" @click="handleSubmit('startBreak')" :disabled="isButtonDisabled">
      休憩開始
    </WarningButton>

    <PrimaryButton v-show="type == 'endBreak'" type="submit" class="text-center whitespace-nowrap w-1/2 py-6"
      :class="{ 'opacity-25': isButtonDisabled }" @click="handleSubmit('endBreak')" :disabled="isButtonDisabled">
      休憩終了
    </PrimaryButton>
  </div>
</template>

<style scoped>
button {
  font-weight: bold;
}
</style>
