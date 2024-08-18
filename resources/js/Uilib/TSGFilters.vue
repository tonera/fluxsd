<script setup>
import { onMounted, ref,watch ,computed} from 'vue';
//组件：滤镜大全
import TSDropInput from './TSDropInput.vue';
import TSColorPicker from './TSColorPicker.vue';
import TSRangeInput from './TSRangeInput.vue';
import {trans} from 'laravel-vue-i18n';
//def 是否选中 init滤镜实体
const props = defineProps(['name','init','def']);
const emit = defineEmits(['tsComCallback']);
// console.log(props.name, props.init);

//回调的数据
const submit = ref({
    name:props.name,
    isChecked:false,
    color:null,
    val:null,
    mode:null,
});

watch(() => props.init?.isChecked, () => {
    submit.value.isChecked = props.init.isChecked;
});

function tsComCallback(){
    if(submit.value.val == null){
        submit.value.val = props.init?.val;
    }
    // console.log(submit.value);
    emit('tsComCallback', props.name, submit.value);
}

function handelCheckBox(val){
    submit.value.isChecked = val;
    tsComCallback();
}

function handelColor(name, val){
    submit.value.color = val;
    tsComCallback();
}
function handelVal(name, val){
    submit.value.val = val;
    tsComCallback();
}
function handelMode(name, val){
    submit.value.mode = val.val;
    tsComCallback();
}
// console.log(radioRef.value );

</script>

<template>
<div class="">
    <div v-if="props.init?.subType == 'radio'" class="py-1">
        <!-- name and checkbox -->
        <div class="form-control">
            <label class="label cursor-pointer w-full flex">
                <span class="label-text">{{props.name}}</span> 
                <input type="checkbox" :checked="props.def" class="checkbox checkbox-sm" @click="handelCheckBox($event.target.checked)" />
            </label>
        </div>
        <div class="flex justify-evenly" v-show="submit.isChecked">
            <div v-for="(item, index) in props.init.options" :key="'TSGF_OP_'+index" class="">
                {{item.label}}
                <input type="radio" :name="props.name" :value="item.val" :checked="props.init?.val == item.val" class="radio radio-sm dark:bg-slate-600" @click="handelVal(props.name,$event.target.value)" />
            </div>
        </div>
    </div>

    <div v-else-if="props.init?.subType == 'colorRange'" class="py-1">
        <div class="">
            <div class="form-control">
                <label class="label cursor-pointer">
                    <span class="label-text">{{props.name}}</span> 
                    <input type="checkbox" :checked="props.def" class="checkbox checkbox-sm" @click="handelCheckBox($event.target.checked)" />
                </label>
            </div>
            <div class="flex" v-show="submit.isChecked">
                <!-- 颜色和数值选择 -->
                <TSColorPicker :name="'fontColor'" :init="props.init?.color" :label="trans('Color')" @tsComCallback="handelColor"></TSColorPicker>
                <TSRangeInput :name="'distance'" :init="props.init?.val" :max="props.init?.max" :min="props.init?.min" :step="props.init?.step" @tsComCallback="handelVal"></TSRangeInput>
            </div>
        </div>
    </div>

    <div v-else-if="'range' == props.init?.subType" class="py-1">
        <div class="form-control">
            <label class="label cursor-pointer">
                <span class="label-text">{{props.name}}</span> 
                <input type="checkbox" :checked="props.def" class="checkbox checkbox-sm" @click="handelCheckBox($event.target.checked)" />
            </label>
        </div>
        <div class="flex" v-show="submit.isChecked">
            <!-- range数值选择 -->
            <TSRangeInput :name="'distance'" :init="props.init?.val" :max="props.init?.max" :min="props.init?.min" :step="props.init?.step" @tsComCallback="handelVal"></TSRangeInput>
        </div>
    </div>

    <!-- only for gamma -->
    <div v-else-if="'MultiRange' == props.init?.subType" class="py-1">
        <div class="form-control">
            <label class="label cursor-pointer">
                <span class="label-text">{{props.name}}</span> 
                <input type="checkbox" :checked="props.def" class="checkbox checkbox-sm" @click="handelCheckBox($event.target.checked)" />
            </label>
        </div>
        <div class="flex" v-show="submit.isChecked">
            <!-- 颜色和数值选择 -->
            <TSColorPicker :name="'color'" :init="props.init?.color" :label="trans('Color')" @tsComCallback="handelColor"></TSColorPicker>
        </div>
    </div>

    <div v-else-if="'MultiModeRange' == props.init?.subType" class="py-1">
        <div class="form-control">
            <label class="label cursor-pointer">
                <span class="label-text">{{props.name}}</span> 
                <input type="checkbox" :checked="props.def" class="checkbox checkbox-sm" @click="handelCheckBox($event.target.checked)" />
            </label>
        </div>
        <div class="flex" v-show="submit.isChecked">
            <!-- 颜色和数值选择 -->
            <TSColorPicker :name="'color'" :init="props.init?.color" :label="trans('Color')" @tsComCallback="handelColor"></TSColorPicker>
            <TSDropInput :name="'mode'" :init="props.init?.mode" :label="'Mode'" @tsComCallback="handelMode" :def="submit.mode"></TSDropInput>
        </div>
    </div>

    <div v-else class="py-1">
        <div class="form-control">
            <label class="label cursor-pointer">
                <span class="label-text">{{props.name}}</span> 
                <input type="checkbox" :checked="props.def" class="checkbox checkbox-sm" @click="handelCheckBox($event.target.checked)" />
            </label>
        </div>
    </div>
</div>    


</template>