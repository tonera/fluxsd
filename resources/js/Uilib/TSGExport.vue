<script setup>
//图片裁剪
import { reactive, ref } from 'vue';
import 'cropperjs/dist/cropper.min.css'
import TSInput from './TSInput.vue';
import TSRunning from './TSRunning.vue';
import { useForm } from '@inertiajs/vue3';
import { showModalWindow,closeModalWindow,debug} from './TSFunction.js'; 
import {trans} from 'laravel-vue-i18n';
const runningText = ref('');
const props = defineProps(['name', 'init', 'modalId']);
const emit = defineEmits(['tsComCallback']);
const fileType = ref('jpeg');
const custom = reactive({
    label:'custom',
    width: 0,
    height: 0,
    isChecked:false,
});
const form = useForm({
    url: props.init,
    dimension:null,
    fileType:fileType.value,
});



var sizeList = [
    {label:'36✕36px',width:36,height:36, isChecked:false},
    {label:'48✕48px',width:48,height:48, isChecked:false},
    {label:'60✕60px',width:60,height:60, isChecked:false},
    {label:'72✕72px',width:72,height:72, isChecked:false},
    {label:'96✕96px',width:96,height:96, isChecked:false},
    {label:'144✕144px',width:144,height:144, isChecked:false},
    {label:'192✕192px',width:192,height:192, isChecked:false},
    {label:'256✕256px',width:256,height:256, isChecked:false},
    {label:'512✕512px',width:512,height:512, isChecked:false},
    {label:'1024✕1024px',width:1024,height:1024, isChecked:false},
];


//设置选项是否选中
function setChecked(index, value){
    console.log(index, value);
    sizeList[index].isChecked = value;
}
//自定义-设置尺寸
function setDimention(name , value){
    console.log(name, value);
    if(name == 'widht'){
        custom.width = parseInt(value);
    }else if(name == 'height'){
        custom.height = parseInt(value);
    }
}
//自定义-设置是否选中
function setCustom(value){
    custom.isChecked = value;
    console.log(value);
}
//导出
function handleExport(){
    runningText.value = '正在打包导出...';
    showModalWindow('running_page_export');
    
    // sizeList.push(custom);
    let checkedList = [];
    for(let i = 0; i < sizeList.length; i++){
        if(sizeList[i].isChecked){
            checkedList.push(sizeList[i]);
        }
    }
    if(custom.isChecked){
        checkedList.push(custom);
    }
    let json = JSON.stringify(checkedList);
    
    //下载图片
    form.dimension = json;
    form.url = props.init;
    form.fileType = fileType.value;
    // console.log(form);
    // return;
    // router.post('/api/export', form)
    // form.post(route('export'), {
    //     // onFinish: () => form.reset('password', 'password_confirmation'),
    // });

    axios.post('/api/export',
        form,
        { responseType: 'blob' })
        .then(res => {
            console.log("提交成功");
            let blob = new Blob([res.data], { type: res.headers['content-type'] });
            let link = document.createElement('a');
            var disposition = res.headers['content-disposition'];
            // var matches = /"=(.*)"/.exec(disposition);
            var matches = disposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
            var filename = (matches != null && matches[1] ? matches[1] : 'aiposter.zip');
            // console.log("disposition=",disposition, matches,filename);
            // return;
            link.href = window.URL.createObjectURL(blob);
            link.download = filename;
            // document.body.appendChild(link);
            link.click();
            closeModalWindow('running_page_export');
            closeModalWindow(props.modalId);
            // link.remove();
        }).catch(err => {})
    
}

</script>

<template>
    
    <label :for="props.modalId" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</label>
    <div class="flex">
        <div class="h-[650px] w-[600px]">
            <img class="h-[600px] w-[550px] object-cover" :id="'croperImage' + props.modalId" :src="props.init" >
        </div>
        <div class="divider lg:divider-horizontal"></div> 
        <!-- 右侧控制按钮 -->
        <div>
            <div class=" text-sm">
                <label>{{trans('Zoom')}}</label>
                <div v-for="(item,index) in sizeList" :key="index">
                    <div class="flex my-2 justify-center gap-2 items-center">
                        <div class="join-item rounded-none">
                            <input @change="setChecked(index, $event.target.checked)" type="checkbox" :checked="item.isChecked" class="join-item checkbox checkbox-sm" /> 
                        </div>
                        <div class="join-item text-sm w-28">{{item.label}}</div>
                    </div>
                </div>
                <div class="flex my-2 justify-center gap-2 items-center">
                    <div class="join-item rounded-none">
                        <input type="checkbox" :checked="custom.isChecked" @change="setCustom($event.target.checked)" class="join-item checkbox checkbox-sm" /> 
                    </div>
                    <div class="join-item text-sm w-28">{{trans('Custom')}}</div>
                </div>
                <div v-show="custom.isChecked">
                    <TSInput name="width" :init="parseInt(custom.width)" :label="'Width'" :sclass="'w-6'" @tsComCallback="setDimention"></TSInput>
                    <TSInput name="height" :init="parseInt(custom.height)" :label="'Height'" :sclass="'w-6'" @tsComCallback="setDimention"></TSInput>
                </div>
                
            </div>
            <div class="flex flex-row gap-3">
                <div class="form-control">
                <label class="label cursor-pointer gap-2">
                    <span class="label-text">PNG</span> 
                    <input type="radio" name="radio-10" class="radio checked:bg-red-500" value="png" @change="fileType = 'png'" />
                </label>
                </div>
                <div class="form-control">
                <label class="label cursor-pointer gap-2">
                    <span class="label-text">JPEG</span> 
                    <input type="radio" name="radio-10" class="radio checked:bg-blue-500" checked value="jpeg" @change="fileType = 'jpeg'"/>
                </label>
                </div>
            </div>
            <div class="w-full justify-center flex py-5">
                <label class="btn btn-primary btn-sm rounded-full w-32"  @click="handleExport">
                    {{trans('Export')}}
                </label>
            </div>
        </div>
    </div>

    <input type="checkbox" id="running_page_export" class="modal-toggle" />
    <div class="modal" role="dialog">
        <TSRunning :init="runningText" :modal-id="'running_page_export'"></TSRunning>
    </div>
        
</template>