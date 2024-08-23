<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {aiHttpRequest, showTopSnackbar, editImage, buildSubmitParams,checkSubmitParams, getSrc} from '../../network';
import { onMounted, reactive, provide, ref} from "vue";
import UploadPhoto from '@/Components/UploadPhoto.vue';
import {usePage } from '@inertiajs/vue3';
import {trans} from 'laravel-vue-i18n'

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;

const page = usePage();
const props = defineProps(['img_url']);
const step = ref('init');
const toggleStatus = ref(true);

onMounted(() => {
    if(props.img_url){
        renderImage(props.img_url);
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
    workImage:'',
    task_id:'',
    isLoading:false,
    width : 0,
    height :0,
});

function renderImage(data){
    var img = new Image();
    img.src = data;
    // console.log("renderImage=",data);
    img.onload = function() {
        state.workImage = data;
        if(state.originImage == ''){
            state.originImage = data;
        }
        state.width = img.width;
        state.height = img.height;
        step.value = 'preview';
        let image = document.getElementById('image');
        image.src = state.workImage;
    };
}

function cropDone(format = 'png'){
    let randomNumber = Math.floor(Math.random() * 100) + 1;
    const link = document.createElement("a");
    link.href = state.workImage;
    link.target = '_blank';
    link.download = `fluxsd-edit${randomNumber}.${format}`;
    // document.body.appendChild(link);
    link.click();
    
}

async function removeBG(){
    if(page.props.auth.user == null){
        showTopSnackbar(trans('Please Log in first. Users who log in for the first time will receive free usage points'), 'error');
        return;
    }
    state.isLoading = true;
    editImage('RBG',{init_img_path:state.workImage, engine:'atz'}, callback);
}


async function callback(res) {
    console.log("received res=",res);
    if(res.message_type == "standing"){
        renderImage(res.show_url);
        state.task_id = res.task_id;
    }
    if('ephemeral' == res.message_type){
        state.isLoading = true;
    }else{
        state.isLoading = false;
    } 
}

function switchImage(){
    toggleStatus.value = !toggleStatus.value;
    let image = document.getElementById('image');
    image.src = toggleStatus.value ? state.workImage : state.originImage;
}



</script>
<template>
<AppLayout title="Ai remove background">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div v-show="step == 'init'" class="flex justify-center h-64 items-center">
        <div class="">
            <UploadPhoto @changeFile="renderImage" :title="$t('Upload a photo')" :withbg="'no'" :key="'uploadwithbg'"></UploadPhoto>
        
        </div>
    </div>
    <div v-show="step == 'preview'">
        <div class="flex w-full justify-center p-3 gap-2" >
            <div class="flex flex-col h-max min-w-[500px] ">
                <!--preview img-->
                <div class="max-w-3xl"><img id="image" style="display:block;"></div>
                <!--Previous button-->
                <div class="pt-2 h-16 flex gap-2" v-show="props.whiteImage != ''">
                    <button @click="step='init'"
                        type="button"
                        class="btn btn-outline btn-success btn-md">
                        {{$t('Return')}}
                    </button>
                    <button @click="switchImage"
                        type="button"
                        class="btn btn-outline btn-success btn-md">
                        {{$t('Compare')}}
                    </button>
                </div>
            </div>

            <!--right operation button-->
            <div class="ml-5 h-min p-2 min-w-[160px] flex flex-col gap-2" v-show="props.whiteImage != ''">
                <div class="text-center text-secondary ">{{parseInt(state.width) + ' × ' + parseInt(state.height)}}</div>

                <div class="text-center flex">
                    <button v-if="state.isLoading == false" @click="removeBG" 
                        type="button"
                        class="text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium rounded-full w-32 text-sm px-5 py-2.5 text-center me-2 mb-2">
                        {{$t('Remove BG')}}
                    </button>
                    <button v-else disabled type="button" class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-full text-sm w-32 px-5 py-2.5 text-center me-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800 inline-flex items-center cursor-pointer">
                        <svg aria-hidden="true" role="status" class="inline w-4 h-4 me-3 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB"/>
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"/>
                        </svg>
                        {{$t('Removing')}}
                    </button>

       

                </div>

                <div class="text-center mt-5 flex">
                    <button @click="cropDone('png')"
                        type="button"
                        class="text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium rounded-full w-32 text-sm px-5 py-2.5 text-center me-2 mb-2" :disabled="state.isLoading">
                        {{$t('download')}}
                    </button>
           
                </div>

            </div>

        </div>

    </div>
    
    <!-- example -->
    <div class="pt-20"><hr></div>
    <div v-show="state.originImage == ''" class="flex p-20 justify-center gap-8">
        <div class="card card-compact bg-base-100 w-60 shadow-xl">
            <figure>
                <img class=" h-68 object-cover"
                src="/images/rbg-1.jpeg"
                alt="crop, resize image" />
            </figure>
            <div class="card-body">
                <h2 class="card-title">Origin image</h2>
                <p>Support png / jpeg images</p>
            
            </div>
        </div>
        <div class="card card-compact bg-base-100 w-60 shadow-xl">
            <figure>
                <img class=" h-68 object-cover"
                src="/images/rbg-2.jpeg"
                alt="rotate image" />
            </figure>
            <div class="card-body">
                <h2 class="card-title">Remove background</h2>
                <p>The background is clean and the main body is prominent</p>
            
            </div>
        </div>
    </div>

</div>

</AppLayout>
</template>