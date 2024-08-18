<script setup>
//生成图片UI
import TSRangeInput from './TSRangeInput.vue';
import TSAspect from './TSAspect.vue';
import TSImageQueue from './TSImageQueue.vue';
import TSPaginate from './TSPaginate.vue';
import TSFloatMask from './TSFloatMask.vue';
import TSRunning from './TSRunning.vue';
import TSGExport from './TSGExport.vue';

//带蒙板操作的图片展示
import TSCroper from './TSCroper.vue';
import {aiHttpRequest, connectWs, showTopSnackbar, getAccessKey, uploadInitImage, getSrc} from './network';
import {ref,onMounted ,watch} from 'vue';
import {usePage,router } from '@inertiajs/vue3';
import {sdAspList} from './TSConstant.js'; 
import {oTask, oImage} from './TSState.js'; 
import {buildFormData, convertToImageFromMessage, computePixel, showModalWindow,closeModalWindow,debug} from './TSFunction.js'; 
import {trans} from 'laravel-vue-i18n';
const page = usePage();
//@init Paper size object @modalId弹窗id
const props = defineProps(['name','init', 'label','modalId','mode','token']);
const emit = defineEmits(['tsComCallback','imageMaskButton']);

const styleList = ref([]);
const styleIndex = ref(0);//选择模型的idx
const currentEngine = ref("atz");
const engineButtonActive = "text-white bg-green-700";
const engineButtonNative = "text-slate-500 bg-white";
const showAdvance = ref(false);
const isRunning = ref(false);
const etaTimeNotice = ref('Unknown');
const imageList = ref([]);//收到的图片列表
const currentSourceIndex = ref(0);//切换图片选择源
const pageNumber = ref(1);//搜索页数
const keyword = ref('');//搜索关键词
const storeList = ref({meta:{last_page:1,current_page:1},data:[]});//图库搜索结果
const portfolioList = ref({meta:{last_page:1,current_page:1},data:[]});//作品集
const fileBase64 = ref(null);//上传图片的base64内容
const alertInfo = ref(null);//警告，错误提示
const initImagesList = ref({meta:{last_page:1,current_page:1},data:[]});//用户上传的图片列表
const activeImage = ref(null);//用户当前选择操作的图片
const runningText = ref('');
const runBatchNumber = ref(0);//每次提交生成图片的数量-for:控制running图标显示.mj5张，atz按设定数量
const sourceList = [
    {label:trans('Generation'),id:0, key:'ai'},
    {label:trans('Materials'),id:1, key:'store'},
    {label:trans('My Images'),id:2, key:'portfolio'},
    {label:trans('Uploaded Images'),id:3, key:'upload'},
];
var taskRunTime = 3000;//任务运行定时器 默认30秒
var mode = props.mode??'small';//大模式下图片显示更大

// console.log(page.props.auth.user);

onMounted(async () => {
    await loadModels(currentEngine.value);
    tsComCallback('aspect',0);
    //载入task缓存 cache_aiposter_task
    let json = localStorage.getItem('cache_aiposter_task');
    let jsonObject = JSON.parse(json); 
    if(jsonObject != null){
        // console.log('jsonObject=',jsonObject);
        Object.keys(jsonObject).forEach((v,k)=>{
            // console.log('k=',k, v);
            oTask[v] = jsonObject[v];
        });
        // console.log('oTask=',oTask);
    }
    //计算默认模型idx
    styleList.value.forEach((v,k)=>{
        if(v.hash_id == oTask.model_hash_id){
            styleIndex.value = k;
        }
        // console.log('kv=',k, v);
    });

    console.log("styleIndex.value",styleIndex.value);
    
});

watch(() => props.init.vh, (newVal, oldVal) => {
    // console.log('props.init=',props.init);
    debug("Watch:画布纵横发生变化",props.init.vh);
    let wh = computePixel(props.init);
});


