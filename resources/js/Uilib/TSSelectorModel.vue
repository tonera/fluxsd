<script setup>
import { onMounted, reactive, ref, watch } from 'vue';
import {aiHttpRequest, showTopSnackbar} from '../network';
import Paginate from './Paginate.vue';
import {trans} from 'laravel-vue-i18n';
const props = defineProps(['name','init','label','def','engine']);
const emit = defineEmits(['tsComCallback']);
const show_more = ref(false);
const cItem = ref( {'name':props.name,'val':props.def??''});
// const topBar = [
//   {label:'Browser models', val:'list'},
//   {label:'Download from URL', val:'url'}
// ];
// const currentBar = ref('list');
const cPage = ref(1);
const models = ref([]);
const total = ref(0);

// SD 1.5         |     1070 |
// | SDXL 1.0       |      137 |
// |                |       21 |
// | Other          |       14 |
// | Pony           |        2 |
// | SD 1.4         |        2 |
// | SDXL Turbo     |        4 |
// | SD 2.1 768     |        2 

const initOptions = [
  'SD 1.5','SDXL 1.0','Other','Pony','SD 1.4','SDXL Turbo','SD 2.1 768','NSFW','Downloadable'
];
const options = reactive([
  'SD 1.5','SDXL 1.0','Other','Pony','SD 1.4','SDXL Turbo','SD 2.1 768'
]);
function setModelsOptions(item){
  let index = options.indexOf(item);
  if (index !== -1) {
    options.splice(index, 1);
  }else{
    options.push(item);
  }
  getModelList(1, props.init );
  console.log(options);
}

getModelList(1,props.init);

watch(() =>props.engine, (e)=>{
  getModelList( 1 , props.init);
  cPage.value = 1;
});
watch(() =>props.def, (e)=>{
  cItem.value.val = props.def;
});

async function getModelList(page , type){
  let res = await aiHttpRequest('get', '/api/models', {page:page, engine:props.engine, type:type, options:JSON.stringify(options)});
  if(res.code == 1){
    models.value = res.data;
    total.value = res.meta.last_page;
  }
  // console.log(res);
}
function handleJump(page){
  cPage.value = page;
  getModelList(page, props.init);
}

//收藏模型
async function handleFavored(item, index){
  //api/models/{model} 
  let favored = item.favored == 1 ? 0 : 1;;
  let res = await aiHttpRequest('put', '/api/models/'+item.id, {favored:favored});
  if(res.code == 1){
    models.value[index].favored = favored;
  }else{
    showTopSnackbar('收藏失败', 'error');
  }
}


function setValue(item){
  //本地引擎只有下载后的模型才可以使用
  if(props.engine == 'lc' && item.is_download != 10){
    showTopSnackbar('必须下载完成后才可以使用这个模型', 'error');
    return;
  }
  // console.log(props.engine, item);
    try{
        emit('tsComCallback', props.name, item);
        show_more.value = false;
        cItem.value = item;
    }catch(error){
        console.log(error);
    }
}
function reset(){
    emit('tsComCallback', props.name, '');
    show_more.value = true;
    cItem.value =  {'name':props.name,'val':''};
}


</script>

