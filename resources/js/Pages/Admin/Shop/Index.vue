<script setup>
import PageTitle from "@/Components/shared/PageTitle.vue";
import SecondaryButton from "@/Components/shared/SecondaryButton.vue";
import AdminAuthenticatedLayout from "@/Layouts/AdminAuthenticatedLayout.vue";
import TextInput from "@/Components/shared/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/shared/InputError.vue";

const props = defineProps({
  shop_info: {
    type: Object,
    default: {},
  },
  auth: {
    type: Object,
    default: {},
  },
});

const form = useForm({
  shop: {
    name: props.shop_info?.name,
    setting_stamp: props.shop_info?.setting_stamp,
    setting_night_stamp: props.shop_info?.setting_night_stamp,
    setting_minute: props.shop_info?.setting_minute,
    rounding_type: props.shop_info?.rounding_type,
  },
  admin: {
    name: props.auth?.user?.name,
    email: props.auth?.user?.email,
    password: "",
    password_confirmation: "",
  },
});

const handleChangeSettingStampType = (type) => {
  if (type == 1 && form.shop.setting_stamp == 1) {
    form.shop.setting_stamp = 2;
    form.shop.rounding_type = null;
    return;
  }

  if (type == 2 && form.shop.setting_stamp == 2) {
    form.shop.setting_stamp = 1;
    form.shop.rounding_type = 1;
    return;
  }

  form.shop.setting_stamp = type;

  if(type == 2) {
    form.shop.rounding_type = null;
  } else {
    form.shop.rounding_type = 1;
    form.shop.setting_minute = form.shop.setting_minute ? form.shop.setting_minute : 5;
  }
};

const handleChangeSettingNightStampType = (type) => {
  if (type == 1 && form.shop.setting_night_stamp == 1) {
    form.shop.setting_night_stamp = 2;
    return;
  }

  if (type == 2 && form.shop.setting_night_stamp == 2) {
    form.shop.setting_night_stamp = 1;
    return;
  }

  form.shop.setting_night_stamp = type;
  console.log(form.shop.setting_night_stamp);
};

const handleChangeRoundingType = (type) => {
  if (type == 1 && form.shop.rounding_type == 1) {
    return (form.shop.rounding_type = 2);
  }

  if (type == 2 && form.shop.rounding_type == 2) {
    return (form.shop.rounding_type = 1);
  }

  form.shop.rounding_type = type;
};

const submit = () => {
    form.post(route('shop.update'));
};
</script>

