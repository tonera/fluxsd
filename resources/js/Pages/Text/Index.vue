<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { aiHttpRequest, showTopSnackbar } from "@/network";
import TSSwitch from '../../Components/TSSwitch.vue';
import {trans} from 'laravel-vue-i18n';
import { onMounted, onUnmounted,reactive, ref } from "vue";
import TSSlider from '../../Uilib/TSSlider.vue';
import MarkdownIt from "markdown-it";
import hljs from 'highlight.js'
import 'highlight.js/styles/monokai.css';
import 'github-markdown-css/github-markdown.css';

// const params = reactive({engine:'atz',page:1});
const markdown = new MarkdownIt({
  highlight: function (str, lang) {
    if (lang && hljs.getLanguage(lang)) {
      try {
        return hljs.highlight(str, { language: lang }).value;
      } catch (__) {}
    }

    return ''; // use external default escaping
  }
});
const props = defineProps(['engines']);
const currentItem = ref();// engine
const currentModel = ref();
const showModelList = ref(false);
const isRunning = ref(false);
const messages = ref([]);//[author time content]
const msgHistory = ref([]);
const question = ref('');
const hasContext = ref(false);

const modelConfig = ref();
var modelParams = {};
const modelList = ref([]);
onMounted(()=>{
    if(props.engines.length > 0){
        currentItem.value = props.engines[0].engine;
        loadTextModels(currentItem.value);
    }

    //listen keyborad
    document.addEventListener('keydown', function(event) {
        // console.log('keyborad:code/ctrlKey/key=',event.code ,event.ctrlKey,event.key, event.shiftKey, event.metaKey);
        if(event.metaKey === true || event.ctrlKey == true){
            switch(event.code){
                case 'Enter':
                    if(question.value){
                        ask(question.value);
                    }                    
                    break;
            }
        }
    });

});
onUnmounted(() => {
});

const setEngine = (item, index)=>{
    currentItem.value = item.engine;
    currentModel.value = null;
    loadTextModels(currentItem.value);
}
const setTextModel = (item)=>{
    currentModel.value = item.name;
    showModelList.value = false;
}

async function loadTextModels(engine){
    let res = await aiHttpRequest('get', '/api/text/models', {engine:engine});
    
    if(res == null || res.code != 1){
        showTopSnackbar(trans("Loading data error"), 'error');
        return;
    }
    modelConfig.value = res.data.config;
    modelList.value = res.data.models;
    if(currentModel.value == null && modelList.value.length > 0){
        currentModel.value = modelList.value[0].name;
    }
    // console.log(currentModel.value );
}

function computeModelSelectStyle(model_name){
    if(model_name == currentModel.value){
        return ' border-teal-600';
    }else{
        return '';
    }
}
function computeChatLocate(role){
    if(role == 'user'){
        return 'chat chat-end flex flex-col items-end';
    }else{
        return 'chat chat-start flex flex-col items-start';
    }
}
function computeChatRoleBG(role){
    if(role == 'user'){
        return 'chat-bubble text-end markdown-body';
    }else{
        return 'chat-bubble markdown-body';
    }
}

function tsComCallback(k ,v){
    switch(k){
        case 'model_name':
            currentModel.value = v.name;
            break;
        case 'has_context':
            hasContext.value = !hasContext.value;
            break;
        default:
        modelParams[k] = v;
    }
}

async function ask(msg){
    if(msg == null || msg == ''){
        showTopSnackbar(trans("Text can not be null"), 'error');
        return;
    }
    let objDiv = document.getElementById("mainpage");
    let timestamp = Date.now();
    var date = new Date(timestamp);
    let query = {
        role:'user',
        content:msg,
        created_at:date.toLocaleDateString() + " " + date.toLocaleTimeString(),
        author:'Me'
    };
    window.scrollTo({ top: objDiv.scrollHeight +100, left: 0, behavior: 'smooth' });
    if(hasContext.value == false){
        messages.value = [];
    }
    messages.value.push(query);
    msgHistory.value.push(query);
    question.value = '';

    isRunning.value = true;
    let params = {
        engine:currentItem.value,
        messages: JSON.stringify(messages.value), 
        model:currentModel.value, 
        model_config:JSON.stringify(modelParams),
    };
    let res = await aiHttpRequest('post', '/api/text/ask', params);
    isRunning.value = false;
    if(res == null || res.code != 1){
        let errorMsg = res?res.msg:trans("Loading data error");
        showTopSnackbar(errorMsg, 'error');
        return;
    }
    Object.entries(res.data).forEach(function([key, value]) {
        console.log(key, value);
        messages.value.push(value);
        msgHistory.value.push(value);
    });
    window.scrollTo({ top: objDiv.scrollHeight +100, left: 0, behavior: 'smooth' });
}

</script>
<style scoped>
    .top-100 {top: 100%}
    .bottom-100 {bottom: 100%}
    .max-h-select {
        max-height: 300px;
    }
	.markdown-body {
		box-sizing: border-box;
		min-width: 200px;
		max-width: 980px;
		margin: 0 auto;
		padding: 10px;
	}

	@media (max-width: 767px) {
		.markdown-body {
			padding: 15px;
		}
	}
