<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {onMounted, ref, reactive, onUnmounted} from 'vue';
import { aiHttpRequest } from '@/network';
import TSPaginate from '@/Uilib/TSPaginate.vue';
import TSFloatMask from '@/Uilib/TSFloatMask.vue';
import {trans} from 'laravel-vue-i18n';

const props = defineProps(['extParams','apitoken'])
const isLoading = ref(false);
const pageNumber = ref(1);
const work_image = ref({show_url:null,index:0});
var img_d_idx = 0;
var img_i_idx = 0;
const state = reactive({ models: {
    meta:{
        last_page:0,
        current_page:1,
    }
}});
function setImageIdx(e, d_idx, i_idx){
    img_d_idx = d_idx;
    img_i_idx = i_idx;
}
// defineExpose({handleJump});
async function handleJump (page, isInit=false){
    if(page == undefined){
        page = pageNumber.value ;
    }
    isLoading.value = true;
    pageNumber.value = page;
    let params = props.extParams;
    params.page = page;
    let res = await aiHttpRequest('get', '/api/works',params);
    if(res != null){
        state.models = res;
    }
    isLoading.value = false;
}
function handleSelecte(item){
    // console.log(item);
    work_image.value = item;
    show_big_works.showModal();
    
}
async function handleButton(btn, value){
    console.log(btn , value);
    //delete img
    if(btn == 'crop'){
        work_image.value = value;
        delete_confirm.showModal();
        
    }
}
async function deleteImage(){
    console.log(work_image.value);
    let res = await aiHttpRequest('delete', '/api/images/'+work_image.value.id);
    if(res != null && res['code'] == 1){
        handleJump();
    }
}

function preview_moveto(d_idx, i_idx, act){
    console.log(d_idx, i_idx,act);
    let maxd = state.models.data.length-1;
    if(d_idx < 0 || d_idx > maxd){
        return;
    }
    let maxi = state.models.data[d_idx]['images'].length-1;

    if(i_idx < 0 ){
        d_idx = d_idx -1;
    }
    if(i_idx > maxi){
        d_idx = d_idx +1;
    }
    if(d_idx < 0 || d_idx > maxd){
        return;
    }
    maxi = state.models.data[d_idx]['images'].length-1;
    if(i_idx < 0 ){
        i_idx = maxi;
    }
    if(i_idx > maxi ){
        i_idx = 0;
    }
    let item = state.models.data[d_idx]['images'][i_idx];
    item.d_idx = d_idx;
    item.i_idx = i_idx;
    img_d_idx =  d_idx;
    img_i_idx = i_idx;

    work_image.value = item;
}

onMounted(()=>{
    handleJump(1);
});


</script>

<template>
    <AppLayout title="My images">

        <div class="py-5 bg-gray-100">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-2">
                    <div class="pt-4 pb-4">
                        <div class="inline-flex rounded-md shadow-sm items-center" role="group">
                            <!-- <Paginate @handleJump="handleJump" :total="state.models.meta.last_page" :cpage="state.models.meta.current_page"></Paginate> -->
                            <TSPaginate @handleJump="handleJump" :total="state.models.meta.last_page" :cpage="state.models.meta.current_page"></TSPaginate>

                            <svg v-show="isLoading" aria-hidden="true" role="status" class="ml-4 inline w-6 h-6 text-gray-200 animate-spin dark:text-gray-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="#1C64F2"/>
                            </svg>
                        </div>
                    </div>

                    <div v-for="(coll, d_index) in state.models.data" :key="d_index">
                        <div class="relative code-syntax">
                            
                            <div class="relative">
                                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 xl:grid-cols-6 gap-2">
                                    <div v-for="(img, i_index) in coll.images" :key="'img_'+i_index">
                                        <!-- <img :src="img.thumb" :key="i_index" class="h-60 object-cover cursor-pointer" 
                                        @click="handelShowModal($event, d_index,i_index )" > -->
                                        <TSFloatMask :init="img" mode="big" 
                                        @handleSelectedImage = "handleSelecte"
                                        :notice="img.model_name+'<br/>'+img.prompt_en+'<br/>'+'Scale:'+img.cfg_scale+';Seed:'+img.seed" 
                                        :export="img.width+'✕'+img.height"
                                        :crop = "trans('delete')"
                                        :prompt="img.prompt_en"
                                        @tsComCallback="handleButton"
                                        @click="setImageIdx($event, d_index,i_index )"
                                        ></TSFloatMask>
                                    </div>
                                </div>
                            </div>
                            <div class="grid pb-4 w-full  bg-gray-50 rounded-t-md dark:bg-gray-700 ">
                            <ul class="flex text-sm font-medium text-center text-gray-500 dark:text-gray-400">
                                <li>
                                <span class="inline-block w-full p-4 px-3 m-0 text-gray-800 bg-gray-200  dark:text-white dark:bg-gray-800">
                                    {{coll.label}}
                                </span>
                                </li>
                            </ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- You can open the modal using ID.showModal() method -->
            <!-- <button class="btn" onclick="my_modal_3.showModal()">open modal</button> -->
            <dialog id="show_big_works" class="modal bg-gray-900 bg-opacity-80">
            <div class=" h-max w-max">
                <form method="dialog">
                <button class="btn btn-circle btn-ghost absolute right-2 top-2 text-2xl bg-gray-500 hover:bg-white">✕</button>
                </form>
                <div class="flex items-center gap-x-5 w-screen justify-evenly">
                    <div class=" text-white pb-20">
                        <button @click="preview_moveto(img_d_idx, img_i_idx-1, 'pre')" class="btn btn-circle btn-ghost  right-2 top-2 text-2xl bg-gray-500 hover:bg-white bg-opacity-55 hover:text-black">
                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"></path>
                            </svg>
                        </button>
                    </div>
                    <img class=" h-screen" :src="work_image.show_url"/>
                    <div class=" text-white pb-20">
                        <button @click="preview_moveto(img_d_idx, img_i_idx+1, 'next')"  class="btn btn-circle btn-ghost  right-2 top-2 text-2xl bg-gray-500 hover:bg-white bg-opacity-55 hover:text-black">
                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
            </div>
            </dialog>

            <dialog class="modal" role="dialog" id="delete_confirm">
                <div class="modal-box bg-slate-200">
                    <h3 class="text-lg font-bold">{{trans('warning')}}!</h3>
                    <p class="py-4">{{trans('Are you sure you want to delete it? The deletion cannot be restored')}}</p>
                    <div class="modal-action ">
                        <form method="dialog" class="gap-2 flex">
                            <button class="btn btn-neutral">{{trans('cancel')}}</button>
                            <button @click="deleteImage" class="btn btn-error">{{trans('delete')}}</button>
                        </form>
                        
                    </div>
                </div>
            </dialog>

        </div>
    </AppLayout>
</template>
