<script setup>
import PageTitle from "@/Components/shared/PageTitle.vue";
import SecondaryButton from "@/Components/shared/SecondaryButton.vue";
import ImportButton from "@/Components/shared/ImportButton.vue";
import AdminAuthenticatedLayout from "@/Layouts/AdminAuthenticatedLayout.vue";
import ListEmployee from "@/Components/employee/ListEmployee.vue";
import { Head, Link } from "@inertiajs/vue3";
import { onMounted, ref, watch } from "vue";
import axios from "axios";
import { useToast } from 'vue-toastification';


const toast = useToast();

const props = defineProps({
  totalUser: {
    type: Number,
    default: 0,
  },
  positions: {
    type: Object,
    default: {},
  },
  positionType: {
    default: 'all'
  }
});

const users = ref([]);
const positionType = ref(props.positionType);
const searchQuery = ref('');
const fileInput = ref(null);

watch([positionType, searchQuery], ([newPosition, newSearchQuery], [oldPosition, oldSearchQuery]) => {
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('position', newPosition);
    currentUrl.searchParams.set('search', newSearchQuery);
    const newUrl = currentUrl.toString();
    history.replaceState(null, null, newUrl);

    getListUser(newPosition, newSearchQuery);
})

onMounted(() => {
    getListUser(props.positionType, searchQuery.value);
})

const getListUser = async (type, search) => {
    try {
        const { data } = await axios.get(`/employee/get-list`, {
            params: {
                position: type,
                search: search,
            }
        });
        users.value = data.data;
    } catch (error) {
        console.error("Failed to fetch user list", error);
    }
}

const openFileDialog = () => {
    fileInput.value.click();
}

const importProgress = ref(0);
const importing = ref(false);

const handleFileUpload = async (event) => {
    const file = event.target.files[0];
    if (file) {
        const formData = new FormData();
        formData.append('file', file);

        try {
            importing.value = true;
            importProgress.value = 0;

            // Fake progress increment
            const fakeProgressInterval = setInterval(() => {
                if (importProgress.value < 95) {
                    importProgress.value += 7;
                }
            }, 200);

            const response = await axios.post('/employee/import', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });

            clearInterval(fakeProgressInterval);
            importProgress.value = 100;

            // Refresh the user list or show success message
            getListUser(positionType.value, searchQuery.value);
            toast.success('Import thành công!');
        } catch (error) {
            console.error("Import lỗi", error);
            toast.error('Import lỗi!');
        } finally {
            importing.value = false;
            setTimeout(() => {
                importProgress.value = 0;
            }, 1000); // Reset progress after 1 second
        }
    }
}

</script>

