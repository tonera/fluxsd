<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import TSSelector from '../../Components/TSSelector.vue';
import TSSelectorModel from '../../Components/TSSelectorModel.vue';
import TSTextArea from '../../Components/TSTextArea.vue';
import TSAspect from '../../Components/TSAspect.vue';
import TSSwitch from '../../Components/TSSwitch.vue';
import TSUploadImage from '../../Components/TSUploadImage.vue';
import TSSlider from '../../Components/TSSlider.vue';
import TSImageQueue from '../../Components/TSImageQueue.vue';
import TSStyle from '../../Components/TSStyle.vue';
import TSRadio from '../../Components/TSRadio.vue';
import ImageNav from './ImageNav.vue';
import TopSnackbar from '../../Components/TopSnackbar.vue';
import OperationButton from '../../Components/OperationButton.vue';
import { trans } from 'laravel-vue-i18n';
import {store} from '../../mock.js';
import {router,usePage } from '@inertiajs/vue3';
import { onMounted, reactive, ref } from 'vue';
import {aiHttpRequest, editImage, showTopSnackbar, getAccessKey, buildSubmitParams,checkSubmitParams, getSrc} from '../../network';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;
const pageVar = usePage();
const props = defineProps(['sdratio','token','omSamplers','lcSamplers','sdSamplers','engines']);
const isAdvance = ref(false);
const isNiji = ref(false);
const hasNiji = ref(false);
const aspectRatio = ref();//aspect 
const engineConfig = ref();
const isRunning = ref(false);
const init_img_path = ref(null);
const history = ref([]);
const work_image = ref({buttonGroups:[], id:null});
const historyPage = ref(1);
const etaTimeNotice = ref('Unknown');
const moveto = ref('');
const samplers = ref([]);
const waitingImageNum = ref(0);//task image waited 
const modelBaseModel = ref(null);
var finishedPages = [];
var taskRunTime = 3000;//default 30s
var modelSizeRatio = 1;//sdxl=1 sd1.5 = 0.5
var task_id = '';


let cacheString = localStorage.getItem('aibox_params');
let initParams = {};
if(cacheString != null){
    // isAdvance.value = true;
    initParams = JSON.parse(cacheString);
    if(initParams['niji'] != null){
        isNiji.value = true;
    }
    initParams['model_hash_id'] = null;
    initParams['lora_hash_id'] = null;
    initParams['image_num'] = initParams['image_num']??1;
    initParams['lora_scale'] = initParams['lora_scale']??0.9;
    // console.log('init from cache: ', initParams );
}else{
    initParams = {
        engine:'lc',
        model_hash_id: null,
        lora_hash_id: null,
        model_name:null,
        lora_name:null,
        prompt: null,
        prompt_en:null,
        negative_prompt: null,
        negative_prompt_en:null,
        // style:null,
        asp_id:0,
        niji:null,
        steps:null,
        cfg_scale:null,
        seed:0,
        face_enhance:false,
        upscale:null,
        width:null,
        height:null,
        weird:null,
        denoising_strength:null,
        cref:null,//role for mj
        sref:null,//style for mj
        sampler_name:null,
        isRandom:true,
        image_num:1,
        lora_scale:0.9,
    };
}

const params = reactive(initParams);

onMounted(() => {
    getHistoryList(historyPage.value);
    samplers.value = getSamplers(params.engine);
    params.sampler_name = samplers.value.length > 0 ? samplers.value[0].val: null;

    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        // wsHost: import.meta.env.VITE_REVERB_HOST,
        wsHost: pageVar.props.reverbHost,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
    });
});

function getSamplers(engine){
    switch(engine){
        case 'lc':
        case 'atz':
            return props.lcSamplers;
        case 'sd':
            return props.sdSamplers;
        default:
            return [];
    }
}

async function getHistoryList(page){
    if(finishedPages.includes(page)){
        console.log(page,'requested this page');
        return;
    }
    finishedPages.push(page);
    let res = await aiHttpRequest('get', '/api/images', {page:page});
    if(res != null && res.code == 1){
        let tmpval =  res.data;
        if(tmpval !== null){
            tmpval.forEach((item)=>{
                history.value.push(item);
            });
        }
        if(tmpval.length > 0){
            historyPage.value = page; 
        }
    }
}

