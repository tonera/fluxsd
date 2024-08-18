<script setup>
import {  ref } from 'vue';
import {usePage } from '@inertiajs/vue3';
const props = defineProps(['name','init','mode', 'notice','prompt','rbg','crop','export','percent','delete']);
const emit = defineEmits(['tsComCallback', 'handleSelectedImage']);

const page = usePage();
const source = ref('')
const copied = ref(false);
if(props.init.task != null){
    source.value = props.init.task.prompt_en;
}

var imgSizeStyle = "h-36";
if(props.mode == 'big'){
    imgSizeStyle = 'h-72';
}else{
    imgSizeStyle = "h-36";
}
function handleSelectedImage(image){
    emit('handleSelectedImage', image);
}

//only https works fine
async function copyToCliboard(txt){
    console.log('txt=',txt);
    copied.value = true;
    // copyDoiToClipboard(txt);
    // navigator.clipboard.writeText('iii');
    try {
        await navigator.clipboard.writeText(txt);
        // console.log('Content copied to clipboard');
        /* Resolved - text copied to clipboard successfully */
    } catch (err) {
        copied.value = false;
        // console.error('Failed to copy: ', err);
        /* Rejected - text failed to copy to the clipboard */
    }
}

</script>
<template>
    <div v-if="props.mode == 'big'" class="group block relative cursor-pointer" >
        <div class="absolute z-10 inset-0 pointer-events-none border border-black/5 dark:border-white/10 rounded-xl"></div>
        <img @click="handleSelectedImage(props.init)" :src="props.init?.thumb" class=" object-cover w-full cursor-pointer rounded-xl" :class="imgSizeStyle">
        <div style="pointer-events:none;" class="absolute inset-0 flex flex-col items-stretch justify-between p-2 bg-black/70 opacity-0 transition-opacity duration-500 group-hover:opacity-100 rounded-xl">
                
        <div class="flex justify-between text-white" style="pointer-events:auto;">
            <button v-show="props.export != null"  @click="emit('tsComCallback', 'export', props.init)" class="flex flex-shrink-0 items-center h-8 px-3 bg-white/10 rounded-full hover:bg-white/20 text-xs">
                {{props.export}}
            </button>

            <button v-show="props.delete != null" @click="emit('tsComCallback', 'delete', props.init)" class="flex flex-shrink-0 items-center h-8 px-3 bg-white/10 rounded-full hover:bg-white/20 text-xs">
                <span>{{props.delete}}</span>
            </button>
        </div>
        <span class="group-hover:opacity-100 opacity-0 transition-opacity duration-500 antialiased text-white text-sm ">
            <div v-show="props.notice != null" class="flex" v-html="props.notice"></div>
            <div class="flex justify-end pt-4 gap-1" style="pointer-events:auto;">
                <button v-show="props.rbg != null" @click="emit('tsComCallback', 'rbg', props.init)" class="flex flex-shrink-0 items-center h-8 px-3 bg-white/10 rounded-full hover:bg-white/20 text-xs">
                    {{props.rbg}}
                </button>
                <button v-show="props.crop != null"  @click="emit('tsComCallback', 'crop', props.init)" class="flex flex-shrink-0 items-center h-8 px-3 bg-white/10 rounded-full hover:bg-white/20 text-xs">
                    {{props.crop}}
                </button>
            </div>
        </span>
        </div>
        <div v-if="props.percent !== undefined" style="pointer-events:none;" class="absolute inset-0 flex flex-col justify-center items-center p-2 bg-black/70 transition-opacity duration-500 opacity-50 rounded-xl text-white">
            <div class="radial-progress" style="--size:6rem; --thickness: 3px;" :style="'--value:'+props.percent+';'" role="progressbar">{{props.percent}}%</div>
        </div>
    </div>
    <img v-else @click="handleSelectedImage(props.init)" :src="props.init?.thumb" class=" object-cover w-full cursor-pointer rounded-xl" :class="imgSizeStyle">


</template>
