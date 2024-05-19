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
    setting_night_stamp: props.shop_info?.setting_night_stamp ?? 2,
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
    <PageTitle title="店舗管理" />
    <form @submit.prevent="submit">
      <div class="mt-5">
        <div class="flex items-center text-sm gap-1">
          <div class="text-base text-[#286fee]">•</div>
          <div>店舗名</div>
        </div>
        <TextInput
          v-model="form.shop.name"
          class="mt-1 block w-full"
          placeholder="店舗名"
          :error="form.errors['shop.name']"
        />
        <InputError :message="form.errors['shop.name']" class="mt-1" />
      </div>

      <div class="mt-5">
        <div class="flex items-center text-sm gap-1">
          <div class="text-base text-[#286fee]">•</div>
          <div>打刻設定</div>
        </div>
        <div class="bg-[#e5e5e5] border text-sm mt-1 py-3 px-3">
          <div>
            <b>
              設定）設定時間：
              <span>10</span>分
              <span class="pl-2">17:05</span>
              打刻
            </b>
          </div>
          <div>
            <b class="text-[#286fee]">切り上げ</b>
            <span>の場合：</span>
            <span>出勤時刻</span>
            <b class="text-[#286fee]">17:10</b>
          </div>
          <div>
            <b class="text-[#286fee]">切り捨て</b>
            <span>の場合：</span>
            <span>出勤時刻</span>
            <b class="text-[#286fee]">17:00</b>
          </div>
        </div>
      </div>

      <div class="flex gap-9">
        <div class="flex items-center text-sm mt-5 gap-1">
          <input
            class="border border-gray-300"
            type="checkbox"
            name="allowSettingTime"
            id="allowSettingTime"
            :checked="form.shop.setting_stamp == 1"
            @change="handleChangeSettingStampType(1)"
          />
          <label for="allowSettingTime">打刻設定する</label>
        </div>

        <div class="flex items-center text-sm mt-5 gap-1">
          <input
            class="border border-gray-300"
            type="checkbox"
            name="disableSettingTime"
            id="disableSettingTime"
            :checked="form.shop.setting_stamp == 2"
            @change="handleChangeSettingStampType(2)"
          />
          <label for="disableSettingTime">打刻設定しない</label>
        </div>
      </div>

      <div class="relative">
        <div
          v-if="form.shop.setting_stamp == 2"
          class="absolute inset-0 bg-[#8b8b8b] opacity-50 pointer-events-none"
        ></div>
        <div
          class="flex gap-7 items-center text-sm mt-5"
          :class="{ 'pointer-events-none': form.shop.setting_stamp == 2 }"
        >
          <div class="flex items-center text-sm gap-1">
            <select
              id="countries"
              v-model="form.shop.setting_minute"
              class="bg-[#ffffff] border border-gray-300 text-sm rounded-[2px] focus:ring-blue-500 focus:border-blue-500 block"
            >
              <option value="5">5</option>
              <option value="10">10</option>
            </select>

            <div class="whitespace-nowrap">分単位</div>
          </div>

          <div class="flex items-center text-sm gap-4">
            <div class="flex items-center text-sm gap-1">
              <input
                class="border border-gray-300"
                type="checkbox"
                name="roundUp"
                id="roundUp"
                :checked="form.shop.rounding_type == 1"
                @change="handleChangeRoundingType(1)"
              />
              <label for="roundUp">切り上げ</label>
            </div>

            <div class="flex items-center text-sm gap-1">
              <input
                class="border border-gray-300"
                type="checkbox"
                name="roundDown"
                id="roundDown"
                :checked="form.shop.rounding_type == 2"
                @change="handleChangeRoundingType(2)"
              />
              <label for="roundDown">切り捨て</label>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-5">
        <div class="flex items-center text-sm gap-1">
          <div class="text-base text-[#286fee]">•</div>
          <div>夜間打刻設定</div>
        </div>
        <div class="bg-[#e5e5e5] border text-sm mt-1 py-3 px-3">
          <div>
            <b>
              0時〜6時の間の打刻を...
            </b>
          </div>
          <div>
            <b class="text-[#286fee]">前日</b>
            <span>に含む場合：</span>
            <b>1/2 2:00</b>
            <span>〜出勤打刻を</span>
            <b class="text-[#286fee]">1/1</b>
            <span>として処理</span>
          </div>
          <div>
            <b class="text-[#286fee]">当日</b>
            <span>に含む場合：</span>
            <b>1/2 2:00</b>
            <span>〜出勤打刻を</span>
            <b class="text-[#286fee]">1/2</b>
            <span>として処理</span>
          </div>
        </div>
      </div>

      <div class="flex gap-9 mt-5 bg-[#8b8b8b] opacity-50 pointer-events-none py-2">
        <div class="flex items-center text-sm gap-1">
          <input
            class="border border-gray-300"
            type="checkbox"
            id="allowSettingNightStamp"
            :checked="form.shop.setting_night_stamp == 1"
            @change="handleChangeSettingNightStampType(1)"
          />
          <label for="allowSettingNightStamp">前日に含む</label>
        </div>

        <div class="flex items-center text-sm gap-1">
          <input
            class="border border-gray-300"
            type="checkbox"
            id="disableSettingNightStamp"
            :checked="form.shop.setting_night_stamp == 2"
            @change="handleChangeSettingNightStampType(2)"
          />
          <label for="disableSettingNightStamp">当日に含む</label>
        </div>
      </div>

      <div class="mt-5">
        <div class="flex items-center text-sm gap-1">
          <div class="text-base text-[#286fee]">•</div>
          <div>管理者名</div>
        </div>
        <TextInput
          v-model="form.admin.name"
          class="mt-1 block w-full"
          placeholder="管理者名"
          :error="form.errors['admin.name']"
        />
        <InputError :message="form.errors['admin.name']" class="mt-1" />
      </div>

      <div class="mt-5">
        <div class="flex items-center text-sm gap-1">
          <div class="text-base text-[#286fee]">•</div>
          <div>管理者メールアドレス</div>
        </div>
        <TextInput
          v-model="form.admin.email"
          class="mt-1 block w-full"
          placeholder="管理者メールアドレス"
          :error="form.errors['admin.email']"
        />
        <InputError :message="form.errors['admin.email']" class="mt-1" />
      </div>

      <div class="mt-5">
        <div class="flex items-center text-sm gap-1">
          <div class="text-base text-[#286fee]">•</div>
          <div>現在のパスワード</div>
        </div>
        <TextInput class="mt-1 block w-full" value="*************" disabled />
      </div>

      <div class="mt-5">
        <div class="flex items-center text-sm gap-1">
          <div class="text-base text-[#286fee]">•</div>
          <div>新しいパスワード</div>
        </div>
        <TextInput
          v-model="form.admin.password"
          class="mt-1 block w-full"
          placeholder="パスワード"
          :error="form.errors['admin.password']"
        />
        <InputError :message="form.errors['admin.password']" class="mt-1" />
      </div>

      <div class="mt-5">
        <div class="flex items-center text-sm gap-1">
          <div class="text-base text-[#286fee]">•</div>
          <div>新しいパスワード（確認用）</div>
        </div>
        <TextInput
          v-model="form.admin.password_confirmation"
          class="mt-1 block w-full"
          placeholder="パスワード"
          :error="form.errors['admin.password_confirmation']"
        />
        <InputError :message="form.errors['admin.password']" class="mt-1" />
      </div>

      <div class="mt-5">
        <div class="flex items-center text-sm gap-1">
          <div class="text-base text-[#286fee]">•</div>
          <div>アカウント作成キー</div>
        </div>
        <TextInput class="mt-1 block w-full" value="*************" disabled />
      </div>

      <div class="mt-7 flex items-center justify-center text-sm">
        <Link :href="route('contact')">
          <div class="text-[#286fee]">アカウント削除依頼はこちら</div>
        </Link>
      </div>

      <div class="mt-7 flex items-center justify-center text-sm pb-8">
        <SecondaryButton
          type="submit"
          class="text-center h-full whitespace-nowrap w-1/2 py-4"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
        >
          変更を保存
        </SecondaryButton>
      </div>
    </form>
  </AdminAuthenticatedLayout>
</template>
