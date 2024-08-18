<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import TSPaginate from '@/Uilib/TSPaginate.vue';
import TSFloatMask from '@/Uilib/TSFloatMask.vue';
import TSScreenImage from '@/Uilib/TSScreenImage.vue';
import { aiHttpRequest, showTopSnackbar } from "@/network";
import {debug,showModalWindow} from '@/Uilib/TSFunction.js'; 
import {trans} from 'laravel-vue-i18n';
import { onMounted, onUnmounted,reactive, ref } from "vue";
const params = reactive({engine:'atz',page:1});
var evtSource = null;
const data = ref({meta:{last_page:1,current_page:1},data:[]});
const props = defineProps({
    apihost:String, 
    modelIds:Array,//downloaded models id
    failedIds:Array,
    dlPercent:Object,//running   => percent
    client_path:String,
});
var downloadedModels = props.modelIds;
const downloadStatus = reactive({});
Object.entries(props.dlPercent).forEach(([key, value]) => {
    downloadStatus[key] = value;
});

const sortField = ref(null);
const orderby = ref(null);
const modelImages = ref([]);
const activeImage = ref(null);
var baseModels = [];
//'1.5','pre1.5','sdxl','presdxl','sd3','Other','SVD XT','PixArt E'
const selectedBaseModels = reactive([]);
//'checkpoint','lora','poses','vae','controlnet','other','upscaler','textualinversion','locon','hypernetwork','wildcards',
//    'motionmodule','workflows','dora','aestheticgradient'
var types = [];
const selectedTypes = reactive([]);
var currentModel = ref({download_url:null, type:null});

onMounted(()=>{
    loadData(1);
    // SSEListen();
});
onUnmounted(() => {
    SSEClose();
});

async function loadData(page){
    params['page'] = page;
    if(selectedBaseModels.length > 0){
        params['options'] = JSON.stringify(selectedBaseModels);
    }
    if(selectedTypes.length > 0){
        params['types'] = JSON.stringify(selectedTypes);
    }

    let res = await aiHttpRequest('get', '/api/models', params);
  
    // console.log(res);
    if(res == null || res.code != 1){
        showTopSnackbar(res.msg??trans("Loading data error"), 'error');
        return;
    }
    baseModels = res.base_models;
    types = res.types;
    data.value = res;
}

//only show local models
function switchOnlyLocalModels(e){
    if(e.target.checked){
        params['engine'] = 'lc'; 
        selectedTypes.splice(0, selectedTypes.length);
        selectedTypes.push('checkpoint','lora');
        selectedBaseModels.splice(0, selectedBaseModels.length);
        selectedBaseModels.push('1.5','pre1.5','sdxl','presdxl');
    }else{
        params['engine'] = 'atz';
    }
    loadData(params['page']);
    // console.log("switchOnlyLocalModels=", e.target.checked, 'selectedBaseModels=',selectedBaseModels);
}

function handleSelecte(item){
    // console.log(item);
    // modelImages.value = [];
    modelImages.value = item.images;
    showModalWindow('show_model_images');
}
function sort(name){
    sortField.value = name;
    orderby.value = orderby.value == 'desc' ? 'asc' :'desc';
    params['sort'] = name;
    params['orderby'] = orderby.value;
    loadData(params['page']);
}

function handleSelectedImage(img){
    activeImage.value = img.thumb;
    showModalWindow('show_full_image');
}

function search(e){
    params['keyword'] = e.target.value;
    loadData(params['page']);
    // console.log(e.target.value);
}

function computeActiveStyle(name){
    if(sortField.value == name){
        return 'font-extrabold text-green-500';
    }else{
        return '';
    }
}
function computeBaseModelStyle(base_model){
    // console.log(selectedBaseModels.indexOf(base_model));
    if(selectedBaseModels.indexOf(base_model) !== -1){
        return 'font-extrabold text-green-500';
    }else{
        return '';
    }
}
function computeTypesStyle(type){
    if(selectedTypes.indexOf(type) !== -1){
        return 'font-extrabold text-green-500';
    }else{
        return '';
    }
}

function selectBaseModel(base_model){
    let index = selectedBaseModels.indexOf(base_model);
    if(index !== -1){
        selectedBaseModels.splice(index, 1);
    }else{
        selectedBaseModels.push(base_model);
    }
    loadData(params['page']);
}
function selectType(type){
    
    let index = selectedTypes.indexOf(type);
    // console.log('type=', type, 'selectedTypes=', selectedTypes, index);
    if(index !== -1){
        selectedTypes.splice(index, 1);
        // console.log('type=', type, 'selectedTypes=', selectedTypes, index);
    }else{
        selectedTypes.push(type);
    }
    loadData(params['page']);
}