function tsComCallback(name, value){
    console.log("Com callback: name =",name, "value =", value);
    switch(name){
        case 'isAdvance':
            isAdvance.value = value;
        break;
        case 'isNiji':
            isNiji.value = value;
        break;
        case 'engine':
            setEngine(value.engine);
        break;
        case 'style':
            addStylePrompt(value);
        break;
        case 'init_img_path':
            init_img_path.value = value;
            break;
        case 'work_image':
            handleHistoryImageClick(value);
            break;
        case 'model_name':
            params.model_hash_id = value.hash??null;
            store.styleList = JSON.parse(value.tags??'[]');
            params.model_name = value.sd_name;
            if(['sdxl','presdxl'].includes(value.base_model)){
                modelBaseModel.value = 'sdxl';
            }else{
                modelBaseModel.value = '1.5';
            }
            // console.log(value.base_model, modelBaseModel.value);
            break;
        case 'lora_name':
            params.lora_hash_id = value.hash??null;
            params.lora_name = value.sd_name??null;
            break;
        case 'sampler_name':
            params[name] = value.val;
        break;
        case 'asp_id':
            params.asp_id = value;
            let asp = aspectRatio.value[value];
            // console.log('asp=',asp, modelSizeRatio ,value);
            //there are diffient size for sdxl /sd1.5
            params.width = parseInt(asp.width * modelSizeRatio);
            params.height = parseInt(asp.height * modelSizeRatio) ;
            // console.log('params=',params);
        break;
        case 'prompt':
            params[name] = value;
            params.prompt_en = null;
            break;
        case 'negative_prompt':
            params[name] = value;
            params.negative_prompt_en = null;
            break;
        default:
            params[name] = value;
    }
}

function handleHistoryImageClick(item){
    work_image.value = item;
    if(work_image.value.index >= (history.value.length -3) ){
        getHistoryList(historyPage.value+1);
    }
    if(item.task != null){
        let task = item.task;
        Object.entries(params).forEach(function([key, value]) {
            if(key in task){
                params[key] = task[key];
            }
        });
    }
}

function addStylePrompt(prompt){
    let charToFind = prompt;
    let index = params.prompt.indexOf(charToFind);
    
    if (index !== -1) {
        ;
    } else {
        params.prompt = params.prompt +',' + charToFind;
    }
}

function setEngine(engine){
    params.engine = engine;
    params.model_name = null;
    params.lora_name = null;
    params.model_hash_id = null;
    params.lora_hash_id = null;
    samplers.value = getSamplers(params.engine);
    params.sampler_name = samplers.value.length > 0 ? samplers.value[0].val: null;
    switch(engine){
        case 'mj':
            hasNiji.value = true;
            aspectRatio.value = props.sdratio;
            engineConfig.value = store.mjConfig;
            params.image_num = 1;
        break;
        case 'sd':
            params.image_num=1;
        case 'atz':
        case 'lc':
            hasNiji.value = false;
            aspectRatio.value = props.sdratio;
            engineConfig.value = store.sdConfig;
        break;
        default:
            params.image_num = 1;
            hasNiji.value = false;
            aspectRatio.value = props.sdratio;
            engineConfig.value = store.sdConfig;
    }
    params.cfg_scale = engineConfig.value.cfg_scale.def;
    params.steps = engineConfig.value.steps.def;
    params.seed = engineConfig.value.seed.def;
    params.weird = engineConfig.value.weird.def;
    params.denoising_strength = engineConfig.value.denoising_strength.def;
}


setEngine(params.engine);
tsComCallback('asp_id', params.asp_id??0);

async function Summit(){
    window.Echo.leave('private-channel_task.'+task_id);
    waitingImageNum.value = 0;
    if(isRunning.value) return;
    let act = 'MK';
    let checked = checkSubmitParams(act,params);
    if(checked !== true){
        showTopSnackbar(checked, 'error');
        return;
    }
    localStorage.setItem('aibox_params', JSON.stringify(params));
    
    let formdata = buildSubmitParams(params);
    formdata.append('act', act);
    formdata.append('job_type', 'MK');
    //cache
    var jsonObj = {};
    formdata.forEach((value, key) => (jsonObj[key] = value));
    console.log(JSON.stringify(jsonObj));

    if(init_img_path.value != null){
        formdata.append('init_img_path', init_img_path.value);
    }
    
    isRunning.value = true;
    let acckey = props.token;
    let res = await aiHttpRequest('post', '/api/task', formdata, {'Content-Type':'multipart/form-data',Authorization:acckey});
    // console.log(res);
    
    if(res != null && res.code == 1){
        taskRunTime = parseInt(res.execTime) * 100;
        settime('ok');
        // console.log('taskRunTime=',taskRunTime);
        task_id = res.data.task_id;
        params.prompt_en = res.data.prompt_en;
        params.negative_prompt_en = res.data.negative_prompt_en;
        connectTaskWs(res.data.task_id);
        waitingImageNum.value = res.data.image_num;
        //rewrite init_img_path 
        if(res.data != null && res.data.init_img != null && res.data.init_img != ''){
            init_img_path.value = res.data.init_img;
        }
    }else{
        isRunning.value = false;
        showTopSnackbar(res.msg??trans('Failed to submit AI generation task'), 'error');
    }
}