</style>
<template>
<AppLayout title="AI Text">
<div id="mainpage" class="mx-auto sm:px-6 lg:px-8 flex gap-2 py-5">
    <div id="menu" class=" w-72">
        <div class="">
            <div role="tablist" class="tabs tabs-boxed tabls-sm justify-self-start">
                <a 
                    v-for="(item, index) in props.engines" 
                    :key="index" 
                    :class="{'tab-active': item.engine == currentItem }"
                    @click="setEngine(item, index)"
                    role="tab" 
                    class="tab font-bold">{{item.label}}
                </a>
            </div>
            <div v-if="props.engines.length == 0" class="text-red-500 font-bold text-sm">{{trans('Not found the active Text model')}}</div>
        </div>

        <div class="flex-auto flex flex-col items-center">
            <div class="flex flex-col items-center relative w-auto">
                <div class="w-72">
                    <div class="my-2 bg-white p-1 flex border border-gray-200 rounded">
                        <div class="flex flex-auto flex-wrap"></div>
                        <input :value="currentModel" @click="showModelList = !showModelList" class="p-1 px-2 appearance-none outline-none w-full text-gray-800 text-xs ">
                      
                        <div class="text-gray-300 w-8 py-1 pl-2 pr-1 border-l flex items-center border-gray-200">
                            <button @click="showModelList = !showModelList" class="cursor-pointer w-6 h-6 text-gray-600 outline-none focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-up w-4 h-4">
                                    <polyline points="18 15 12 9 6 15"></polyline>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div v-show="showModelList" class="absolute shadow top-100 z-40 lef-0 rounded max-h-select overflow-y-auto  w-72 ">
                    <div  class="flex flex-col w-full">
                        <div v-for="(item, index) in modelList" :key="index" class="cursor-pointer w-full border-gray-100 rounded-t border-b 
                    hover:bg-teal-100" style="">
                            <div class="flex w-full items-center p-2 pl-2 border-transparent bg-white border-l-2 relative hover:bg-teal-600 hover:text-teal-100 hover:border-teal-600" :class="computeModelSelectStyle(item.name)">
                                <div  @click="setTextModel(item)" class="w-full items-center flex">
                                    <div class="mx-2 leading-6 text-sm ">{{item.label}}</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div v-for="(item,index) in modelConfig" :key="index">
            <div v-if="item.type =='slider'">
                <TSSlider :name="item.key" :label="trans(item.key)" @tsComCallback="tsComCallback" :min="item.min" :max="item.max" :def="item.def" :step="item.step"></TSSlider>
            </div>
            <div v-else-if="item.type == 'switch'" class="py-5">
                <TSSwitch :name="item.key" :label="trans(item.key)" @tsComCallback="tsComCallback" :def="hasContext"></TSSwitch>
            </div>
            
        </div>

    </div>

    <div id="content" class="w-full ">
        <!-- message area  :class="computeChatLocate(item.role)" -->
        <div v-show="msgHistory.length > 0" name="chatbox" class=" border border-gray-300 rounded-md bg-gray-100 px-3">
            <div v-for="(item, index) in msgHistory" :key="index" :class="computeChatLocate(item.role)">
                <div class="chat-header">
                    {{item.author}}
                    <time class="text-xs opacity-50">{{item.created_at}}</time>
                </div>
                <div >
                    <div :class="computeChatRoleBG(item.role)" v-html="markdown.render(item.content)"></div>
                </div>
                
            </div>
            
            <!-- :class="computeChatRoleBG(item.role)" -->
            <!-- <div class="chat chat-start" >
                <div class="chat-header">Obi-Wan Kenobi
                    <time class="text-xs opacity-50">12:45</time>
                </div>
                <div class="chat-bubble">You were the Chosen One!</div>
            </div>

            <div class="chat chat-end">
                <div class="chat-header">Anakin
                    <time class="text-xs opacity-50">12:46</time>
                </div>
                <div class="chat-bubble">I hate you</div>
            </div> -->

        </div>

        

        
        <!-- float text input area  :class="{'fixed bottom-0 left-1/2 transform -translate-x-1/2 inline-flex mx-auto':messages.length > 0}"-->
        <div class="justify-between w-10/12 rounded-3xl">
            <div class="w-72"></div>
            <!-- text input -->
            <div class="flex py-2 grow join">
                <textarea v-model="question" class="textarea textarea-bordered w-full h-32 join-item focus:outline-none" placeholder="Enter text here"></textarea>
                <div class="flex justify-end items-center join-item">
                    <button v-if="isRunning == false" @click="ask(question)"
                        type="button"
                        class="text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium  w-32 h-32 text-sm px-5 py-2.5 text-center me-2 mb-2 rounded-md" :disabled="isRunning">
                        {{$t('Submit')}}
                    </button>
                    <button v-else disabled type="button" class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium text-sm w-32 h-32 px-5 py-2.5 text-center me-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800 inline-flex items-center cursor-pointer">
                        <svg aria-hidden="true" role="status" class="inline w-4 h-4 me-3 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB"/>
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"/>
                        </svg>
                        {{$t('Thinking')}}
                    </button>
                </div>
            </div>

        </div>
        <div v-show="messages.length > 0" class=" h-96"></div>

    </div>
</div>
</AppLayout>
</template>