<template>
  <Head title="Employee" />

  <AdminAuthenticatedLayout>
    <PageTitle title="QUẢN LÝ NHÂN VIÊN" />

    <div class="flex justify-end items-center gap-4 my-7">
      <button
        :type="type"
        class="flex items-center px-4 h-[52px] border-2 border-[#E1E3EA] rounded-md font-semibold text-xs text-black uppercase tracking-widest shadow-sm hover:bg-gray-400"
        @click="openFileDialog"
      >
        <svg xmlns="http://www.w3.org/2000/svg" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" image-rendering="optimizeQuality" fill-rule="evenodd" clip-rule="evenodd" viewBox="0 0 379 511.54" width="20px" height="20px" class="mr-2">
          <path fill-rule="nonzero" d="M107.83 0h194.21c21.17 0 40.41 8.68 54.34 22.61C370.33 36.56 379 55.82 379 76.96v357.62c0 21.06-8.69 40.29-22.63 54.26l-.1.1c-13.97 13.93-33.19 22.6-54.23 22.6H107.83c-21.15 0-40.41-8.67-54.36-22.62-13.93-13.93-22.61-33.17-22.61-54.34V360.6h37.55v73.98c0 10.81 4.45 20.67 11.59 27.81 7.15 7.15 17.02 11.6 27.83 11.6h194.21c10.83 0 20.7-4.43 27.8-11.54 7.18-7.17 11.61-17.04 11.61-27.87V76.96c0-10.8-4.45-20.67-11.6-27.82-7.13-7.14-17-11.59-27.81-11.59H107.83c-10.84 0-20.7 4.44-27.84 11.58-7.14 7.13-11.58 17-11.58 27.83v73.96H30.86V76.96c0-21.17 8.66-40.42 22.6-54.36C67.4 8.66 86.65 0 107.83 0zm59.06 161.72 97.02 91.6-101.77 96.42-25.8-27.12 50.94-48.28L0 274.66v-37.39l192.03-.33-50.8-47.96 25.66-27.26z"/>
        </svg>
        Nhập danh sách nhân viên
      </button>

      <input type="file" ref="fileInput" @change="handleFileUpload" class="hidden" />
      
      <Link :href="route('users.create')">
        <SecondaryButton class="max-w-fit flex items-center text-center justify-center py-4 gap-2 min-w-[285.75px]">
          <svg fill="#ffffff" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
              width="20px" height="20px" viewBox="0 0 45.402 45.402" xml:space="preserve">
            <g>
              <path d="M41.267,18.557H26.832V4.134C26.832,1.851,24.99,0,22.707,0c-2.283,0-4.124,1.851-4.124,4.135v14.432H4.141 c-2.283,0-4.139,1.851-4.138,4.135c-0.001,1.141,0.46,2.187,1.207,2.934c0.748,0.749,1.78,1.222,2.92,1.222h14.453V41.27 c0,1.142,0.453,2.176,1.201,2.922c0.748,0.748,1.777,1.211,2.919,1.211c2.282,0,4.129-1.851,4.129-4.133V26.857h14.435 c2.283,0,4.134-1.867,4.133-4.15C45.399,20.425,43.548,18.557,41.267,18.557z"/>
            </g>
          </svg>
          Đăng ký nhân viên mới
        </SecondaryButton>
      </Link>
    </div>

    <div v-if="importing" class="w-full bg-gray-200 rounded-full h-4 mb-4">
      <div :style="{ width: importProgress + '%' }" class="bg-blue-600 h-4 rounded-full transition-all duration-500"></div>
    </div>

    <div
      class="bg-[#e8e8e8] mt-2 text-[12px] flex items-center justify-center py-[10px]"
    >
      Tổng số nhân viên:
      <div class="text-base text-[#286fee] mx-2">{{ totalUser }} </div>
      người
    </div>

    <div class="flex flex-col md:flex-row gap-3 items-center text-sm mt-4">
      <form class="w-full md:w-1/2 flex items-center border border-gray-300 bg-gray-50 rounded-[2px] mb-2 md:mb-0">
        <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
        <div class="relative w-full">
          <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
            </svg>
          </div>
          <input
            type="search"
            id="default-search"
            class="block w-full p-2 pl-10 text-sm text-gray-900 border-0 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 rounded-[2px]"
            placeholder="Tìm kiếm tên, biệt danh..."
            required
            v-model="searchQuery"
          />
        </div>
      </form>

      <div class="flex w-full md:w-1/2 flex-col md:flex-row justify-end items-center gap-2">
        <div class="flex justify-start items-center md:justify-end text-sm gap-1 w-full md:w-1/4">
          <div class="text-base text-[#286fee]">•</div>
          <div>Lọc theo chức vụ</div>
        </div>

        <select
          v-model="positionType"
          class="bg-gray-50 border border-gray-300 text-sm rounded-[2px] focus:ring-blue-500 focus:border-blue-500 block w-full md:w-1/2 px-2 py-3"
        >
          <option value="all" :selected="positionType == 'all'">Hãy Chọn chức vụ</option>
          <option
            v-for="option in positions"
            :key="option.NAME"
            :value="option.ID"
            :selected="positionType == option.ID"
          >
            {{ option.NAME }}
          </option>
        </select>
      </div>
    </div>

    <div class="py-8">
      <ListEmployee :users="users" :positions="positions" />
    </div>
  </AdminAuthenticatedLayout>
</template>
