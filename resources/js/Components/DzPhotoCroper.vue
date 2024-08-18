<script setup>
import { onMounted, reactive, ref, watch, inject} from "vue";
import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.min.css'
import {trans} from 'laravel-vue-i18n'
import TSSlider from './TSSlider.vue';

var cropper;
const props = defineProps(['whiteImage','rbgLoading']);
const currentSizeIndex = ref(1);
const emit = defineEmits(['getCropFile','previousStep','removeBg']);
const rbgLoading = ref(false);
const cropperRotate = ref(0);

const idPhotoStyle = inject('idPhotoStyle');
const cropImgWidth = ref(0);
const cropImgHeight = ref(0);
const option = reactive({
    //aspectRatio: 16 / 9,
    viewMode: 1,
    // 设置图片是否可以拖拽功能
    dragMode: 'move',
    // 是否显示图片后面的网格背景,一般默认为true
    background: true,
    // 进行图片预览的效果
    preview: '.preview',
    // 设置裁剪区域占图片的大小 值为 0-1 默认 0.8 表示 80%的区域
    autoCropArea: 0.6,
    // 设置图片是否可以进行收缩功能
    zoomOnWheel: false,
    // 是否显示 + 箭头
    center: true,
    modal:true,

    crop(event) {
        // console.log(event.detail.x);
        // console.log(event.detail.y);
        // console.log(event.detail.width);
        // console.log(event.detail.height);
        // console.log(event.detail.rotate);
        // console.log(event.detail.scaleX);
        // console.log(event.detail.scaleY);
        cropImgWidth.value = event.detail.width;
        cropImgHeight.value = event.detail.height;
    },

});
onMounted(() => {
    rbgLoading.value = props.rbgLoading;
    
});


watch(()=>props.whiteImage, (e)=>{
    // console.log('whiteImage 发生变化');
    let image = document.getElementById('image');
    image.src = props.whiteImage;
    rbgLoading.value = false;

    if(cropper != undefined){
        cropper.destroy()
    }
    cropper = new Cropper(image, option);

    // if(cropper === undefined){
    //     cropper = new Cropper(image, option);
    // }else{
    //     cropper.replace(image.src);
    // }
    changeBoxSize(currentSizeIndex.value);

    image.addEventListener('ready', function () {
        cropper.crop();
        let cropData = cropper.getData();
        cropImgWidth.value = cropData.width;
        cropImgHeight.value = cropData.height;
        // console.log(cropData);
        // let canvasData = cropper.getImageData();
        //emit('setHeight', parseInt(canvasData.height));
        //cropper.setCanvasData({width:300, height:600});
        // console.log('canvasData.width=',canvasData.width);
    });
});
watch(()=>props.rbgLoading, (e)=>{
    rbgLoading.value = props.rbgLoading;
});


function changeBoxSize(idx){
    currentSizeIndex.value = idx;
    let sizeConfig = idPhotoStyle[idx];
    // console.log(sizeConfig.w, sizeConfig.h, sizeConfig.id);
    if(sizeConfig.id == 0){ //自由裁剪模式
        //cropper.setDragMode('crop');
        cropper.setAspectRatio(null);
    }else{
        cropper.setAspectRatio(sizeConfig.w/sizeConfig.h);
    }
    
    //console.log(currentSizeIndex.value);
}

function changeOption(act, val){
    switch(act){
        case 'zoom':
            cropper.zoom(parseFloat(val));
            break;
        case 'rotate':
            let imgData = cropper.getData();
            cropperRotate.value = val+imgData.rotate;
            cropper.rotate(val);
            break;
    }
}
function tsComCallback(item, val){
    switch(item){
        case 'rotate':
            cropperRotate.value = val;
            cropper.rotateTo(val);
            break;
    }
}

function cropDone(format = 'png'){
    //拿到裁剪后的图片
    let data = cropper.getCroppedCanvas({
        imageSmoothingQuality: 'high',
        imageSmoothingEnabled:true,
    }).toDataURL('image/png',1); // 设置图片格式

    emit('getCropFile',data, idPhotoStyle[currentSizeIndex.value], format);

    // let data = cropper.getCroppedCanvas({
    //     imageSmoothingQuality: 'high',
    //     imageSmoothingEnabled:true,
    // }).toBlob(
    //     blob => {
    //         console.log(blob);
    //         emit('getCropFile',blob, idPhotoStyle[currentSizeIndex.value]);
    //     }, 'image/png', 1);

}

