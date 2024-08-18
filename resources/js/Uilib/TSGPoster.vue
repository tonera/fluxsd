<script setup>
//画布模板选择编辑器
import TSInput from './TSInput.vue';
import TSColorPicker from './TSColorPicker.vue';
import {ref } from 'vue';
import {trans} from 'laravel-vue-i18n';
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
<div class="">
    <!-- 宽高度 -->
    <div class="flex gap-2 ">
        <TSInput :name="'w'" :label="'宽度'" :init="props.init.w" @tsComCallback="tsComCallback"></TSInput>
        <select class="select text-xs select-bordered select-sm w-full max-w-xs" @change="tsComCallback('unit', $event.target.value)">
            <option :selected="props.init.unit === 'inch'" value="inch">{{'Inch'}}</option>
            <option :selected="props.init.unit === 'pixel'" value="pixel">{{'Pixel'}}</option>
            <option :selected="props.init.unit === 'cm'" value="cm">{{'cm'}}</option>
            <option :selected="props.init.unit === 'mm'" value="mm">{{'mm'}}</option>
        </select>
    </div>
    <div class="flex gap-2 ">
        <TSInput :name="'h'" :label="trans('Height')" :init="props.init.h" @tsComCallback="tsComCallback"></TSInput>
        <!-- 横向还是竖向 -->
        <div class="form-control w-32 items-end">
            <label class="cursor-pointer label gap-2">
            <span class="label-text">{{props.init?.vh?trans('Horizontal'):trans('Vertical') }}</span> 
            <input type="checkbox" class="toggle toggle-primary" :checked="props.init?.vh" @click="tsComCallback('vh', $event.target.checked) " />
            </label>
        </div>
    </div>
    <div class="flex gap-2 z-50">

            <TSColorPicker :name="'background'" :init="props.init?.background" :label="trans('Background')" @tsComCallback="tsComCallback"></TSColorPicker>

    </div>

    
    


</div>
</template>