async function handleButton(btn, value){
    // console.log(btn , value);
    //download model 
    if(btn == 'crop'){
        if(downloadedModels.includes(value.hash)){
            currentModel.value = value;
            showModalWindow('confirm_download_model');
            return;
        }
        let res = await aiHttpRequest('post', '/api/models/download', value);
        // console.log(res);
        if(res == null || res.code != 1){
            showTopSnackbar(res.msg??trans("Server error"), 'error');
        }else{
            // SSEClose();
            // downloadStatus[value.hash] = 0;
            // SSEListen();
            // showTopSnackbar(trans("It has been into the download queue. Please wait patiently"), 'success');
            // download_url
            currentModel.value = res.data;
            downloadedModels.push(currentModel.value.hash);
            showModalWindow('confirm_download_model');

            const url = currentModel.value.download_url; // Replace with your file URL
            const link = document.createElement('a');
            link.href = url;
            link.download = currentModel.value.sd_name; // Optional: Specify the download name
            link.target = '_blank';
            link.click();
        }
    }else if (btn == 'delete'){
        currentModel.value = value;
        showModalWindow('confirm_delete_model');
    }
}

async function deleteModel(){
    let res = await aiHttpRequest('delete', '/api/models/'+currentModel.value.id);
    if(res == null || res.code != 1){
        showTopSnackbar(res?res.msg : trans("Delete the model failed"), 'error');
    }else{
        showTopSnackbar(trans("Successfully deleted"), 'success');
        loadData(params['page']);
    }
}

function computeDownloadText(model_hash_id, percent){
    // console.log(model_hash_id);
    if(props.modelIds.includes(model_hash_id)){
        return trans("Downloaded");
    }else if(props.failedIds.includes(model_hash_id)){
        return trans("Retry");
    }else{
        if(percent  != undefined){
            return percent + '%';
        }else{
            return trans('download');
        }
    }
}


const SSEClose = ()=>{
    if(evtSource != null){
        // console.log("close sse");
        evtSource.close();
    }
}
const SSEListen = ()=>{
    let progress_ids = Object.keys(downloadStatus);
    evtSource = new EventSource("/api/download/progress?ids="+JSON.stringify(progress_ids));

    evtSource.addEventListener("open", (e) => {
        // console.log("Connection opened");
    });

    evtSource.addEventListener("message", (e) => {
        // console.log("Data: " + e.data);
        let statusJson = JSON.parse(e.data);
        for (var key in statusJson) {
            if (statusJson.hasOwnProperty(key)) {
                // console.log(key + " -> " + statusJson[key]);
                downloadStatus[key] = statusJson[key];
            }
        }

    });

    evtSource.addEventListener("error", (e) => {
        console.log("Error: " + e.message);
    });

    evtSource.addEventListener("notice", (e) => {
        console.log("Notice: " + e.data);
    });
}