async function handleCreate(){
    if(page.props.auth.user == null){
        // debug("TSGGeneration开始AI生成图片");
        showTopSnackbar(trans('Please Log in First'),'info');
        alertInfo.value = '此操作需要先登录';
        return;
    }
    //每次提交需要生成的图片数量-收到一张就减一，减到0为止
    runBatchNumber.value = oTask.image_num;
    if(currentEngine.value == 'mj'){
        runBatchNumber.value = 5;
    }
    debug("TSGGeneration开始AI生成图片 ");
    // console.log(oTask);
    // oImage.thumb = 'http://local.aiposter.cc/images/Mix-pose02.png';
    // oImage.show_url = 'http://local.aiposter.cc/images/Mix-pose02.png';
    // convertToImageFromMessage(oImage);
    // // imageList.value.push(oImage);
    // imageList.value.unshift(oImage);
    // console.log(oImage);
    // return;
    
    oTask.engine = currentEngine.value;
    let formdata = buildFormData(oTask);
    
    var jsonObj = {};
    isRunning.value = true;
    formdata.forEach((value, key) => (jsonObj[key] = value));
    //缓存
    localStorage.setItem('cache_aiposter_task', JSON.stringify(jsonObj));
    console.log(jsonObj);
    // return;

    // //todo 可从本地取，因为db是通的
    let acckey = await getAccessKey('/api/accesskey');
    // connectWs(acckey, callback, 'ws://192.168.0.105:9600','123456');
    // console.log('acckey=', props.token);
    // return;
    let apiPath = oTask.engine == 'atz'?'/v1/tsmain':'/v1/tasks';
    debug("生成图片提交参数:");
    console.log(jsonObj);
    // return;
    let res = await aiHttpRequest('post', page.props.apihost+apiPath, formdata, {'Content-Type':'multipart/form-data',Authorization:acckey});
    debug("返回值:",res);
    // console.log('oTask=',oTask);
    // return;
    if(res != null && res.code == 1){
        oTask.task_id = res.data.task_id;
        taskRunTime = parseInt(res.execTime) * 100;
        settime('ok');
        connectWs(acckey, callback, res.wsHost,res.data.task_id);
    }else{
        isRunning.value = false;
        // console.log(res.msg);
        alertInfo.value = res.msg??trans('Failed to submit AI generation task');
    }
    
}
function callback(res){
//   console.log('ws call back',res.message_type, res);
  debug("WS服务器返回:",res.message_type);
  taskRunTime = 3000;
//   console.log('oTask=',oTask);
//   console.log('currentEngine=',currentEngine.value);
  if(res.message_type == 'standing'){
    convertToImageFromMessage(res);
    saveImages(oImage)
    // console.log(" standing 收到图片",oImage);
    isRunning.value = false;
  }else if (res.message_type == 'ephemeral'){
    convertToImageFromMessage(res);
    saveImages(oImage);
    // console.log(" ephemeral 收到图片",oImage);
  }else if (res.message_type == 'failed'){
    isRunning.value = false;
  }
}

function tsComCallback(name , value){
    debug("TSGGeneration回调",name,'=', value);
    // console.log(value);
    //如果prompt变动，prompt_en马上清空
    if(name == 'prompt'){
        oTask.prompt_en = '';
    }
    if(name == 'negative_prompt'){
        oTask.negative_prompt_en = '';
    }
    // emit('tsComCallback', name, value);
    if(name == 'model'){
        oTask.model_hash_id = value.hash_id;
        oTask.short_name = value.short_name;
    }else if (name == 'aspect'){
        let aspect = sdAspList[value];
        oTask.width = aspect.width;
        oTask.height = aspect.height;
    }else if (name == 'width'){
        oTask.width = parseInt(value);
    }else if (name == 'height'){
        oTask.height = parseInt(value);
    }else{
        oTask[name] = value;
    }
}
//倒计时器
function settime(val){
    if(taskRunTime <= -6000){
        if(isRunning.value == true){
            isRunning.value = false;
            showTopSnackbar(trans('Over time'),'info');
        }
        // console.log('额外60秒倒计时结束');
        debug("额外60秒倒计时结束");
        etaTimeNotice.value = 'Unknown';
    }else{
        // console.log('剩余', taskRunTime,'s');
        taskRunTime--;
        etaTimeNotice.value = (taskRunTime/100)+'秒';
        setTimeout(function(){
            settime(val)
        },10)
    }
}

