<script setup>
//组件：对象位置，旋转角度，宽高。适用图片+文字
import {ref } from 'vue';
import TSDropInput from './TSDropInput.vue';
import TSInput from './TSInput.vue';
import TSRangeInput from './TSRangeInput.vue';
import TSColorPicker from './TSColorPicker.vue';
import {trans} from 'laravel-vue-i18n';

//定义对象位置、对象尺寸和偏移度组 isObjectLink=true时，表示锁定对象宽高比
const props = defineProps(['name','init','def','isObjectLink','label']);
const emit = defineEmits(['tsComCallback','showCropObject']);
const showContent = ref(true);
function tsComCallback(name , value){
    emit('tsComCallback', name, value);
}

</script>

<template>
    <div>
       
        <div class="divider divider-start label-text-alt cursor-pointer">
            {{props.label}}
            <!-- <svg v-if="!showContent" class="w-[14px] h-[14px]" fill="yellow" viewBox="0 0 320 512" xmlns="http://www.w3.org/2000/svg">
            <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l192 192c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L77.3 256 246.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-192 192z"></path>
            </svg>
            <svg v-else class="w-[20px] h-[20px]" fill="currentColor" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg">
            <path d="M201.4 342.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 274.7 86.6 137.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z"></path>
            </svg> -->
        </div>
        <div v-show="showContent">
            <div class="flex flex-row gap-2">
                <!-- 宽高比固定? -->
                <div class="justify-center items-center flex w-8 cursor-pointer" @click="emit('tsComCallback', 'isObjectLink', !props.isObjectLink);">
                    <svg class="w-[20px] h-[20px] " :fill="props.isObjectLink ? 'yellow' : 'currentColor'"  viewBox="0 0 640 512" xmlns="http://www.w3.org/2000/svg">
                    <path d="M579.8 267.7c56.5-56.5 56.5-148 0-204.5c-50-50-128.8-56.5-186.3-15.4l-1.6 1.1c-14.4 10.3-17.7 30.3-7.4 44.6s30.3 17.7 44.6 7.4l1.6-1.1c32.1-22.9 76-19.3 103.8 8.6c31.5 31.5 31.5 82.5 0 114L422.3 334.8c-31.5 31.5-82.5 31.5-114 0c-27.9-27.9-31.5-71.8-8.6-103.8l1.1-1.6c10.3-14.4 6.9-34.4-7.4-44.6s-34.4-6.9-44.6 7.4l-1.1 1.6C206.5 251.2 213 330 263 380c56.5 56.5 148 56.5 204.5 0L579.8 267.7zM60.2 244.3c-56.5 56.5-56.5 148 0 204.5c50 50 128.8 56.5 186.3 15.4l1.6-1.1c14.4-10.3 17.7-30.3 7.4-44.6s-30.3-17.7-44.6-7.4l-1.6 1.1c-32.1 22.9-76 19.3-103.8-8.6C74 372 74 321 105.5 289.5L217.7 177.2c31.5-31.5 82.5-31.5 114 0c27.9 27.9 31.5 71.8 8.6 103.9l-1.1 1.6c-10.3 14.4-6.9 34.4 7.4 44.6s34.4 6.9 44.6-7.4l1.1-1.6C433.5 260.8 427 182 377 132c-56.5-56.5-148-56.5-204.5 0L60.2 244.3z"></path>
                    </svg>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <!-- 对象宽高 'name','init','label' -->
                    <div class="flex flex-col gap-1">
                        <TSInput :name="'objectWidth'" :init="props.init?.objectWidth" :label="trans('Width')" @tsComCallback="tsComCallback"></TSInput>
                        <TSInput :name="'objectHeight'" :init="props.init?.objectHeight" :label="trans('Height')" @tsComCallback="tsComCallback"></TSInput>
                        <TSDropInput :name="'objectAngleDef'" :init="props.init?.objectAngle" :label="trans('Rotate')" :def="props.init?.objectAngleDef" @tsComCallback="tsComCallback"></TSDropInput>
                        <div class="flex flex-col  mt-4 gap-1">
                            <div class="join flex justify-start items-center gap-1">
                                <span class="join-item label-text-alt w-10">{{trans('Flip')}}</span>
                                <div class="join-item flex gap-1">
                                    <div class="tooltip tooltip-accent" data-tip="Flip Y">
                                        <svg @click="emit('tsComCallback','flipX', !props.init?.flipX)" class="dark:hover:fill-slate-50 w-[18px] h-[18px] cursor-pointer" fill="currentColor" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M406.6 374.6l96-96c12.5-12.5 12.5-32.8 0-45.3l-96-96c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L402.7 224l-293.5 0 41.4-41.4c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-96 96c-12.5 12.5-12.5 32.8 0 45.3l96 96c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.3 288l293.5 0-41.4 41.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="tooltip tooltip-accent" data-tip="Flip X">
                                        <svg  @click="emit('tsComCallback','flipY', !props.init?.flipY)" class="dark:hover:fill-slate-50 w-[18px] h-[18px] cursor-pointer" fill="currentColor" viewBox="0 0 320 512" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M182.6 9.4c-12.5-12.5-32.8-12.5-45.3 0l-96 96c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L128 109.3V402.7L86.6 361.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l96 96c12.5 12.5 32.8 12.5 45.3 0l96-96c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 402.7V109.3l41.4 41.4c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-96-96z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <!-- 偏移坐标 -->
                    <div class="flex flex-col gap-1">
                        <TSInput :name="'objectX'" :init="props.init?.objectX" :label="'X'" @tsComCallback="tsComCallback"></TSInput>
                        <TSInput :name="'objectY'" :init="props.init?.objectY" :label="'Y'" @tsComCallback="tsComCallback"></TSInput>
                        <!-- <TSDropInput :name="'objectOpacityDef'" :init="props.init?.objectOpacity" :label="'透明'" :def="props.init?.objectOpacityDef" @tsComCallback="callback"></TSDropInput> -->
                        <TSRangeInput :name="'objectOpacityDef'" :init="props.init?.objectOpacityDef" :label="trans('Opacity')" :max="props.init?.opacityMax" :min="props.init?.opacityMin" :step="props.init?.opacityStep" @tsComCallback="tsComCallback"></TSRangeInput>

                        <div class="join flex justify-start items-center mt-4">
                            <span class="join-item label-text-alt w-10">{{'Crop'}}</span>
                            <div class="join-item flex gap-1">
                                <div class="tooltip tooltip-accent" :data-tip="trans('Crop')">
                                    <svg title="Crop" @click="emit('showCropObject', props.def)" class="dark:hover:fill-slate-50 w-[18px] h-[18px] cursor-pointer" fill="currentColor"  viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M128 32c0-17.7-14.3-32-32-32S64 14.3 64 32V64H32C14.3 64 0 78.3 0 96s14.3 32 32 32H64V384c0 35.3 28.7 64 64 64H352V384H128V32zM384 480c0 17.7 14.3 32 32 32s32-14.3 32-32V448h32c17.7 0 32-14.3 32-32s-14.3-32-32-32H448l0-256c0-35.3-28.7-64-64-64L160 64v64l224 0 0 352z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                    </div>
            
                    
                </div>

            </div>
        </div>
    </div>
</template>