<template>
  <Head title="Edit Shop" />

  <AdminAuthenticatedLayout>
    <PageTitle title="CÀI ĐẶT DOANH NGHIỆP" />
    <form @submit.prevent="submit">
      <div class="mt-5">
        <div class="flex items-center text-sm gap-1">
          <div class="text-base text-[#286fee]">•</div>
          <div>Tên cửa hàng</div>
        </div>
        <TextInput
          v-model="form.shop.name"
          class="mt-1 block w-full"
          placeholder="Tên cửa hàng"
          :error="form.errors['shop.name']"
        />
        <InputError :message="form.errors['shop.name']" class="mt-1" />
      </div>

      <div class="mt-5">
        <div class="flex items-center text-sm gap-1">
          <div class="text-base text-[#286fee]">•</div>
          <div>Cài đặt thời gian chấm công một ngày</div>
        </div>
        <div class="bg-[#e5e5e5] border text-sm mt-1 py-3 px-3">
          <div>
            <b>
              Ví dụ）
            </b>
          </div>
          <div>
            <span>Trường hợp chọn </span>
            <b class="text-[#286fee]"> 00:00 ~ 24:00 cùng ngày</b>:
            <span>Thời gian chấm công một ngày từ 00:00 ~ 24:00 <span class="text-[#286fee]">ngày 1/1/2024</span></span>
          </div>
          <div>
            <span>Trường hợp chọn </span>
            <b class="text-[#286fee]"> 06:00 ngày hôm trước ~ 06:00 ngày hôm sau</b>:
            <span>Thời gian chấm công một ngày từ 06:00 <span class="text-[#286fee]">ngày 1/1/2024</span>  đến 06:00 <span class="text-[#286fee]">ngày 2/1/2024</span> </span>
          </div>
        </div>
      </div>

      <div class="flex gap-9">
        <div class="flex items-center text-sm mt-5 gap-1">
            <input class="border border-gray-300" type="checkbox" id="disableSettingNightStamp"
                :checked="form.shop.setting_night_stamp == 2" @change="handleChangeSettingNightStampType(2)" />
            <label for="disableSettingNightStamp">00:00 ~ 24:00 cùng ngày</label>
        </div>

        <div class="flex items-center text-sm mt-5 gap-1">
            <input class="border border-gray-300" type="checkbox" id="allowSettingNightStamp"
                :checked="form.shop.setting_night_stamp == 1" @change="handleChangeSettingNightStampType(1)" />
            <label for="allowSettingNightStamp">06:00 ngày hôm trước ~ 06:00 ngày hôm sau</label>
        </div>

      </div>

      <div class="mt-5">
        <div class="flex items-center text-sm gap-1">
          <div class="text-base text-[#286fee]">•</div>
          <div>Tên quản lý</div>
        </div>
        <TextInput
          v-model="form.admin.name"
          class="mt-1 block w-full"
          placeholder="Tên quản lý"
          :error="form.errors['admin.name']"
        />
        <InputError :message="form.errors['admin.name']" class="mt-1" />
      </div>

      <div class="mt-5">
        <div class="flex items-center text-sm gap-1">
          <div class="text-base text-[#286fee]">•</div>
          <div>Email quản lý</div>
        </div>
        <TextInput
          v-model="form.admin.email"
          class="mt-1 block w-full"
          placeholder="Email quản lý"
          :error="form.errors['admin.email']"
        />
        <InputError :message="form.errors['admin.email']" class="mt-1" />
      </div>

      <div class="mt-5">
        <div class="flex items-center text-sm gap-1">
          <div class="text-base text-[#286fee]">•</div>
          <div>Mật khẩu hiện tại</div>
        </div>
        <TextInput class="mt-1 block w-full" value="*************" disabled />
      </div>

      <div class="mt-5">
        <div class="flex items-center text-sm gap-1">
          <div class="text-base text-[#286fee]">•</div>
          <div>Mật khẩu mới</div>
        </div>
        <TextInput
          v-model="form.admin.password"
          class="mt-1 block w-full"
          placeholder="Mật khẩu"
          :error="form.errors['admin.password']"
        />
        <InputError :message="form.errors['admin.password']" class="mt-1" />
      </div>

      <div class="mt-5">
        <div class="flex items-center text-sm gap-1">
          <div class="text-base text-[#286fee]">•</div>
          <div>Mật khẩu mới（Xác nhận）</div>
        </div>
        <TextInput
          v-model="form.admin.password_confirmation"
          class="mt-1 block w-full"
          placeholder="Mật khẩu"
          :error="form.errors['admin.password_confirmation']"
        />
        <InputError :message="form.errors['admin.password']" class="mt-1" />
      </div>

      <!-- <div class="mt-5">
        <div class="flex items-center text-sm gap-1">
          <div class="text-base text-[#286fee]">•</div>
          <div>アカウント作成キー</div>
        </div>
        <TextInput class="mt-1 block w-full" value="*************" disabled />
      </div> -->

      <!-- <div class="mt-7 flex items-center justify-center text-sm">
        <Link :href="route('contact')">
          <div class="text-[#286fee]">Yêu cầu xóa tài khoản ở đây</div>
        </Link>
      </div> -->

      <div class="mt-7 flex items-center justify-center text-sm pb-8">
        <SecondaryButton
          type="submit"
          class="text-center h-full whitespace-nowrap w-1/2 py-4"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
        >
          Lưu
        </SecondaryButton>
      </div>
    </form>
  </AdminAuthenticatedLayout>
</template>
