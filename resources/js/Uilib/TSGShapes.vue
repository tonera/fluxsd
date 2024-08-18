<script setup>
//shapes列表
import TSPaginate from './TSPaginate.vue';
import {aiHttpRequest, } from './network';
import {ref,onMounted ,watch} from 'vue';
import {usePage } from '@inertiajs/vue3';
import { debug} from './TSFunction.js'; 

const page = usePage();
//@init Paper size object @modalId弹窗id
const props = defineProps(['name','init', 'label','modalId']);
const emit = defineEmits(['tsComCallback']);


const isRunning = ref(false);
const imageList = ref({meta:{last_page:1,current_page:1},data:[]});//svglist
const currentSourceIndex = ref(3);//切换图片选择源
const pageNumber = ref(1);//搜索页数
const alertInfo = ref(null);//警告，错误提示
const sourceList = [
    {label:'AI生成',id:0, key:'ai'},
    {label:'素材库',id:1, key:'store'},
    {label:'作品集',id:2, key:'portfolio'},
    {label:'上传',id:3, key:'upload'},
];

onMounted(async () => {
    loadSvgList(currentSourceIndex.value);
});

watch(() => props.init.vh, (newVal, oldVal) => {

});

function tsComCallback(name , value){
    debug("TSGGeneration回调",name,'=', value);
    
}


//选择图片
function handleSelectedImage(item){
    //控制modal开关
    // checkbox.checked = true; // 打开
    // checkbox.checked = false; // 关闭
    var checkbox = document.getElementById(props.modalId);
    // console.log(image);
    debug("选择svg:",item);
    emit('tsComCallback', item.thumb, 'url');
    
    checkbox.checked = false; 
}
//载入模型列表
async function loadSvgList(cPage){
    debug("载入svg 分类,cateId=",currentSourceIndex.value, 'page=', cPage);
    pageNumber.value = cPage;
    // console.log('engine=',engine);
    imageList.value = await aiHttpRequest('get', '/api/shape',{cate_id:currentSourceIndex.value, page:pageNumber.value},null);
}
//切换图片源
async function switchSource(index){
    debug("切换图片源:",index);
    isRunning.value = true;
    currentSourceIndex.value = index;
    alertInfo.value = null;
    pageNumber.value = 1;
    // console.log('currentSourceIndex.value=',currentSourceIndex.value);
    loadSvgList(pageNumber.value);
    isRunning.value = false;
}

</script>

<template>
<div class="">
    <div role="tablist" class="tabs tabs-boxed mt-1 text-xs tabs-md ">
        <a role="tab" 
            class="tab" 
            v-for="(item,index) in props.init" :key="'tag_'+index"
            :class="{'tab-active':currentSourceIndex == item.id}"
            @click="switchSource(item.id)"
        >
            {{item.name}}
        </a>
    </div>
    
    <div v-show="alertInfo != null" role="alert" class="alert alert-warning mt-6">
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
        
        <span>{{alertInfo}}</span>
        <label @click="alertInfo = null" class="btn btn-sm btn-circle btn-ghost hover:bg-gray-500 right-2 top-2">✕</label>
    </div>

    <label :for="props.modalId" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</label>

    <!-- svg list -->
    <div>
        <div class="flex justify-between my-1">
            <div></div>
            <TSPaginate @handleJump="loadSvgList" :total="imageList.meta.last_page" :cpage="imageList.meta.current_page"></TSPaginate>
        </div>
        <!-- 素材库内容gri d -->
        <div class="grid grid-cols-6 gap-1">
            <div v-for="(item , index) in imageList.data" :key="index" class=" bg-white">
                <img @click="handleSelectedImage(item)" :src="item.thumb" class=" w-full cursor-pointer h-36">
            </div>
        </div>
        <div class="flex justify-end">
            <span v-show="isRunning" class="loading loading-ring loading-lg"></span>
            <TSPaginate @handleJump="loadSvgList" :total="imageList.meta.last_page" :cpage="imageList.meta.current_page"></TSPaginate>
        </div>

    </div>



</div>
</template>