//从收到的消息中提取完整图片的对象存入 imageList
function saveImages(image){
    if(image.show_url == null){
        return;
    }
    let newImg = {...image};    
    let imageSrc = [];
    for (let element of imageList.value) {
        imageSrc.push(element.show_url);
    }
    // console.log('imageSrc=',imageSrc);
    if(!imageSrc.includes(newImg.show_url)){
        // console.log('新图片', newImg.show_url);
        imageList.value.push(newImg);
        runBatchNumber.value--;
        // imageList.value.unshift(newImg);
    }else{
        // console.log('旧图片', newImg.show_url);
    }
}
//选择图片
function handleSelectedImage(image){
    // console.log("client image", image);
    //控制modal开关
    // checkbox.checked = true; // 打开
    // checkbox.checked = false; // 关闭
    
    // console.log(image);
    debug("选择图片:",image);
    emit('tsComCallback', page.props.cdnurl+image.uri, 'url');
    // emit('tsComCallback', 'http://aiposter.local/images/Mix-pose02.png', 'url');
    if(props.modalId != undefined){
        var checkbox = document.getElementById(props.modalId);
        checkbox.checked = false; 
    }
}
//载入模型列表
async function loadModels(engine){
    // console.log('engine=',engine);
    debug("载入模型信息,engine=",engine);
    currentEngine.value = engine;
    let resStyle = await aiHttpRequest('get', page.props.fehost+'/v2/models/search',{engine:currentEngine.value,type:'all'},null);
    styleList.value = resStyle.data;
}
function searchImage(){
    keyword.value = document.getElementById('keyword').value;
    pageNumber.value = 1;
    loadImageStore(pageNumber.value);
    // console.log("searching...", keyword, keyword.length);
    // console.log(document.getElementById('keyword').value);
}
//载入素材库图片
async function loadImageStore(cPage, isInit=false){
    debug("搜索素材库图片,keyword=",keyword.value);
    if(cPage == undefined){
        cPage = pageNumber.value ;
    }
    // console.log('载入素材库图片');
    
    isRunning.value = true;
    pageNumber.value = cPage;
    // let acckey = await getAccessKey('/api/accesskey');
    let acckey = '';
    // console.log("searching...",keyword.value, pageNumber.value);
    storeList.value = await aiHttpRequest('get', page.props.fehost+'/api/image-search', {page:pageNumber.value, limit:30,keyword:keyword.value,client:'aiposter'},{'Content-Type':'multipart/form-data',Authorization:acckey});
    isRunning.value = false;
}
async function loadPortfolio(cPage, isInit=false){
    debug("载入作品集");
    if(cPage == undefined){
        cPage = pageNumber.value ;
    }
    // console.log('载入作品集');
    
    pageNumber.value = cPage;
    let acckey = await getAccessKey('/api/accesskey');
    let res = await aiHttpRequest('get', page.props.apihost+'/v1/images', {page:pageNumber.value,keyword:keyword.value},{'Content-Type':'multipart/form-data',Authorization:acckey});
    if(res.code == 1){
        portfolioList.value = res;
        // console.log(portfolioList.value);
    }else{
        alertInfo.value = res.msg??'获取作品集失败';
        // console.log(typeof(res), res.code,res.msg);
    }
}
//载入用户上传的图片列表
async function loadInitImages(cPage){
    // console.log('载入用户上传的图片列表');
    if(cPage == undefined){
        cPage = pageNumber.value ;
    }
    pageNumber.value = cPage;
    let acckey = await getAccessKey('/api/accesskey');
    let res = await aiHttpRequest('get', page.props.apihost+'/v1/initimages', {page:pageNumber.value},{'Content-Type':'multipart/form-data',Authorization:acckey});
    if(res.code == 1){
        initImagesList.value = res;
        // console.log(initImagesList.value);
    }else{
        alertInfo.value = res.msg??'获取上传图片列表失败';
        // console.log(typeof(res), res.code,res.msg);
    }
}
//切换图片源
async function switchSource(index){
    debug("切换图片源:",currentSourceIndex.value);
    isRunning.value = true;
    currentSourceIndex.value = index;
    alertInfo.value = null;
    keyword.value = '';
    pageNumber.value = 1;
    // console.log('currentSourceIndex.value=',currentSourceIndex.value);
    if(currentSourceIndex.value == 1){
        loadImageStore(pageNumber.value);//载入素材库 todo
    }else if (currentSourceIndex.value == 2){
        loadPortfolio(pageNumber.value);//载入作品集 todo
    }else if (currentSourceIndex.value == 3){
        loadInitImages(pageNumber.value);
    }
    isRunning.value = false;
}
async function uploadFile(event){
    isRunning.value = true;
    //在队列插入一个正在上传的loading图标
    initImagesList.value.data.unshift({show_url: '/images/loading-3.gif'});
  
    console.log('上传文件',event);
    debug("上传文件:event=",event);
    // return;
    let file = event.target.files[0];
    // var filePath = URL.createObjectURL(image);
    getSrc(file).then((res) => {
        fileBase64.value = res;
        // return res;
    }).catch((err) => {
        console.log(err);
        return;
    });
    let acckey = await getAccessKey('/api/accesskey');
    //上传文件获得url
    let upRes = await uploadInitImage( page.props.apihost+'/v1/images',file, 0 ,acckey);
    debug("上传文件结果:",upRes);
    if(upRes != null && upRes.init_img_path != null) {
        initImagesList.value.data[0] = upRes;
    }
    isRunning.value = false;
    // console.log(upRes);
}

