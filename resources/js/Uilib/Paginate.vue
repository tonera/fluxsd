<script setup>
import { reactive,computed, onMounted, watch } from 'vue';
const emit = defineEmits(['handleJump']);

const props = defineProps(['total', 'cpage']);
const limit = 9;//显示10个页码
var start = 0;
var end = 0;
reactive({
    start:0,
    end:0,
});



const pageList = computed(()=>{
    let listtmp = [];
    //计算起始和结束页码，当前页码永远在中间，移位limit的一半
    start = props.cpage - Math.floor(limit/2);
    start = start < 1 ? 1 : start;//最小不能小于1
    end = start + limit;
    end = end > props.total ? props.total : end;
    for (let pageNumber = start; pageNumber <= end; pageNumber++) {
        let element = {
            label:pageNumber ,
            page:pageNumber,
        };
        listtmp.push(element);
    }
    return listtmp;
});

const classObject = computed( () => (page)=>{
    return {
        'bg-slate-300 hover:bg-slate-500 focus:bg-slate-300 ': props.cpage == page,
        'bg-neutral-100 hover:bg-slate-500':props.cpage != page,
    };
})

function handlePage(page){
    if(page > props.total || page < 1){
        return;
    }
    emit('handleJump', page);
}
// console.log(props.total);

</script>

<template>
<div v-if="total>1" class="inline-flex rounded-md shadow-sm" role="group">
    <button type="button" class="bg-neutral-100 rounded-l hover:bg-slate-500 inline-block px-3 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-neutral-800 transition duration-150 ease-in-out focus:ring-0 active:bg-neutral-200"
        data-te-ripple-init
        data-te-ripple-color="light"
        @click="handlePage(parseInt(props.cpage)-1)"
        >
        <svg class="w-[20px] h-[20px] fill-[#8e8e8e]" viewBox="0 0 320 512" xmlns="http://www.w3.org/2000/svg">
        <path d="M41.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.3 256 246.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"></path>
        </svg>
        <!-- {{$t('Previous')}} -->
    </button>
    <button v-for="(btn,idx) in pageList" type="button" class="inline-block px-6 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-neutral-800 transition duration-150 ease-in-out focus:ring-0 active:bg-neutral-200"
        data-te-ripple-init
        data-te-ripple-color="light"  
        :class="classObject(btn.page)" @click="handlePage(btn.page)" :key="idx"
        >
        {{btn.label}}
    </button>
    <button type="button" class="bg-neutral-100 rounded-r hover:bg-slate-500 inline-block px-3 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-neutral-800 transition duration-150 ease-in-out focus:ring-0 active:bg-neutral-200"
        data-te-ripple-init
        data-te-ripple-color="light"
        @click="handlePage(parseInt(props.cpage)+1)"
        >
        <svg class="w-[20px] h-[20px] fill-[#8e8e8e]" viewBox="0 0 320 512" xmlns="http://www.w3.org/2000/svg">
        <path d="M278.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-160 160c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L210.7 256 73.4 118.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l160 160z"></path>
        </svg>
    </button>
</div>
<div v-else></div>

</template>