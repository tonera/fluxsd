<script setup>
//图文组件组：边框-颜色，透明度，宽度
import TSInput from './TSInput.vue';
import TSColorPicker from './TSColorPicker.vue';
import {ref } from 'vue';
import {trans} from 'laravel-vue-i18n';
const showContent = ref(true);
const props = defineProps(['name','init','def','label']);
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
        <div v-show="showContent" class="grid grid-cols-2 gap-2 ml-2">
            <!-- 对像宽高 'name','init','label' -->
            <div class="flex flex-col gap-1">
     
                <TSColorPicker :name="'stroke'" :init="props.init?.stroke" :label="trans('Color')" @tsComCallback="tsComCallback"></TSColorPicker>
                
            </div>
            <!-- 偏移坐标 -->
            <div class="flex flex-col gap-1  grow">
                <TSInput :name="'strokeWidth'" :init="props.init?.strokeWidth" :label="trans('Width')" @tsComCallback="tsComCallback"></TSInput>
            </div>
            <div class="gap-1">
                
                
            </div>
        </div>
    </div>
</template>