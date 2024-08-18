<script setup>
//组件：阴影 颜色，模糊度，偏移x轴 偏移y轴
import TSDropInput from './TSDropInput.vue';
import TSColorPicker from './TSColorPicker.vue';
import TSRangeInput from './TSRangeInput.vue';
import {trans} from 'laravel-vue-i18n';
import {ref } from 'vue';
const showContent = ref(true);

const props = defineProps(['name','init','def', 'label']);
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
            <!-- 阴影颜色，X轴 -->
            <div class="flex flex-col gap-1">
                <TSColorPicker :name="'color'" :init="props.init?.color" :label="trans('Color')" @tsComCallback="tsComCallback"></TSColorPicker>
                <!-- <TSDropInput :name="'shadowBlurDef'" :init="props.init?.shadowBlur" :label="'模糊'" @tsComCallback="callback" :def="props.init?.shadowBlurDef"></TSDropInput> -->
                <TSRangeInput :name="'shadowBlurDef'" :init="props.init?.shadowBlurDef" :label="trans('Blur')" :max="100" :min="0" :step="1" @tsComCallback="tsComCallback"></TSRangeInput>
            </div>
            <!-- 模糊度 Y轴 -->
            <div class="flex flex-col gap-1  grow">
                <TSDropInput :name="'shadowXDef'" :init="props.init?.shadowX" :label="'X'" @tsComCallback="tsComCallback" :def="props.init?.shadowXDef"></TSDropInput>
                <TSDropInput :name="'shadowYDef'" :init="props.init?.shadowY" :label="'Y'" @tsComCallback="tsComCallback" :def="props.init?.shadowYDef"></TSDropInput>
            </div>
            
        </div>
    </div>
</template>