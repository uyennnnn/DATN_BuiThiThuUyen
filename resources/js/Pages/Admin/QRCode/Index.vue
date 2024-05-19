<script setup>
import PageTitle from '@/Components/shared/PageTitle.vue';
import SecondaryButton from '@/Components/shared/SecondaryButton.vue';
import AdminAuthenticatedLayout from '@/Layouts/AdminAuthenticatedLayout.vue';
import ListEmployee from '@/Components/employee/ListEmployee.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    imageUrl: {
        type: String,
    },
});

function downloadQR() {
    var imgElement = document.getElementById('imgQR');
    var canvas = document.createElement('canvas');
    var context = canvas.getContext('2d');
    canvas.width = imgElement.width;
    canvas.height = imgElement.height;
    context.drawImage(imgElement, 0, 0);
    var imgDataUrl = canvas.toDataURL('image/png');
    var downloadLink = document.createElement('a');
    downloadLink.href = imgDataUrl;
    downloadLink.download = 'QR.png';
    downloadLink.click();
}
</script>

<template>
    <Head title="Contact" />

    <AdminAuthenticatedLayout>
        <div>
            <PageTitle title="QRコード"/>

            <div class="text-center text-sm mt-10 text-gray-600">
                従業員の方のスマートフォンがNFC未対応だった場合に<br>
                QRダウンロードを行い従業員の方へLINEやメールで送信してください
            </div>
            <div class="flex items-center justify-center mt-7 py-4 border border-[#e8e8e8]">
                <img :src="imageUrl" alt="" title="" width="400" height="400" id="imgQR" />
            </div>

            <div class="text-center text-sm mt-10 text-gray-400">
                ※QRコードは毎日0時に更新されます
            </div>

            <div class="flex items-center justify-center mt-7">
                <SecondaryButton @click="downloadQR()" type="submit" class="text-center py-4 !px-14 mt-2" >
                    QRダウンロード
                </SecondaryButton>
            </div>

        </div>

    </AdminAuthenticatedLayout>
</template>
