<script setup>
//图文组件：背景色修改与背景图操作
import TSColorPicker from './TSColorPicker.vue';
import {ref, watch } from 'vue';
import {debug} from './TSFunction';
import {trans} from 'laravel-vue-i18n';
const showContent = ref(true);

//@init color @image bg image @isImage 当前选择的对象是否是图片
//ref({width:0, height:0,backgroundColor: 'rgba(255, 255, 255,100)', backgroundImage:null});
const props = defineProps(['name','init', 'label','isImage']);
const emit = defineEmits(['tsComCallback','setBackgroundImage','zoom']);

debug('props.init?.backgroundImage=', props.init?.backgroundImage == null);

function tsComCallback(name , value){
    // console.log(name,'=',value);
    emit('tsComCallback', name, value);
}

//删/添加除背景图
function setBackgroundImage(isAdd){
    emit('setBackgroundImage',isAdd);
}
watch(()=>props.isImage, ()=>{
    // console.log('当前对象是否图片', props.isImage);
});
watch(()=>props.init, ()=>{
    // console.log('画布初始化watch', props.init);
});
// console.log('画布初始化', props.init);
</script>

<template>
    <div class="py-2">
        <div class="divider divider-start label-text-alt cursor-pointer">
            {{props.label}}
        
        </div>
        <div v-show="showContent" class="gap-3">
            <div class="flex gap-3 items-center">
                <!-- 缩小画布 -->
                <svg @click="emit('zoom',0.9)" class="w-[18px] h-[18px] cursor-pointer hover:fill-white" fill="currentColor" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM184 232H328c13.3 0 24 10.7 24 24s-10.7 24-24 24H184c-13.3 0-24-10.7-24-24s10.7-24 24-24z"></path>
                </svg>
                <div class="join flex justify-center items-center gap-1">
                    <span class="label-text-alt join-item text-xs">{{trans('Width')}}</span>
                    <input type="text" class="join-item input input-xs input-accent w-12" @change="emit('tsComCallback', 'canvasWidth', $event.target.value);" :value="props.init?.width"/>
                </div>
                <div class="join flex justify-center items-center gap-1">
                    <span class="label-text-alt join-item text-xs">{{trans('Height')}}</span>
                    <input type="text" class="join-item input input-xs input-accent w-12" @change="emit('tsComCallback', 'canvasHeight', $event.target.value);" :value="props.init?.height"/>
                </div>
                <!-- 放大画布 -->
                <svg @click="emit('zoom',1.1)" class="w-[18px] h-[18px] cursor-pointer hover:fill-white" fill="currentColor"  viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM232 344V280H168c-13.3 0-24-10.7-24-24s10.7-24 24-24h64V168c0-13.3 10.7-24 24-24s24 10.7 24 24v64h64c13.3 0 24 10.7 24 24s-10.7 24-24 24H280v64c0 13.3-10.7 24-24 24s-24-10.7-24-24z"></path>
                </svg>
              
                
            </div>
            <div class="flex">
                <!-- 画布背景 backgroundColor -->
                <TSColorPicker :name="'backgroundColor'" :init="props.init?.backgroundColor" :label="trans('Background Color')" @tsComCallback="tsComCallback"></TSColorPicker>
                <div class="join flex justify-start items-center gap-1 py-2">
                    
                    <span class="label-text-alt join-item text-xs w-14">{{trans('Background Image')}}</span>
                    <!-- //如果有背景图，则显示背景图，否则显示上传按钮 -->
                    <div v-if="props.init?.backgroundImage == null || props.init?.backgroundImage == false" class="h-7 flex justify-center items-center">
                        <button v-if="props.isImage" @click="setBackgroundImage(true)"  class="btn btn-xs rounded-full btn-primary  font-light text-xs hover:font-normal">{{trans('Be Background')}}</button>
                        <div v-else class="text-xs">N/A</div>
                    </div>
                    <div v-else class="flex gap-1 items-center h-7">
                        <button @click="setBackgroundImage(false)" class="btn btn-xs rounded-full btn-primary  font-light text-xs hover:font-normal">{{trans('Remove BG')}}</button>
                
                    </div>
                </div>
            </div>
            
        </div>



    </div>


</template>