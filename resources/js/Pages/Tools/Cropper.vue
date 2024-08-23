<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {aiHttpRequest, showTopSnackbar, editImage, buildSubmitParams,checkSubmitParams, getSrc} from '../../network';
import { onMounted, reactive, provide, ref} from "vue";
import UploadPhoto from '@/Components/UploadPhoto.vue';
import PhotoCroper from '@/Components/DzPhotoCroper.vue';
import {usePage } from '@inertiajs/vue3';
import {trans} from 'laravel-vue-i18n'
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;

const page = usePage();
const props = defineProps(['idPhotoStyle','base64']);

onMounted(() => {
    if(props.base64){
        renderImage(props.base64);
    }
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        // wsHost: import.meta.env.VITE_REVERB_HOST,
        wsHost: page.props.reverbHost,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
    });
});

const state = reactive({
    originImage:'',
    cropImage:'',
    exportImage:'',
    task_id:'',
    sizeConfig:{},
    isLoading:false,
});

provide('idPhotoStyle', props.idPhotoStyle);

function renderImage(data){
    var img = new Image();
    img.src = data;
    img.onload = function() {
        state.originImage = data;
    };
}

function getCropFile(base64 , size, format){
    state.sizeConfig = size;
    var arr = base64.split(',');
    var mime = arr[0].match(/:(.*?);/)[1];
    console.log('mime=',mime);
    var bstr = atob(arr[1]);
    var n = bstr.length;
    var u8arr = new Uint8Array(n);
    while (n--) {
        u8arr[n] = bstr.charCodeAt(n);
    }
    var blob = new Blob([u8arr], { type: mime });
    if(format == 'png'){
        var url = URL.createObjectURL(blob);
        let randomNumber = Math.floor(Math.random() * 100) + 1;
        const link = document.createElement("a");
        link.href = url;
        link.target = '_blank';
        link.download = `fluxsd-edit${randomNumber}.${format}`;
        // document.body.appendChild(link);
        link.click();
        setTimeout(function() {
            // document.body.removeChild(link);
            window.URL.revokeObjectURL(url);  
        }, 0); 
    }else{
        convertPngToJpeg(blob, 90);
    }
}

function convertPngToJpeg(pngBlob, quality) {
    let canvas = document.createElement('canvas');
    let context = canvas.getContext('2d');
    console.log("导出jpeg");
    let img = new Image();
    let url = URL.createObjectURL(pngBlob);
    img.src = url;
    img.onload = function() {
        canvas.width = img.width;
        canvas.height = img.height;
        context.drawImage(img, 0, 0);
        canvas.toBlob(function(jpegBlob) {
            console.log("导出jpeg", jpegBlob.length);
            let randomNumber = Math.floor(Math.random() * 100) + 1;
            let link = document.createElement("a");
            let jpegUrl = URL.createObjectURL(jpegBlob);
            link.href = jpegUrl;
            link.target = '_blank';
            link.download = `fluxsd-edit${randomNumber}.jpeg`;
            link.click();
        }, 'image/jpeg', quality);
    };
}



async function removeBG(){
    //login?
    if(page.props.auth.user == null){
        showTopSnackbar(trans('Please Log in first'), 'error');
        return;
    }
    editImage('RBG',{init_img_path:state.originImage, engine:'atz'}, callback);
}



async function callback(res) {
    if(res.message_type == "standing"){
        //get img url and covert to base64
        let formdata = new FormData();
        formdata.append('url', res.show_url);
        state.task_id = res.task_id;
        let base64 = await aiHttpRequest('post', '/api/image/convert', formdata, {'Content-Type':'multipart/form-data'});
        renderImage(base64)
    }
    state.isLoading = false;
}

</script>
<template>
<AppLayout title="Edit images">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
<!-- <div class="sm:w-11/12 lg:w-3/4 mx-auto" :style="dynamicStyle"> -->

    <div v-show="state.originImage == ''" class="flex justify-center h-64 items-center">
        <div class="">
            <UploadPhoto @changeFile="renderImage" :title="$t('Upload a photo')" :withbg="'no'" :key="'uploadwithbg'"></UploadPhoto>
        </div>
    </div>
    <div v-show="state.originImage != ''">
        <PhotoCroper 
            :whiteImage = "state.originImage" 
            @getCropFile="getCropFile" 
            @removeBg="removeBG" 
            :rbgLoading = "state.isLoading"
            @previousStep="state.originImage = ''"></PhotoCroper>
    </div>

    <!-- example -->
    <div class="pt-20"><hr></div>
    <div v-show="state.originImage == ''" class="flex p-20 justify-center gap-8">
        <div class="card card-compact bg-base-100 w-60 shadow-xl">
            <figure>
                <img class=" h-68 object-cover"
                src="/images/crop-1.jpg"
                alt="crop, resize image" />
            </figure>
            <div class="card-body">
                <h2 class="card-title">Crop</h2>
                <p>Crop ,rotation and scale image</p>
            
            </div>
        </div>
        <div class="card card-compact bg-base-100 w-60 shadow-xl">
            <figure>
                <img class=" h-68 object-cover"
                src="/images/crop-2.jpg"
                alt="rotate image" />
            </figure>
            <div class="card-body">
                <h2 class="card-title">Rotation</h2>
                <p>+-360 degree rotation</p>
            
            </div>
        </div>
        <div class="card card-compact bg-base-100 w-60 shadow-xl">
            <figure>
                <img class=" h-68 object-cover" 
                src="/images/crop-3.jpg"
                alt="export png or jpeg" />
            </figure>
            <div class="card-body">
                <h2 class="card-title">PNG/JPEG</h2>
                <p>Export to png or jpeg</p>
            
            </div>
        </div>

    </div>

</div>


</AppLayout>
</template>