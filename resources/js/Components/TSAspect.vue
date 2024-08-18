<script setup>
import { ref } from 'vue';

const props = defineProps(['name','init','label','def']);
const emit = defineEmits(['tsComCallback']);
const currentIndex = ref(props.def??0);

function setValue(item,index){
    emit('tsComCallback', props.name, index);
    currentIndex.value = index;
}
</script>

<template>

<div class="flex gap-2">
    <div 
        @click="setValue(item,index)" 
        v-for="(item, index) in props.init" :key="index" 
        class="bg-gray-300 text-center cursor-pointer"
        :class="currentIndex == index ? 'border-solid border-4 border-green-500':''"
        >
        <div class=" h-14 w-14 text-center place-items-center grid">
            <div class="bg-white" :style="{'height': (item.height/30)+'px','width': (item.width/30)+'px'}"></div>
        </div>
        <div class=" text-center font-serif text-gray-800">{{item.label}}</div>
    </div>
</div>

</template>