function imageMaskButton(btn, value){
    
    // return;
    debug("点击图片浮层按钮", btn);
    console.log(value);
    if(btn == 'crop'){
        activeImage.value = page.props.cdnurl+value?.uri
        console.log(activeImage.value);
        showModalWindow('materia_cropper');
    }else if (btn == 'rbg'){
        activeImage.value = page.props.cdnurl+value?.uri
        removeBG(activeImage.value);
    }else if (btn == 'export'){
        activeImage.value = page.props.cdnurl+value?.uri
        showModalWindow('materia_export');
    }
    
    // emit('imageMaskButton', btn, value);
}
//裁剪对象
async function cropObject(cropData){
    runningText.value = trans('Cropping');
    showModalWindow('running_page');
    //上传文件获得url
    cropData.url = activeImage.value;
    // console.log('clip!', cropData);
    // return;
    let acckey = await getAccessKey('/api/accesskey');
    let res = await aiHttpRequest('post', page.props.apihost+'/v1/initimages', cropData,{'Content-Type':'multipart/form-data',Authorization:acckey});
    
    runningText.value = '';
    closeModalWindow('running_page');
    
    console.log(res);
    var checkbox = document.getElementById('materia_cropper');
    checkbox.checked = false; 

    if(res.code == 1){
        showTopSnackbar(trans('Done'), 'success');
        switchSource(3);//跳转到结果页
    }else{
        showTopSnackbar(res.msg??trans('Crop failed'),'error');
    }
}

//移除背景
async function removeBG(url){
    runningText.value = trans('Removing Background');//正在移除背景
    showModalWindow('running_page');
    let acckey = await getAccessKey('/api/accesskey');
    isRunning.value = true;
    let res = await aiHttpRequest('post', page.props.apihost+'/v1/tsmain', {act:'RBG',init_img_path:url}, {'Content-Type':'multipart/form-data',Authorization:acckey});
    if(res != null && res.code == 1){
        connectWs(acckey, callbackRemoveBG, res.wsHost,res.data.task_id);
    }else{
        runningText.value = '';
        closeModalWindow('running_page');
        console.log(res.msg);
        showTopSnackbar(res.msg??trans('Call the api is failed'), 'error');
    }
}
//移除背景回调
function callbackRemoveBG(message){
    console.log("收到去背景回调", message);
    if(message.message_type == 'standing'){
        runningText.value = '';
        closeModalWindow('running_page');

        switchSource(2);//跳转到结果页
        showTopSnackbar( trans('Done'), 'success');
    }else if(message.message_type == 'failed'){
        runningText.value = '';
        closeModalWindow('running_page');

        showTopSnackbar(message.msg?? trans('Remove background failed'),'error');
    }else{
        runningText.value = message.progress_label??'Removing...';
        console.log("Running...", message);
    }
}

