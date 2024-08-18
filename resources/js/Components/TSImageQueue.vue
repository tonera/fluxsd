<script setup>
import { ref, watch } from 'vue';

const props = defineProps(['name','init','def','tabstyle','moveto']);
const emit = defineEmits(['tsComCallback']);
const currentIndex = ref(props.def??0);

// console.log('props.init = ');
// console.log(props.init);

watch(()=>props.moveto, (e)=>{
    const arr = props.moveto.split("_");
    console.log(arr);
    if(arr[0] == 'next'){
        setValue(currentIndex.value+1);
    }else{
        setValue(currentIndex.value-1);
    }
    // props.moveto = '';
});

const tabStyle = {
    small:{
        btnw:30,
        btnh:70,
        blockw:'w-16',
        img:'w-16 h-16 object-cover cursor-pointer',
        pix:68, //每小图间距
    },
    normal:{
        btnw:50,
        btnh:120,
        blockw:'w-32',
        img:'w-32 h-32 object-cover cursor-pointer',
        pix:132,
    }
};
const currentStyle = props.tabstyle == 'normal' ? tabStyle.normal : tabStyle.small;

async function setValue(index){
    let total = parseInt(props.init.length);
    // console.log('total = ' + total);
    index = index < 0 ? 0 : index;
    index = index >= total ? (total-1) :index;
    let item = props.init[index];
    item['index'] = index;
    emit('tsComCallback', props.name, item);
    currentIndex.value = index;
    //console.log('item = ' + item);
    // console.log('index = ' + index);
    let cposition = (index-2) * currentStyle.pix;
    // console.log(cposition);
    document.getElementById('style-list').scrollTo({ left: cposition, behavior: 'smooth' });
}

</script>

<template>

<div v-show="props.init != undefined && props.init.length > 0" class="px-4 py-2 sm:px-6 lg:px-8 lg:py-2 mx-auto flex mb-2 sm:mb-2">
    <div class="oneline">
        <button class="movebutton" type="button" @click="setValue(currentIndex-1)">
            <svg :width="currentStyle.btnw" :height="currentStyle.btnh" fill="currentColor" class="bi bi-chevron-compact-left" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M9.224 1.553a.5.5 0 0 1 .223.67L6.56 8l2.888 5.776a.5.5 0 1 1-.894.448l-3-6a.5.5 0 0 1 0-.448l3-6a.5.5 0 0 1 .67-.223z"/>
            </svg>
        </button>
    </div>
    <!--no-scrollba-->
    <div id="style-list" class="flex flex-nowrap overscroll-auto overflow-auto">
        <div  v-for="(item, index) in props.init" class="text-center m-1" @click="setValue(index)" :key="'L'+index">
            <div :class="currentStyle.blockw">
                <img :class="[currentStyle.img, {'border-solid border-4 border-green-500':index == currentIndex}]" :src="item.thumb">
            </div>
        </div>
    </div>
    
    <div class="oneline">
        <button class="movebutton" type="button" @click="setValue(currentIndex+1)">
            <svg :width="currentStyle.btnw" :height="currentStyle.btnh" fill="currentColor" class="bi bi-chevron-compact-right" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M6.776 1.553a.5.5 0 0 1 .671.223l3 6a.5.5 0 0 1 0 .448l-3 6a.5.5 0 1 1-.894-.448L9.44 8 6.553 2.224a.5.5 0 0 1 .223-.671z"/>
            </svg>
        </button>
    </div>
</div>
</template>