function settime(val){
    if(taskRunTime <= -6000){
        if(isRunning.value == true){
            isRunning.value = false;
            showTopSnackbar(trans('Over time'),'info');
        }
        // console.log('over time 60s');
        etaTimeNotice.value = 'Unknown';
    }else{
        // console.log('et:', taskRunTime,'s');
        taskRunTime--;
        etaTimeNotice.value = (taskRunTime/100)+'秒';
        setTimeout(function(){
            settime(val)
        },10)
    }
}

//image edit btn
async function btnAction(item){
    console.log(item);
    if(isRunning.value == true){
        console.log('Task is running');
        return;
    }
    isRunning.value = true;
    if(item.act == 'EDIT'){  
        router.get(route('tools.cropper', {url:item.img.show_url}));
    }else{
        taskRunTime = 1500;
        settime('ok');
        waitingImageNum.value = 1;
        editImage(item.act, {init_img_path:item.img.show_url, id:item.img.id, engine:params.engine}, callback);
    }
}


function callback(res){
    console.log('ws call back',res.message_type, waitingImageNum.value);
    if(res.message_type == 'standing'){
        history.value.unshift(res);
        work_image.value = res;
        taskRunTime = 3000;
        waitingImageNum.value = waitingImageNum.value - 1;
        if(waitingImageNum.value == 0){
            isRunning.value = false;
        }
    }else if (res.message_type == 'ephemeral'){
        isRunning.value = true;
        taskRunTime = parseInt(res.execTime??'1') * 100;
    }else if (res.message_type == 'failed'){
        isRunning.value = false;
        taskRunTime = 3000;
        showTopSnackbar(res.msg??trans('Failed to submit AI generation task'), 'error');
    }
}

const connectTaskWs = (task_id) => {
    console.log('listen: channel_task =',task_id);
    window.Echo.private('channel_task.'+task_id)
        .listen('TaskMessage', async (e) => {
            callback(e.message);
        });
}


//test
function getInput(){
    isRunning.value = !isRunning.value;
    taskRunTime = parseInt('0') * 100;
    settime('ok');
    connectTaskWs('240620282377527296');
    window.Echo.leave('private-channel_task.240620282377527296');
}
 
</script>