</script>
<style>
.preview {
  overflow: hidden;
  width: 176px; 
  height: 200px;
}
</style>
<template>
<div class="flex w-full justify-center p-3 gap-2" >
    <div class="flex flex-col h-max min-w-[500px] ">
        <div class="max-w-3xl"><img id="image" style="display:block;"></div>
        <!--下排按钮-->
        <div class="pt-2 h-16" v-show="props.whiteImage != ''">
            <button @click="emit('previousStep')"
                type="button"
                class="btn btn-outline btn-success">
                {{$t('Return')}}
            </button>
        </div>
    </div>
    <!--operation btn-->

    <div class="grid grid-cols-2 gap-2 ml-5 h-min border p-2 items-center justify-center place-items-center" v-show="props.whiteImage != ''">
        <div class="col-span-2 text-center grid justify-center">
            <div id="preview" class="preview"></div>
            <div>{{parseInt(cropImgWidth) + ' × ' + parseInt(cropImgHeight)}}</div>
        </div>

        <!--rotate-->
        <div class="col-span-2">
            <TSSlider :name="'rotate'" :label="trans('Rotate')" @tsComCallback="tsComCallback" min="-360" max="360" :def="cropperRotate" :step="1"></TSSlider>
        </div>
        <button
            type="button"
            @click="changeOption('rotate',-90)"
            data-te-ripple-init
            data-te-ripple-color="light"
            class="w-20 flex justify-center rounded bg-primary px-6 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80">
            <svg class="w-[20px] h-[20px]" fill="currentColor" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
            <path d="M48.5 224H40c-13.3 0-24-10.7-24-24V72c0-9.7 5.8-18.5 14.8-22.2s19.3-1.7 26.2 5.2L98.6 96.6c87.6-86.5 228.7-86.2 315.8 1c87.5 87.5 87.5 229.3 0 316.8s-229.3 87.5-316.8 0c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0c62.5 62.5 163.8 62.5 226.3 0s62.5-163.8 0-226.3c-62.2-62.2-162.7-62.5-225.3-1L185 183c6.9 6.9 8.9 17.2 5.2 26.2s-12.5 14.8-22.2 14.8H48.5z"></path>
            </svg>
        </button>
        
    
        <button
            type="button"
            @click="changeOption('rotate',90)"
            data-te-ripple-init
            data-te-ripple-color="light"
            class="w-20 flex justify-center rounded bg-primary px-6 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80">
                <svg class="w-[20px] h-[20px]" fill="currentColor" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                <path d="M386.3 160H336c-17.7 0-32 14.3-32 32s14.3 32 32 32H464c17.7 0 32-14.3 32-32V64c0-17.7-14.3-32-32-32s-32 14.3-32 32v51.2L414.4 97.6c-87.5-87.5-229.3-87.5-316.8 0s-87.5 229.3 0 316.8s229.3 87.5 316.8 0c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0c-62.5 62.5-163.8 62.5-226.3 0s-62.5-163.8 0-226.3s163.8-62.5 226.3 0L386.3 160z"></path>
                </svg>
        </button>
        
       
        <button
            type="button"
            @click="changeOption('zoom',-0.1)"
            data-te-ripple-init
            data-te-ripple-color="light"
            class="w-20 flex justify-center rounded bg-primary px-6 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80">
                <svg class="w-[20px] h-[20px]" fill="currentColor" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                    <path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM136 184c-13.3 0-24 10.7-24 24s10.7 24 24 24H280c13.3 0 24-10.7 24-24s-10.7-24-24-24H136z"></path>

                </svg>
        </button>
    
        <button
            type="button"
            @click="changeOption('zoom',0.1)"
            data-te-ripple-init
            data-te-ripple-color="light"
            class="w-20 flex justify-center rounded bg-primary px-6 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80">
                <svg class="w-[20px] h-[20px]" fill="currentColor" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                    <path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM184 296c0 13.3 10.7 24 24 24s24-10.7 24-24V232h64c13.3 0 24-10.7 24-24s-10.7-24-24-24H232V120c0-13.3-10.7-24-24-24s-24 10.7-24 24v64H120c-13.3 0-24 10.7-24 24s10.7 24 24 24h64v64z"></path>
                </svg>
        </button>
       

        <!--尺寸选择 active:border-gray-800-->
        <div v-for="(btn, index) in idPhotoStyle" :key="'S_'+index">
            <button 
            @click="changeBoxSize(index)" type="button" 
            :class="{'border-gray-800':index==currentSizeIndex}"
            class="text-gray-900 hover:text-white border  hover:bg-gray-900 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-full w-20 text-sm px-5 py-2 text-center me-2 mb-2 dark:border-gray-600 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-800 ">{{btn.label}}</button>
        </div>

        <div class="col-span-2 text-center">
            <button v-if="rbgLoading == false" @click="()=>{emit('removeBg');rbgLoading = true;}" 
                type="button"
                class="text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium rounded-full w-32 text-sm px-5 py-2.5 text-center me-2 mb-2">
                {{$t('Remove BG')}}
            </button>
            <button v-else disabled type="button" class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-full text-sm w-32 px-5 py-2.5 text-center me-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800 inline-flex items-center cursor-pointer">
            <svg aria-hidden="true" role="status" class="inline w-4 h-4 me-3 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB"/>
            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"/>
            </svg>
            {{$t('Removing')}}
            </button>

        </div>

        <div class="col-span-2 text-center mt-5 flex">
            <button @click="cropDone('png')"
                type="button"
                class="text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium rounded-full w-32 text-sm px-5 py-2.5 text-center me-2 mb-2" :disabled="rbgLoading">
                {{$t('ExportPng')}}
            </button>
            <button @click="cropDone('jpeg')"
                type="button"
                class="text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium rounded-full w-32 text-sm px-5 py-2.5 text-center me-2 mb-2" :disabled="rbgLoading">
                {{$t('ExportJpeg')}}
            </button>
        </div>

        
        


    </div>

</div>

</template>