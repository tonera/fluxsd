<script setup>
//图片裁剪
import Cropper from 'cropperjs';
import { onMounted, reactive, watch, ref } from 'vue';
import 'cropperjs/dist/cropper.min.css'
import TSRangeInput from './TSRangeInput.vue';
import TSInput from './TSInput.vue';
const props = defineProps(['name', 'init', 'modalId']);
const emit = defineEmits(['tsComCallback']);
import {trans} from 'laravel-vue-i18n';
// const imageSrc = ref(null);
var image = null;//待编辑图片element
var cropper = null;
var preview = null;

const cropperObject = reactive({
    width: 0,
    height: 0,
    x: 0,
    y: 0,
    rotate: 0,
    scaleX: 1,
    scaleY: 1,
    zoom: 1,
    crop: false,
    cropData: null,
    cropDataUrl: null,
    cropDataBase64: null,
    cropDataBlob: null,
    cropDataFile: null,
});

watch(() => props.init, () => {
 
    // console.log('watch init', props.init?.getSrc());
    if(props.init != null ){
        // console.log('imageSrc 不为空，刷新image');
        if(cropper != null){
            // console.log('旧图片存在，，销毁');
            cropper.destroy();
        }
        initCroper();
    }
});

onMounted(()=>{
    image = document.getElementById('croperImage'+props.modalId);
    preview = document.getElementById('preview');
});

function initCroper(){
    image = document.getElementById('croperImage'+props.modalId);
    image.src = props.init;
    // console.log('init initCroper', image);
    cropper = new Cropper(image, {
        aspectRatio: null,
        // viewMode: 3,
        dragMode: 'move',
        preview: '.preview',
        checkCrossOrigin:false,
        // cropBoxMovable: true,
        // cropBoxResizable: true,
        // toggleDragModeOnDblclick: false,
        // autoCropArea: 0.5,
        // restore: false,
        // guides: false,
        // center: false,
        crop(event) {
                cropperObject.width = parseFloat(event.detail.width);
                cropperObject.height = parseFloat(event.detail.height);
                cropperObject.rotate = event.detail.rotate;
                cropperObject.scaleX = event.detail.scaleX;
                cropperObject.scaleY = event.detail.scaleY;
                cropperObject.x = parseFloat(event.detail.x);
                cropperObject.y = parseFloat(event.detail.y);
            },
    })
}

function tsComCallback(name, value){
    if(cropper == null) return;
    value = parseFloat(value);
    switch(name){
        case 'x':
        cropperObject.x = value;
        console.log("回调x=",name, value, cropperObject.x );
        break;
        case 'y':
        cropperObject.y = value;
        break;
        case 'width':
        cropperObject.width = value;
        break;
        case 'height':
        cropperObject.height = value;
        break;
        case 'rotate':
        cropperObject.rotate = value;
        break;
    }
    // console.log("回调",name, value, cropperObject);
    cropper.setData(cropperObject);
}

function handleCrop(){
    // console.log("crop", cropper.getData());
    emit('tsComCallback',cropperObject);
    
}

</script>

<style>
.preview {
  overflow: hidden;
  width: 160px; 
  height: 160px;
}
</style>

<template>
    
        <label :for="props.modalId" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</label>
        <div class="flex">
            <div class="h-[650px] w-[600px]">
                <img :id="'croperImage' + props.modalId" :src="props.init" >
            </div>
            <div class="divider lg:divider-horizontal"></div> 
            <!-- 右侧控制按钮 -->
            <div>
                <div class="flex justify-center h-44">
                    <div id="preview" class="preview"></div>
                </div>
                <div class="flex flex-col gap-3">
                    <TSInput name="width" :init="parseInt(cropperObject.width)" :label="trans('Width')" :sclass="'w-6'" @tsComCallback="tsComCallback"></TSInput>
                    <TSInput name="height" :init="parseInt(cropperObject.height)" :label="trans('Height')" :sclass="'w-6'" @tsComCallback="tsComCallback"></TSInput>
                    <TSInput name="x" :init="parseInt(cropperObject.x)" :label="'X'" :sclass="'w-6'" @tsComCallback="tsComCallback"></TSInput>
                    <TSInput name="y" :init="parseInt(cropperObject.y)" :label="'Y'" :sclass="'w-6'" @tsComCallback="tsComCallback"></TSInput>
                    <TSRangeInput :name="'rotate'" :init="parseInt(cropperObject.rotate)" :label="trans('Rotate')" :max="360" :min="0" :step="45" @tsComCallback="tsComCallback"></TSRangeInput>
                    
                </div>
                <div class="w-full justify-center flex py-5">
                    <label class="btn btn-primary btn-sm rounded-full w-32"  @click="handleCrop">
                        {{trans('Crop')}}
                    </label>
                </div>
            </div>
        </div>
        
</template>