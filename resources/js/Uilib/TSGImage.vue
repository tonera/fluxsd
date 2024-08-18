<script setup>
//图文组件：滤镜，翻转
import TSDropInput from './TSDropInput.vue';
import TSRangeInput from './TSRangeInput.vue';
import {ref } from 'vue';
const showContent = ref(true);

const props = defineProps(['name','init', 'label']);
const emit = defineEmits(['tsComCallback']);

// console.log(props.init);


function tsComCallback(name , value){
    // console.log(name,'=',value);
    emit('tsComCallback', name, value);
}

</script>

<template>
    <div>
        <div class="divider divider-start label-text-alt cursor-pointer">
            {{props.label}}
        
        </div>
        
        <div v-show="showContent" class="grid grid-cols-2 gap-2 ml-2 py-1">
            <TSRangeInput :name="'skewX'" :init="props.init?.skewX" :label="'X'" :max="180" :min="0" @tsComCallback="tsComCallback"></TSRangeInput>
            <TSRangeInput :name="'skewY'" :init="props.init?.skewY" :label="'Y'" :max="180" :min="0" @tsComCallback="tsComCallback"></TSRangeInput>
        </div>

    
    </div>
</template>