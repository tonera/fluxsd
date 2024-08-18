<script setup>
//选择宽高比
import { ref } from 'vue';

const props = defineProps(['name','init','label','def','size']);
const emit = defineEmits(['tsComCallback']);
const currentIndex = ref(props.def);
var base = 25;

if(props.size){
    base = parseInt(props.size);
}

function setValue(item,index){
    emit('tsComCallback', props.name, index);
    currentIndex.value = index;
}

// console.log(base);
//:class="getSizeClass(item)" 
//:style="{'height': (item.height/25)+'px','width': (item.width/25)+'px'}"
function getSizeClass(item){
    let width = 0;
    let height = 0;
    let aspect = item.width/item.height;
    if(aspect > 1){
        width = base;
        height = parseInt(base/aspect);
    }else{
        width = parseInt(base*aspect);
        height = base;
    }
    // let sizeClass = "w-["+width+"px] h-["+height+"px]";
    let sizeClass = "height: "+height+"px ;width:"+width+"px";
    // let sizeClass="height: 19px;width:25px";
    // console.log(sizeClass);
    return sizeClass;
}

//:style="getSizeClass(item)"
</script>

<template>

<div class="flex">
    <div 
        @click="setValue(item,index)" 
        v-for="(item, index) in props.init" :key="index" 
        class="bg-gray-300 dark:bg-gray-700 text-center m-1 cursor-pointer"
        :class="currentIndex == index ? 'border-solid border-2 border-green-500':''"
        >
        <div class="p-2 w-12 grid grid-rows-2 items-center justify-items-center">
            <div class="bg-white h-full" :style="getSizeClass(item)"></div>
            <div class=" text-center ">{{item.label}}</div>
        </div>
        
    </div>
</div>

</template>