</script>

<template>
<div class="">
    <div role="tablist" class="tabs tabs-boxed mt-1 text-xs tabs-md" :class="props.mode == 'big' ? 'w-2/3':'w-2/3' ">
        <a role="tab" 
            class="tab" 
            v-for="(item,index) in sourceList" :key="'tag_'+index"
            :class="{'tab-active':currentSourceIndex == index}"
            @click="switchSource(index)"
        >
            {{item.label}}
        </a>
    </div>
    
    <div v-show="alertInfo != null" role="alert" class="alert alert-warning mt-6">
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
        
        <span>{{alertInfo}}</span>
        <label @click="alertInfo = null" class="btn btn-sm btn-circle btn-ghost hover:bg-gray-500 right-2 top-2">✕</label>
    </div>

    <label v-if="props.mode == 'small'" :for="props.modalId" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</label>
    <!-- ai生成 -->
    <div v-if="currentSourceIndex == 0" class="flex flex-row w-full">
        <!-- 左侧主区域 -->
        <div class=" w-2/3 py-4">
            <div class="w-full">
                <textarea :placeholder="trans('Prompt')" class="textarea textarea-bordered textarea-md w-full" @input="tsComCallback('prompt', $event.target.value)" v-model="oTask.prompt"></textarea>
            </div>
            <div class="flex justify-between gap-2 pt-2">
                <!-- <div class=" flex gap-2">
                    <TSInput :name="'width'" :init="oTask.width " :label="trans('Width')" :sclass="'w-6'" @tsComCallback="tsComCallback"></TSInput>
                    <TSInput :name="'height'" :init="oTask.height " :label="trans('Height')" :sclass="'w-6'" @tsComCallback="tsComCallback"></TSInput>
                </div> -->
                <div>{{oTask.short_name}}</div>

                <div class="flex gap-2">
                    <button class="btn btn-sm rounded-full text-xs" :class="currentEngine == 'atz'?engineButtonActive:engineButtonNative" @click="loadModels('atz')">
                        {{trans('ATZ engine')}}
                        <svg width="16" height="16" fill="currentColor" class="bi bi-0-circle-fill" viewBox="0 0 640 512" xmlns="http://www.w3.org/2000/svg">
                        <path d="M318.4 16l-161 480h77.5l25.4-81.4h119.5L405 496h77.5L318.4 16zm-40.3 341.9l41.2-130.4h1.5l40.9 130.4h-83.6zM640 405l-10-31.4L462.1 358l19.4 56.5L640 405zm-462.1-47L10 373.7 0 405l158.5 9.4 19.4-56.4z"></path>
                        </svg>
                    </button>
                    <button class="btn btn-sm rounded-full text-xs" :class="currentEngine == 'mj'?engineButtonActive:engineButtonNative" @click="loadModels('mj')">
                        {{trans('MJ engine')}}
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 576 512"><path d="M256 16c0-7 4.5-13.2 11.2-15.3s13.9 .4 17.9 6.1l224 320c3.4 4.9 3.8 11.3 1.1 16.6s-8.2 8.6-14.2 8.6H272c-8.8 0-16-7.2-16-16V16zM212.1 96.5c7 1.9 11.9 8.2 11.9 15.5V336c0 8.8-7.2 16-16 16H80c-5.7 0-11-3-13.8-8s-2.9-11-.1-16l128-224c3.6-6.3 11-9.4 18-7.5zM5.7 404.3C2.8 394.1 10.5 384 21.1 384H554.9c10.6 0 18.3 10.1 15.4 20.3l-4 14.3C550.7 473.9 500.4 512 443 512H133C75.6 512 25.3 473.9 9.7 418.7l-4-14.3z"/></svg>
                    </button>
                </div>
            </div>
            <div class="">
                <TSAspect :name="'aspect'" :label="'Aspect'" @tsComCallback="tsComCallback" :init="sdAspList" :size="25" def="0"></TSAspect>
            </div>
            <div class="">
                <TSImageQueue :name="'model'" :init="styleList" :def="styleIndex" :tabstyle="'small'" @tsComCallback="tsComCallback" :moveto="''" :direction="'horizontal'" ></TSImageQueue> 
            </div>
            <div v-show="currentEngine == 'atz'" class="flex w-full justify-end ">
                <div class="flex gap-2">
                    <label class="text-sm">{{trans('Image')}}</label>
                    <div>
                        <input type="range" min="1" max="4" v-model="oTask.image_num" class="range range-xs" step="1" />
                        <div class=" flex justify-between text-xs px-2">
                            <span>1</span>
                            <span>2</span>
                            <span>3</span>
                            <span>4</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="">
                <div class="divider divider-end font-normal text-sm cursor-pointer" @click="showAdvance = !showAdvance">{{trans('Advance options')}}</div>
                <div v-show="showAdvance">
                    <div class="gap-4 py-2 flex">
                        <TSRangeInput :name="'steps'" :init="oTask.steps" :label="'Steps'" :max="50" :min="10" :step="1" @tsComCallback="tsComCallback"></TSRangeInput>
                        <TSRangeInput :name="'seed'" :init="oTask.seed" :label="'Seed'" :max="4294967295" :min="0" :step="1" @tsComCallback="tsComCallback"></TSRangeInput>
                        <TSRangeInput :name="'cfg_scale'" :init="oTask.cfg_scale" :label="'Scale'" :max="100" :min="0" :step="1" @tsComCallback="tsComCallback"></TSRangeInput>
                    </div>
                    <textarea :placeholder="trans('Negative Prompt')" class="textarea textarea-bordered textarea-sm w-full" @input="tsComCallback('negative_prompt', $event.target.value)" v-model="oTask.negative_prompt"></textarea>
                </div>
            </div>
        </div>
        <!-- 左侧主区域 end -->
        <div class="divider lg:divider-horizontal"></div> 
        <div class="w-1/4">
            <div class="modal-action ">
                <div class="w-full justify-center flex">
                    
                    <label v-if="isRunning" class="btn btn-primary btn-sm rounded-full w-36">
                        <span class="loading loading-infinity loading-md text-warning"></span>
                        {{etaTimeNotice}}
                    </label>
                    <label v-else class="btn btn-primary btn-sm rounded-full w-36"  @click="handleCreate">
                        Create!
                    </label>
                    
                </div>
            </div>
            <!-- 缩略图 -->
            <div class="flex flex-wrap-reverse gap-2 pt-4">
                
                <div class="flex justify-center" v-for="(item, index) in imageList" :key="'thumb_'+index">
                    <img
                        @click="handleSelectedImage(item)" 
                        class="rounded-xl w-24 h-24 object-cover hover:border-2 hover:border-gray-400 cursor-pointer"  
                        :src="item.thumb"
                    >
                </div>
                <div v-show="isRunning || runBatchNumber > 0" class="h-28 w-24 flex justify-center items-center">
                    <span class="loading loading-ring loading-lg"></span>
                </div>
                
            </div>
        </div>
    </div>
    <!-- 素材库搜索 todo image list -->
    <div v-else-if="currentSourceIndex == 1">
        <div class="flex justify-between my-1">
            <label class="input input-bordered flex items-center gap-2">
                
            <input type="text" id="keyword" class="grow border-none" placeholder="Search" @change="searchImage()" />
            <svg @click="searchImage()" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70"><path fill-rule="evenodd" d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z" clip-rule="evenodd" /></svg>
            <span v-if="isRunning" class="loading loading-spinner loading-md"></span>
            </label>
            <TSPaginate @handleJump="loadImageStore" :total="storeList.meta.last_page" :cpage="storeList.meta.current_page"></TSPaginate>
        </div>
        <!-- 素材库内容gri d -->
        <div class="grid grid-cols-6 gap-1">
            <div v-for="(item , index) in storeList.data" :key="index" class="tooltip tooltip-info" :data-tip=" item.width + 'x' + item.height">
                <TSFloatMask :init="item" :mode="props.mode" @handleSelectedImage="handleSelectedImage" @tsComCallback="imageMaskButton" :rbg="trans('Remove BG')" :crop="trans('Crop')"></TSFloatMask>
            </div>
        </div>
        <div class="flex justify-end">
            <span v-show="isRunning" class="loading loading-ring loading-lg"></span>
            <TSPaginate @handleJump="loadImageStore" :total="storeList.meta.last_page" :cpage="storeList.meta.current_page"></TSPaginate>
        </div>

    </div>
    <!-- 作品集 -->
    <div v-else-if="currentSourceIndex == 2">
        <div class="flex justify-end my-1">
            <TSPaginate @handleJump="loadPortfolio" :total="portfolioList.meta.last_page" :cpage="portfolioList.meta.current_page"></TSPaginate>
        </div>
        <!-- 作品集内容grid -->
        <div class="grid grid-cols-6 gap-1">
            <div v-for="(item , index) in portfolioList.data" :key="index" class="tooltip tooltip-info" :data-tip="item.task!=null ? item.task.width + 'x' + item.task.height : ''">
                <TSFloatMask :init="item" :mode="props.mode" @handleSelectedImage="handleSelectedImage" @tsComCallback="imageMaskButton" :rbg="trans('Remove BG')" :crop="trans('Crop')" :export="trans('Export')" :prompt="'Prompt'"></TSFloatMask>

            </div>
        </div>
        <div class="flex justify-end">
            <span v-show="isRunning" class="loading loading-ring loading-lg"></span>
            <TSPaginate @handleJump="loadPortfolio" :total="portfolioList.meta.last_page" :cpage="portfolioList.meta.current_page"></TSPaginate>
        </div>
    </div>
    <!-- 上传 -->
    <div v-else-if="currentSourceIndex == 3" class=" ">
        <div class="flex justify-end my-1">
            <span v-show="isRunning" class="loading loading-ring loading-lg"></span>
            <input :disabled="isRunning" type="file" label="label" @input="uploadFile" class="file-input cursor-pointer file-input-md file-input-bordered w-full max-w-xs" />
        </div>
        <div class="grid grid-cols-6 gap-1">
            <div v-for="(item , index) in initImagesList.data" :key="index" class="">
                <TSFloatMask :init="item" :mode="props.mode" @handleSelectedImage="handleSelectedImage" @tsComCallback="imageMaskButton" :rbg="trans('Remove BG')" :crop="trans('Crop')" :export="trans('Export')"></TSFloatMask>
            </div>
        </div>
    </div>

</div>

<input type="checkbox" id="materia_cropper" class="modal-toggle" />
<div class="modal" role="dialog">
    <div class="modal-box w-11/12 max-w-4xl h-[700px]">
        <TSCroper :init="activeImage" @tsComCallback="cropObject" :modal-id="'materia_cropper'"></TSCroper>
    </div>
</div>

<input type="checkbox" id="running_page" class="modal-toggle" />
<div class="modal" role="dialog">
    <TSRunning :init="runningText" :modal-id="'running_page'"></TSRunning>
</div>

<input type="checkbox" id="materia_export" class="modal-toggle" />
<div class="modal" role="dialog">
    <div class="modal-box w-11/12 max-w-4xl h-[700px]">
        <TSGExport :init="activeImage" @tsComCallback="cropObject" :modal-id="'materia_export'"></TSGExport>
    </div>
</div>

</template>