</script>
<template>
<AppLayout title="Model market">
    
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-2 bg-slate-50">
        <div class="py-1 flex justify-between h-14">
            <TSPaginate @handleJump="loadData" :total="data.meta.last_page" :cpage="data.meta.current_page"></TSPaginate>
            <div class="flex items-center">
                <label class="input input-bordered flex items-center gap-2 input-sm">
                <input type="text" class="grow border-none input-xs" placeholder="Search" @change="search" v-model="params.keyword" />
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70"><path fill-rule="evenodd" d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z" clip-rule="evenodd" /></svg>
                </label>

                <div class="form-control">
                <label class="label cursor-pointer gap-2 px-2">
                    <span class="label-text">{{trans('Local Models')}}</span>
                    <input @click="switchOnlyLocalModels" type="checkbox" class="checkbox checkbox-sm" />
                </label>
                </div>

                <div>
                    <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class="btn m-1 btn-sm">{{trans('category')}}</div>
                    <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box">
                        <li v-for="(item,index) in types" :key="'li_tp_'+index" :class="computeTypesStyle(item)"><a @click="selectType(item)">{{item}}</a></li>
                    </ul>
                    </div>
                </div>
                <div>
                    <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class="btn m-1 btn-sm">{{trans('filter')}}</div>
                    <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-48">
                        <li v-for="(item,index) in baseModels" :key="'li_bm_'+index" :class="computeBaseModelStyle(item)"><a @click="selectBaseModel(item)">{{item}}</a></li>
                    </ul>
                    </div>
                </div>
                <div>
                    <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class="btn m-1 btn-sm">{{trans('sort')}}</div>
                    <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-48">
                        <li><a @click="sort('type')" :class="computeActiveStyle('type')">{{trans('category')}}</a></li>
                        <li><a @click="sort('rating')" :class="computeActiveStyle('rating')">{{trans('rate')}}</a></li>
                        <!-- <li><a @click="sort('nsfw')" :class="computeActiveStyle('nsfw')">{{trans('nsfw')}}</a></li> -->
                        <li><a @click="sort('is_active')" :class="computeActiveStyle('is_active')">{{trans('active')}}</a></li>
                        <li><a @click="sort('base_model')" :class="computeActiveStyle('base_model')">{{trans('base model')}}</a></li>
                    </ul>
                    </div>
                </div>
            </div>
            
            
        </div>
        <div class="grid grid-cols-6 gap-2">
            <div v-for="(item , index) in data.data" :key="index">
                <!-- <img @click="handleSelecte(item)" :src="item.thumb" class=" object-cover w-full h-72 cursor-pointer"> -->
                <TSFloatMask :init="item" mode="big" 
                @handleSelectedImage="handleSelecte" 
                :notice="'Type:'+item.type+'<br/>Name:'+item.name+'<br/>Base model:'+item.base_model" 
                :export="item.rating"
                :rbg = "item.credit"
                :delete="params.engine == 'lc' ? trans('delete') :null"
                :crop = "computeDownloadText(item.hash, downloadStatus[item.hash])"
                :percent = downloadStatus[item.hash]
                @tsComCallback="handleButton"
                ></TSFloatMask>
            </div>
        </div>
    </div>

<!--:crop = "props.modelIds.includes(item.hash) ? 'downloaded':'download'" -->
<input type="checkbox" id="show_model_images" class="modal-toggle" />
<div class="modal" role="dialog">
    <div class="modal-box w-11/12 max-w-5xl grid grid-cols-7 gap-1">
        <label for="show_model_images" class="btn btn-sm btn-circle btn-ghost absolute right-0 top-2">✕</label>
        <div v-for="(item, index) in modelImages" :key="index">
            <img @click="handleSelectedImage(item)" :src="item.thumb" class=" object-cover cursor-pointer w-full h-56">
        </div>
    </div>
</div>
<!-- preview image -->
<input type="checkbox" id="show_full_image" class="modal-toggle" />
<div class="modal" role="dialog">
    <TSScreenImage :init="activeImage" :modal-id="'show_full_image'" :modalId="'show_full_image'"></TSScreenImage>
</div>

<input type="checkbox" id="confirm_delete_model" class="modal-toggle" />
<div class="modal" role="dialog">
    <div class="modal-box w-11/12 max-w-xl">
        <label for="confirm_delete_model" class="btn btn-sm btn-circle btn-ghost absolute right-0 top-2">✕</label>
        <h3 class="text-lg font-bold">{{trans("Are you sure to delete the model?")}}</h3>
        <p class="py-4">{{trans('This model will be deleted!')}}</p>
        <div class="modal-action flex gap-2">
        <label for="confirm_delete_model" class="btn btn-neutral">{{trans('cancel')}}</label>
        <label for="confirm_delete_model" class="btn btn-error" @click="deleteModel">{{trans('OK')}}</label>
        </div>
    </div>
</div>

<input type="checkbox" id="confirm_download_model" class="modal-toggle" />
<div class="modal" role="dialog">
    <div class="modal-box w-11/12 max-w-xl">
        <label for="confirm_download_model" class="btn btn-sm btn-circle btn-ghost absolute right-0 top-2">✕</label>
        <h3 class="text-lg font-bold">{{trans("Download")}}</h3>
        <p v-if="currentModel && currentModel.download_url != null && currentModel.download_url != ''" class="py-4"><a :href="currentModel.download_url" target="_blank">{{currentModel.download_url}}</a></p>
        <p>{{trans('download_notice')}}</p>
        <p class=" bg-slate-100 p-4 rounded-md">{{props.client_path}}/{{currentModel.type == 'lora'?'loras':'models'}}</p>
        <div class="modal-action flex gap-2">
        <label for="confirm_download_model" class="btn btn-error">{{trans('OK')}}</label>
        </div>
    </div>
</div>



</AppLayout>
</template>
