<script setup>
//poster尺寸模板
import {ref, watch } from 'vue';
const props = defineProps(['init', 'def']);
const emit = defineEmits(['tsComCallback']);
// console.log(props.init);
//计算宽与长
let width = 0;
let height = 0;
let base = 60;//px
let respect = props.init.w/props.init.h;

if(respect > 1){
    width = base;
    height = parseInt(base / respect);
}else{
    width = parseInt(base * respect);
    height = base;
}
const sizeClass = ref("w-["+width+"px] h-["+height+"px] ");

function tsComCallback(item){
    emit('tsComCallback', item);
}

// console.log(sizeClass);

</script>

<template>
    <div @click="tsComCallback(props.init)" class="w-24 h-28 items-center justify-center cursor-pointer rounded-xl hover:bg-base-200 flex flex-col gap-2 p-2" :class="{'dark:bg-base-300':props.init?.label == props.def?.label}">
        <!-- <div class="dark:bg-slate-100" :class="sizeClass"></div> -->

        <svg v-if="props.init?.vh == false" :class="sizeClass" fill="currentcolor" viewBox="0 0 384 512" xmlns="http://www.w3.org/2000/svg">
<path d="M64 464c-8.8 0-16-7.2-16-16V64c0-8.8 7.2-16 16-16H224v80c0 17.7 14.3 32 32 32h80V448c0 8.8-7.2 16-16 16H64zM64 0C28.7 0 0 28.7 0 64V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V154.5c0-17-6.7-33.3-18.7-45.3L274.7 18.7C262.7 6.7 246.5 0 229.5 0H64zm96 256a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zm69.2 46.9c-3-4.3-7.9-6.9-13.2-6.9s-10.2 2.6-13.2 6.9l-41.3 59.7-11.9-19.1c-2.9-4.7-8.1-7.5-13.6-7.5s-10.6 2.8-13.6 7.5l-40 64c-3.1 4.9-3.2 11.1-.4 16.2s8.2 8.2 14 8.2h48 32 40 72c6 0 11.4-3.3 14.2-8.6s2.4-11.6-1-16.5l-72-104z"></path>
</svg>

<svg v-else :class="sizeClass" fill="currentcolor" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
<path d="M448 80c8.8 0 16 7.2 16 16V415.8l-5-6.5-136-176c-4.5-5.9-11.6-9.3-19-9.3s-14.4 3.4-19 9.3L202 340.7l-30.5-42.7C167 291.7 159.8 288 152 288s-15 3.7-19.5 10.1l-80 112L48 416.3l0-.3V96c0-8.8 7.2-16 16-16H448zM64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V96c0-35.3-28.7-64-64-64H64zm80 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"></path>
</svg>

        <div class="items-center">
            <p class="label-text-alt text-center">{{props.init?.label}}</p>
        </div>
    </div>
</template>