<template>
<div class="join py-4">
    <label class="font-semibold grid join-item btn items-center justify-items-center text-center">{{props.label}} </label>
    <div class="relative join-item">
      <div class="h-12 bg-white flex border border-gray-200 rounded items-center">
        <img v-show="cItem.cover_url!=undefined" class="object-cover w-12 h-12 pr-1" :src="cItem.cover_url"/>
        <input @click="show_more = !show_more" :value="cItem.val" name="select" id="select" class="peer block min-h-[auto] w-full rounded border-0 bg-transparent px-3 py-[0.32rem] leading-[1.6] outline-none transition-all duration-200 ease-linear focus:placeholder:opacity-100 peer-focus:text-primary data-[twe-input-state-active]:placeholder:opacity-100 motion-reduce:transition-none [&:not([data-twe-input-placeholder-active])]:placeholder:opacity-0" checked/>
        
        <button 
            @click="reset"
            class="cursor-pointer outline-none focus:outline-none transition-all text-gray-300 hover:text-gray-600">
          <svg class="w-4 h-4 mx-2 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
        <label :for="'show_more'+props.name" 
          class="cursor-pointer outline-none focus:outline-none border-l border-gray-200 transition-all text-gray-300 hover:text-gray-600">
          <svg class="w-4 h-4 mx-2 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="18 15 12 9 6 15"></polyline>
          </svg>
        </label>
      </div>

      <!-- 显示下拉页面 -->
      <input type="checkbox" name="show_morew" :id="'show_more'+props.name" class="hidden peer" :checked="show_more" />
      <div 
        class="absolute size-[48rem] rounded shadow bg-white overflow-hidden hidden peer-checked:flex flex-col mt-1 border border-gray-200 z-20">
        <!-- 同步模型 -->
        <div class="flex justify-end pt-2 pr-4 gap-x-2">
          <button class="btn btn-outline btn-sm">分享模型</button>
          <button class="btn btn-outline btn-sm">同步模型</button>
          <button class="btn btn-outline btn-sm" @click="show_more = false">关闭</button>
        </div>
        <!-- 选项内容 -->
        <div class="p-2 flex justify-between h-16">
          <Paginate :total="total" :cpage="cPage" @handleJump="handleJump"></Paginate>

          <ul class="menu lg:menu-horizontal z-30 w-40 justify-end">
            <li>
              <details close>
                <summary class="w-40 justify-end">筛选</summary>
                <ul>
                  <li v-for="(item, index) in initOptions" :key="'option_'+index">
                    <a @click="setModelsOptions(item)">
                      <svg v-if="options.includes(item)" class=" w-5 h-5 fill-[#0eaf19]" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg">
                        <path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"></path>
                      </svg>
                      <div v-else class="w-5"></div>
                      {{item}}
                    </a>
                  </li>
                </ul>
              </details>
            </li>
          </ul>

        </div>
        <div class="overscroll-auto overflow-auto">
          <div class="w-full grid grid-cols-3 lg:grid-cols-4 gap-2 px-2 " >
            <div 
                v-for="(item, index) in models" 
                :key="'pl_'+index"
                class="m-4 relative text-sm font-medium bg-gray-200 hover:bg-gray-300 cursor-pointer ">
                <div @click="setValue(item)" class="relative h-60">
                  <div class="absolute z-20 inset-0 bg-gray-900 opacity-0 transition-opacity duration-300 hover:opacity-80 flex items-center justify-center text-white whitespace-pre-wrap overflow-wrap-break-word break-all p-2">
                    {{item.sd_name}}
                  </div>
                  <div class="absolute inset-0">
                      <img class="object-cover h-full w-full" :src="item.cover_url"/>
                  </div>
                </div>

                <div class="p-2 flex justify-center ">
                  <button v-if="item.is_download == 10"
                    type="button"
                    class="btn btn-sm btn-outline btn-success">
                    已下载
                  </button>
                  <button v-else-if="item.is_download == 7"
                    type="button"
                    class="btn btn-sm btn-warning">
                    重试下载
                  </button>
                  <button v-else-if="item.is_download == 5"
                    type="button"
                    class="btn btn-sm">
                    <span class="loading loading-spinner"></span>
                      正在下载
                  </button>
                  <button v-else-if="item.is_download == 3"
                    type="button"
                    class="btn btn-sm btn-outline">
                    下载
                  </button>
                </div>
                <button @click="handleFavored(item,index)" type="button" class="absolute inline-flex items-center justify-center w-7 h-7 z-30 text-xs text-white bg-white border-2 border-white rounded-full -top-2 -right-2 dark:border-gray-900">
                    <svg v-if="item.favored == 1" class="w-[25px] h-[25px] fill-[#e6492d]" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                    <path d="M47.6 300.4L228.3 469.1c7.5 7 17.4 10.9 27.7 10.9s20.2-3.9 27.7-10.9L464.4 300.4c30.4-28.3 47.6-68 47.6-109.5v-5.8c0-69.9-50.5-129.5-119.4-141C347 36.5 300.6 51.4 268 84L256 96 244 84c-32.6-32.6-79-47.5-124.6-39.9C50.5 55.6 0 115.2 0 185.1v5.8c0 41.5 17.2 81.2 47.6 109.5z"></path>
                    </svg>
                    <svg v-else class="w-[25px] h-[25px] fill-[#e6492d]" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                    <path d="M225.8 468.2l-2.5-2.3L48.1 303.2C17.4 274.7 0 234.7 0 192.8v-3.3c0-70.4 50-130.8 119.2-144C158.6 37.9 198.9 47 231 69.6c9 6.4 17.4 13.8 25 22.3c4.2-4.8 8.7-9.2 13.5-13.3c3.7-3.2 7.5-6.2 11.5-9c0 0 0 0 0 0C313.1 47 353.4 37.9 392.8 45.4C462 58.6 512 119.1 512 189.5v3.3c0 41.9-17.4 81.9-48.1 110.4L288.7 465.9l-2.5 2.3c-8.2 7.6-19 11.9-30.2 11.9s-22-4.2-30.2-11.9zM239.1 145c-.4-.3-.7-.7-1-1.1l-17.8-20c0 0-.1-.1-.1-.1c0 0 0 0 0 0c-23.1-25.9-58-37.7-92-31.2C81.6 101.5 48 142.1 48 189.5v3.3c0 28.5 11.9 55.8 32.8 75.2L256 430.7 431.2 268c20.9-19.4 32.8-46.7 32.8-75.2v-3.3c0-47.3-33.6-88-80.1-96.9c-34-6.5-69 5.4-92 31.2c0 0 0 0-.1 .1s0 0-.1 .1l-17.8 20c-.3 .4-.7 .7-1 1.1c-4.5 4.5-10.6 7-16.9 7s-12.4-2.5-16.9-7z"></path>
                    </svg>
                </button>
            </div>
          </div>
        </div>

      </div>

    </div>
  </div>
</template>