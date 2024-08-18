<script setup>
import { ref,watch } from 'vue';
import {getSrc} from '../network';

const props = defineProps(['name','desc', 'def', 'imgheight']);
const init_image = ref(props.def);
const emit = defineEmits(['tsComCallback']);

function uploadMutiFile(event){
    if(event.target.files[0] === undefined){
        // console.log('无文件选中,上传取消');
        return;
    }
    getSrc(event.target.files[0]).then((res) => {
        init_image.value = res;
        // console.log(res);
    }).catch((err) => {
        console.log(err);
    });
    emit('tsComCallback', props.name, event.target.files[0]);
}

function moveInitImage(){
    init_image.value = null;
    emit('tsComCallback', props.name, null);
}

watch(()=>props.def, (e)=>{
    init_image.value = props.def;
});

</script>

<template>

    <div v-if="init_image != null" class="m-1 max-h-min max-w-max relative inline-flex items-center p-3 text-sm font-medium text-center text-white bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700">
        <img :src="init_image" class=" object-cover" :class="props.imgheight">
        <button type="button" class="absolute inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white  border-2 border-white rounded-full -top-2 -right-2 dark:border-gray-900" @click="moveInitImage(index)">
            <svg class="w-[20px] h-[20px] fill-[#dd2222]" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
            <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM184 232H328c13.3 0 24 10.7 24 24s-10.7 24-24 24H184c-13.3 0-24-10.7-24-24s10.7-24 24-24z"></path>
            </svg>
        </button>
    </div>

    <label v-else :for="props.name" 
    class="m-1 flex h-full w-full flex-col items-center justify-center border-2 border-gray-400 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-200">
        <div class="flex flex-col items-center justify-center p-4">
            <p class="mb-4 text-sm text-gray-500"><span class="font-semibold">{{props.desc??''}}</span></p>
            <input :id="props.name" type="file" multiple accept="image/png, image/jpeg" class="hidden" @input="uploadMutiFile($event)"/>
            <p class="text-xs text-gray-500">PNG or JPG</p>
        </div>
    </label>

</template>