<template>
    <AppLayout title="AI images">
        <template #header>
            <ImageNav :init="props.engines" :def="params.engine" :name="'engine'" @tsComCallback="tsComCallback" ></ImageNav>
        </template>

        <TopSnackbar></TopSnackbar>
        <div class=" px-5 bg-white grid grid-cols-2">

            <div id="left" class="basis-1/2 ">
                <div class="flex gap-2">
                    <TSSelectorModel v-if="['lc','atz'].includes(params.engine)" :name="'model_name'" :label="trans('model')" :engine="params.engine"  :init="'checkpoint'" @tsComCallback="tsComCallback" :def="params.model_name"></TSSelectorModel>
                    <div>
                        <TSSelectorModel v-if="['lc','atz','sd'].includes(params.engine)" :name="'lora_name'" :label="trans('lora')" :engine="params.engine" :init="'lora'" @tsComCallback="tsComCallback" 
                        :base="modelBaseModel"
                        :def="params.lora_name"></TSSelectorModel>
                        <div v-show="params.lora_hash_id!=null && ['lc','atz'].includes(params.engine)" class="flex items-center justify-between gap-2">
                            <input type="range" min="0" max="1" step="0.1" v-model="params.lora_scale" class="range range-xs range-success" />
                            <label class="w-16 whitespace-nowrap text-xs text-right">{{trans('scale')}}:</label>
                            <div class="badge badge-neutral badge-sm w-10">{{params.lora_scale}}</div>
                        </div>
                    </div>
                    

                </div>
                  
                <!-- prompt -->
                <div>
                    <TSTextArea :name="'prompt'" :label="trans('prompt')" @tsComCallback="tsComCallback" :def="params.prompt"></TSTextArea>
                    <!-- style prompt -->
                    <TSStyle v-if="store.styleList.length>0" :name="'style'" :init="store.styleList" @tsComCallback="tsComCallback"></TSStyle>
                </div>
            
                <TSAspect  :name="'asp_id'" :label="'Aspect'" @tsComCallback="tsComCallback" :init="aspectRatio" :def="params.asp_id"></TSAspect>
                
                <!-- niji and advance -->
                <div class="flex justify-between p-2">
                    <TSSwitch v-if="hasNiji" :name="'isNiji'" :label="'Niji?'" @tsComCallback="tsComCallback" :def="isNiji"></TSSwitch>
                    <div v-else></div>
                    <div class="flex items-center gap-2">
                        <div v-if="['lc','atz'].includes(params.engine)" class="flex items-center gap-2 w-60">
                            <div class="text-sm whitespace-nowrap">{{trans('image num')}}:{{params.image_num}}</div>
                            <input type="range" min="1" max="20" value="1" v-model="params.image_num" class="range range-success range-sm" />
                        </div>
                        <TSSwitch :name="'isRandom'" :label="trans('random seed')" @tsComCallback="tsComCallback" :def="params.isRandom"></TSSwitch>
                        <TSSwitch :name="'isAdvance'" :label="trans('advance')" @tsComCallback="tsComCallback" :def="isAdvance"></TSSwitch>
                    </div>
                </div>

                <!-- niji for mj -->
                <div v-show="isNiji && hasNiji" class="flex">
                    <TSRadio :init="store.nijiStyle" :def="params.niji" :name="'niji'"  @tsComCallback="tsComCallback"></TSRadio>
                </div>
                <!-- advance option -->
                <div v-show="isAdvance" class="divider">{{trans('advance')}}</div>
                <div v-show="isAdvance" >
  
                    <TSTextArea :name="'negative_prompt'" :label="trans('neg prompt')" @tsComCallback="tsComCallback" :def="params.negative_prompt"></TSTextArea>
                    <div class="flex gap-x-4 py-4">
                        <div>
                            <TSSlider :name="'steps'" :label="trans('steps')" @tsComCallback="tsComCallback" :min="engineConfig.steps.min.toString" :max="engineConfig.steps.max" :def="params.steps" :step="engineConfig.steps.step"></TSSlider>
                        </div>
                        <div>
                            <TSSlider :name="'cfg_scale'" :label="trans('scale')" @tsComCallback="tsComCallback" :min="engineConfig.cfg_scale.min" :max="engineConfig.cfg_scale.max" :def="params.cfg_scale"></TSSlider>
                        </div>
                        <div>
                            <TSSlider v-show="!params.isRandom" :name="'seed'" :label="trans('seed')" @tsComCallback="tsComCallback" :min="engineConfig.seed.min" :max="engineConfig.seed.max" :def="params.seed"></TSSlider>
                        </div>
                        <div>
                            <TSSlider v-show="params.engine == 'mj'" :name="'weird'" :label="'Weird'" @tsComCallback="tsComCallback" :min="engineConfig.weird.min" :max="engineConfig.weird.max" :def="params.weird"></TSSlider>
                        </div>
                    </div>

                    <!-- face enhance 2x upscale 4x upscale -->
                    <div v-show="['lc','atz'].includes(params.engine)" class="flex justify-between items-center">
                        <TSSwitch :name="'face_enhance'" :label="trans('Face enhance')" @tsComCallback="tsComCallback" :def="params.face_enhance"></TSSwitch>
                        <label class="relative inline-flex items-center cursor-pointer p-2">
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-300 mr-1">{{trans('upscale')}}</span>
                            <TSRadio :init="store.upscaleOptions" :def="params.upscale" :name="'upscale'"  @tsComCallback="tsComCallback"></TSRadio>
                        </label>
                    </div>

                    <!-- sampler -->
                    <TSSelector v-if="params.engine != 'mj'" :name="'sampler_name'" :label="trans('sampler')" :init="samplers" @tsComCallback="tsComCallback" :def="params.sampler_name"></TSSelector>
                    
                    <!-- upload image for img2img -->
                    <div class="flex justify-start py-4 gap-x-4 items-center">
                        <div class=" w-40 h-40">
                            <TSUploadImage :name="'init_img_path'" :desc="trans('upload a picture')" :def="init_img_path" @tsComCallback="tsComCallback" ></TSUploadImage>
                        </div>
                        <div v-show="init_img_path != null" class="flex flex-col">
                            <TSSlider :name="'denoising_strength'" :label="'Denoising'" @tsComCallback="tsComCallback" :min="engineConfig.denoising_strength.min" :max="engineConfig.denoising_strength.max" :def="params.denoising_strength" :step="engineConfig.denoising_strength.step"></TSSlider>
                            <div v-if="params.engine == 'mj' && engineConfig.cref!=undefined">
                                <TSSlider :name="'cref'" :label="trans('cref')" @tsComCallback="tsComCallback" :min="engineConfig.cref.min" :max="engineConfig.cref.max" :def="params.cref" :step="engineConfig.cref.step"></TSSlider>
                                <TSSlider :name="'sref'" :label="trans('sref')" @tsComCallback="tsComCallback" :min="engineConfig.sref.min" :max="engineConfig.sref.max" :def="params.sref" :step="engineConfig.sref.step"></TSSlider>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="flex justify-center p-10">
                    <!-- <TSPrimaryButton :label="trans('Generate')" @click="Summit" :isRunning="isRunning"></TSPrimaryButton>  -->
                    <!-- <TSPrimaryButton :label="'GetCache'" @click="getInput"></TSPrimaryButton>  -->
                    <button  @click="Summit" 
                        type="button"
                        class="text-white bg-gradient-to-r from-green-500 via-green-600 to-green-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 shadow-lg shadow-green-500/50 font-medium rounded-full w-32 text-sm px-5 py-2.5 text-center me-2 mb-2">
                        {{$t('Generate')}}
                    </button>
                </div>
            </div>

            <!-- right -->
            <div id="right" class=" basis-1/2 bg-gray-100 rounded-xl mx-4 ">
                <div class="">
                    <TSImageQueue :name="'work_image'" :init="history" :def="0" :tabstyle="'small'" @tsComCallback="tsComCallback" :moveto="moveto" ></TSImageQueue>
                </div>

                <div class="w-full h-screen">
                    <div class="relative text-sm font-medium cursor-pointer">
                        <div class="relative ">

                            <div class="flex justify-center pt-52">
                                <div class="absolute grid z-20 rounded text-accent-content place-content-center">
                                    <div v-if="isRunning" class="grid justify-center justify-items-center rounded-xl bg-neutral w-52 py-8 bg-opacity-80">
                                        <span class="loading loading-dots loading-lg text-error"></span>
                                        <label class=" text-white flex" >
                                            No.{{waitingImageNum}} {{trans('estimated')}}:<div class=" w-16 text-center">{{etaTimeNotice}}</div>
                                        </label>
                                    </div>
                                </div> 
                            </div>
                            
                            <div class="absolute inset-0">
                                <div v-if="work_image.show_url != undefined" class="">
                                    <div class="flex justify-center"><img onclick="my_modal_3.showModal()" :src="work_image.show_url"/></div>
                                    <div v-show="isRunning" class="flex justify-center py-5"><progress class="progress w-56"></progress></div>
                                    <OperationButton :init="work_image.buttonGroups" :name="'show_image'" :img="work_image" @btnAction="btnAction"></OperationButton>
                                </div>
                                <div v-else class=" text-2xl text-stone-700 text-center z-30 pt-20">{{trans('image preview area')}}</div>
                            </div>

                        </div>

                    </div>

                </div>

            </div>
            <!-- 右区结束 -->

            <!-- You can open the modal using ID.showModal() method -->
            <!-- <button class="btn" onclick="my_modal_3.showModal()">open modal</button> -->
            <dialog id="my_modal_3" class="modal bg-gray-900 bg-opacity-80">
            <div class=" h-max w-max">
                <form method="dialog">
                <button class="btn btn-circle btn-ghost absolute right-2 top-2 text-2xl bg-gray-500 hover:bg-white">✕</button>
                </form>
                <div class="flex items-center gap-x-9 w-screen justify-evenly">
                    <div class=" text-white pb-20">
                        <button @click="moveto = 'pre_'+ work_image.index" class="btn btn-circle btn-ghost  right-2 top-2 text-2xl bg-gray-500 hover:bg-white bg-opacity-55 hover:text-black">
                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"></path>
                            </svg>
                        </button>
                    </div>
                    <img class=" h-screen" :src="work_image.show_url"/>
                    <div class=" text-white pb-20">
                        <button @click="moveto = 'next_'+ work_image.index" class="btn btn-circle btn-ghost  right-2 top-2 text-2xl bg-gray-500 hover:bg-white bg-opacity-55 hover:text-black">
                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
            </div>
            </dialog>

               
        
        </div>
    </AppLayout>
</template>
