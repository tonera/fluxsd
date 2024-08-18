<script setup>
import { onMounted, reactive, ref, watch, inject} from "vue";
import 'fabric';
import 'fabric-with-erasing';
import { showTopSnackbar } from "@/network";
import {trans} from 'laravel-vue-i18n'

var canvas;
const idPhotoStyle = inject('idPhotoStyle');
// var canvasWidth = 1498.6; //1498.6 127mm
// var canvasHeight = 1050.2; //1050.2 89mm
const rate = ref(0.5);
const props = defineProps(['cropedImage', 'sizeConfig']);
const photo = ref(null);
const state = reactive({
    margin:50, //边距5mm
    padding:40,//间距4mm
    canvasWidth:1498.6,
    canvasHeight:1050.2,
    bgColor:'rgba(255, 255, 255, 1)'
});

onMounted(()=>{
    state.margin = state.margin * rate.value;
    state.padding = state.padding * rate.value;
    state.canvasWidth = state.canvasWidth * rate.value;
    state.canvasHeight = state.canvasHeight * rate.value;

    canvas = new fabric.Canvas('canvas',{
            width: state.canvasWidth,
            height:state.canvasHeight
        });
    //console.log('相纸'+state.canvasWidth, state.canvasHeight);
    canvas.setBackgroundColor('rgba(255, 255, 255, 1)', canvas.renderAll.bind(canvas));
    //canvas.setOverlayColor("rgba(0,0,0,0)", undefined, { erasable: false });
});
watch(()=>props.cropedImage, (e)=>{
    init();
});


function init(){
    //清除所有对象
    canvas.clear();
    canvas.setBackgroundColor('rgba(255, 255, 255, 1)', canvas.renderAll.bind(canvas));
    var imgObj = new Image();
    imgObj.src = props.cropedImage;
    imgObj.onload = function () {
        let imageModel = new fabric.Image(imgObj);
        
        //计算缩放比例:从配置表中取出宽度与当前图像宽度进行比较得出缩放比例
        let scalingRate = props.sizeConfig.w * 11.8 / imgObj.width * rate.value;
        //console.log('缩放比='+ scalingRate);

        let imgObjWidth = imgObj.width * scalingRate;
        let imgObjHeight = imgObj.height * scalingRate;
        //console.log(imgObjWidth ,imgObjHeight);

        //将用户裁剪后的图片存储起来
        photo.value = imageModel;
        
        let res = getImgtypoInfo(state.canvasWidth , state.canvasHeight, imgObjWidth, imgObjHeight);
        //console.log(res);
        //如果res[6]==false则说明要横着排，由于fabric旋转270度时图片也转向上方一个图的尺寸。所以top值要加上一个图片的旋转边
        let addVal = 0;
        let angleVal = 0;
        if(res[6] == false){
            addVal = imgObjWidth;
            angleVal = 270;
        }

        if(res !== false){
            let total = res[0] * res[1];
            //console.log('可生成'+(res[0] * res[1])+"张图片."+res[0]+" * "+res[1]);
            //console.log('每张照片'+ res[2], res[3]);
            for (let index = 1; index <= total; index++) {
                let imgItem = typograpy(res[0], res[1],res[2], res[3],res[4], res[5],index);
                //let imgItem = typograpy(res[0], res[1],imgObjWidth , imgObjHeight ,res[4], res[5],index);
                imageModel.clone((imageModel) => {
                    canvas.add(
                        imageModel.set({
                            left: imgItem[2],
                            top: imgItem[3]+addVal,
                            opacity: 1,
                            angle: angleVal,
                            originX: 'left',
                            originY: 'top',
                            erasable:true,
                            width:imgObj.width,
                            height:imgObj.height,
                            lockMovementX : true,
                            lockMovementY : true,
                            lockScalingX : true,
                            lockScalingY : true,
                            lockRotation : true,
                            hasControls : false,
                            hasBorders : false,
                            backgroundColor:state.bgColor,
                        }).scale(scalingRate)
                    );
                    canvas.renderAll();
                });
            }
        }else{
            showTopSnackbar(trans('Calculation error, unable to generate ID photo'), 'error');
        }
        
    };
}

//求图片可以排版多少个
function getImgtypoInfo(cw, ch, pw, ph){
    //高大于相约任意边
    if(ph > cw && ph > ch){
        return false;
    }
    //或宽大于相纸任意边
    if(pw > cw && pw > ch){
        return false;
    }
    //如果间距比边距还要宽，则算出差值，要预留 maring是边距 
    let distance = 0;
    if(state.padding > state.margin){
        distance = state.padding - state.margin;
        state.margin = state.padding;//间距大，则边距=间距
    }else{
        distance = state.margin;
    }
    //state.margin = state.margin < state.padding ? state.padding :state.margin;
    //console.log('cw='+cw, 'ch='+ch, 'pw='+pw, 'ph='+ph, 'idx='+idx , 'distance='+distance);
    //照片按宽排试试
    //先计算行数
    let rows1 = Math.floor((ch)/(ph+state.padding*2));
    let cols1 = Math.floor((cw)/(pw+state.padding*2));
    let total1 = rows1 * cols1;//此方案可得照片张数

    //console.log('横排row='+rows1,'col='+cols1,'total='+total1, 'padding='+state.padding, 'margin='+state.margin,'pw='+pw, 'ph='+ph);
    //console.log(cw-distance*2 , pw+state.padding*2);

    //照片按个方式排版
    let rows2 = Math.floor((ch)/(pw+state.padding*2));
    let cols2 = Math.floor((cw)/(ph+state.padding*2));
    let total2 = rows2 * cols2;
    
    if((rows1 == 0) && rows2 == 0){
        return false;
    }
    if(cols1 == 0 && cols2 == 0){
        return false;
    }
    //console.log('竖排row='+rows2,'col='+cols2,'total='+total2, 'padding='+state.padding, 'margin='+state.margin);
    //console.log('row='+rows2,'col='+cols2,'total='+total2);
    //console.log(cw-distance*2 , ph+state.padding*2);

    let stepSizeCol = 0;
    let stepSizeRow = 0;
    let row = 0;
    let col =0 ;
    //每个实体被分配的宽高-图片宽高差值，以免排版时出现顶边情况
    let entryw  = 0;//没必要
    let entryh = 0;
    let isVertical = false;//横向还是纵向排列
    //谁大选用谁的边长开始排版
    if( total1 >= total2){
        stepSizeCol = (cw) / cols1;
        stepSizeRow = (ch ) / rows1;
        row = rows1;
        col = cols1;
        entryw = (stepSizeCol - pw);//不需要除以2
        entryh = (stepSizeRow - ph);
        isVertical = true;
    }else{
        stepSizeCol = (cw) / cols2;
        stepSizeRow = (ch) / rows2;
        row = rows2;
        col = cols2;
        entryw = (stepSizeCol - ph);
        entryh = (stepSizeRow - pw);
        isVertical = false;
    }
    
    //返回行数、列数、每行步长，每列步长，行留白，列留白
    return [row, col, stepSizeRow , stepSizeCol, entryw, entryh, isVertical];
}

//排版,给定一个长宽尺寸 横还是竖排 照片尺寸 ，第几个号位置，给出其坐标
//cw ch相纸尺寸 pw ph图片尺寸 idx第几个照片
function typograpy(row, col, stepSizeRow, stepSizeCol,  entryw, entryh,idx){
    //超过最大可排版数
    if(idx > (col*row)){
        return false;
    }
    //console.log('选择方案row='+row,'col='+col,'stepSizeRow='+stepSizeRow, 'stepSizeCol='+stepSizeCol,'entryw='+entryw,'entryh='+entryh);
    //求出指定idx的照片在第几行第几列坐标
    let prow = Math.ceil(idx/col);//行数从1开始
    let pcol = (idx-1)%col;//列数从0开始
    //top=行数 left=列数-1
    let pleft = parseInt(stepSizeCol * (pcol) + entryw/2);
    let ptop = parseInt(stepSizeRow * (prow-1) + entryh/2);

    //console.log(entryw,entryh);
    
    //console.log('图片行数='+prow,'col='+pcol,'pleft='+pleft, 'ptop='+ptop);
    
    return [prow, pcol, pleft, ptop];
}

//test func
function count(){
    // let res = getImgtypoInfo(127, 89, 25,35);
    // console.log(res);
    // let ddd = typograpy(res[0], res[1],res[2], res[3],res[4], res[5],1);
    // //console.log(ddd);
    // init();
}

function changeBgColor(color){
    canvas.forEachObject((obj)=>{
        //obj.setBackgroundColor('rgba(255, 0, 0, 1)', canvas.renderAll.bind(canvas));
        obj.set({
            backgroundColor:color
        });
    });
    state.bgColor = color;
    canvas.renderAll();
}

function setOptions(option, val){
    switch(option){
        case 'margin':
            state.margin = state.margin * val;
            break
        case 'padding':
            state.padding = state.padding * val;
            break;
    }
    init();
    // console.log('设置参数',option,val , '间距='+state.padding , '边距='+state.margin);
}

function download() {
    const ext = "png";
    if(canvas === undefined){
        //console.log('模板为空,无法导出');
        return;
    }
    const base64 = canvas.toDataURL({
        format: ext,
        enableRetinaScaling: true
    });
    // const fullQuality = canvas.toDataURL("image/jpeg", 1.0);
    const link = document.createElement("a");
    link.href = base64;
    link.download = `eraser_example.${ext}`;
    link.click();
};


</script>
<template>
<div class="flex w-full justify-center">

<!--左侧图片排版区-->
<div class=" bg-slate-200 p-2">
    <canvas id="canvas" width="749.3" height="525.1"></canvas>
</div>

<!--右侧操作区-->
<div class="grid grid-cols-2 gap-2 w-44 ml-5 h-min">
    <div>
        <button @click="changeBgColor('rgba(255, 0, 0, 1)')" type="button" class="inline-block rounded bg-red-500 text-neutral-50 shadow-[0_4px_9px_-4px_rgba(51,45,45,0.7)] hover:bg-red-600 hover:shadow-[0_8px_9px_-4px_rgba(51,45,45,0.2),0_4px_18px_0_rgba(51,45,45,0.1)] focus:bg-red-800 focus:shadow-[0_8px_9px_-4px_rgba(51,45,45,0.2),0_4px_18px_0_rgba(51,45,45,0.1)] active:bg-red-700 active:shadow-[0_8px_9px_-4px_rgba(51,45,45,0.2),0_4px_18px_0_rgba(51,45,45,0.1)] px-6 pb-6 pt-6 text-xs font-medium uppercase leading-normal transition duration-150 ease-in-out focus:outline-none focus:ring-0">{{$t('Red BG')}}</button>
    </div>
    <div>
        <button @click="changeBgColor('rgba(67, 142, 219, 1)')"  type="button" class="inline-block rounded bg-blue-500 text-neutral-50 shadow-[0_4px_9px_-4px_rgba(51,45,45,0.7)] hover:bg-blue-600 hover:shadow-[0_8px_9px_-4px_rgba(51,45,45,0.2),0_4px_18px_0_rgba(51,45,45,0.1)] focus:bg-blue-800 focus:shadow-[0_8px_9px_-4px_rgba(51,45,45,0.2),0_4px_18px_0_rgba(51,45,45,0.1)] active:bg-blue-700 active:shadow-[0_8px_9px_-4px_rgba(51,45,45,0.2),0_4px_18px_0_rgba(51,45,45,0.1)] px-6 pb-6 pt-6 text-xs font-medium uppercase leading-normal transition duration-150 ease-in-out focus:outline-none focus:ring-0">{{$t('Blue BG')}}</button>
    </div>
    <div>
        <button @click="changeBgColor('gray')"  type="button" class="inline-block rounded bg-zinc-500 text-neutral-50 shadow-[0_4px_9px_-4px_rgba(51,45,45,0.7)] hover:bg-zinc-600 hover:shadow-[0_8px_9px_-4px_rgba(51,45,45,0.2),0_4px_18px_0_rgba(51,45,45,0.1)] focus:bg-zinc-800 focus:shadow-[0_8px_9px_-4px_rgba(51,45,45,0.2),0_4px_18px_0_rgba(51,45,45,0.1)] active:bg-zinc-700 active:shadow-[0_8px_9px_-4px_rgba(51,45,45,0.2),0_4px_18px_0_rgba(51,45,45,0.1)] px-6 pb-6 pt-6 text-xs font-medium uppercase leading-normal transition duration-150 ease-in-out focus:outline-none focus:ring-0">{{$t('Gray BG')}}</button>
    </div>
    <div>
        <button @click="changeBgColor('white')"  type="button" class="inline-block rounded border-2 border-blue-500 text-blue-500 hover:border-blue-600 hover:bg-blue-400 hover:bg-opacity-10 hover:text-blue-600 focus:border-blue-700 focus:text-blue-700 active:border-blue-800 active:text-blue-800 dark:border-blue-300 dark:text-blue-300 dark:hover:hover:bg-blue-300 px-6 pb-5 pt-6 text-xs font-medium uppercase leading-normal transition duration-150 ease-in-out focus:outline-none focus:ring-0">{{$t('White BG')}}</button>
    </div>
    <!--调用间距和行距-->
    <div class="col-span-2 flex items-center gap-x-2 pt-8">
        <div>
            <label class="block text-sm font-medium dark:text-white">{{$t('Margin')}}</label>
        </div>
        <button
            type="button" 
            @click="setOptions('margin', 0.9)"
            class="flex justify-center rounded-full bg-primary px-0 pb-0 pt-0 text-xs font-medium uppercase leading-normal text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18 12H6" />
            </svg>
        </button>
        <input type="text" class="py-2 w-12 border-gray-200 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400" placeholder="" v-model="state.margin">
        <button
            type="button"
            @click="setOptions('margin', 1.1)"
            class="flex justify-center rounded-full bg-primary px-0 pb-0 pt-0 text-xs font-medium uppercase leading-normal text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
            </svg>
        </button>
    </div>
    <div class="col-span-2 flex items-center gap-x-2">
        <label class="block mb-2 text-sm font-medium dark:text-white">{{$t('Spacing')}}</label>
        <button
            type="button"
            @click="setOptions('padding', 0.9)"
            class="flex justify-center rounded-full bg-primary px-0 pb-0 pt-0 text-xs font-medium uppercase leading-normal  text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18 12H6" />
            </svg>
        </button>
        <input type="text" class="py-2 w-12 border-gray-200 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400" placeholder="" v-model="state.padding">
        <button
            type="button"
            @click="setOptions('padding', 1.1)"
            class="flex justify-center rounded-full bg-primary px-0 pb-0 pt-0 text-xs font-medium uppercase leading-normal text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
            </svg>
        </button>
    </div>

    <div class="col-span-2 text-center mt-5">
            <button @click="download()"
                type="button"
                class="w-32 inline-block rounded-full bg-success px-6 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80">
                {{$t('Download')}}
            </button>
        </div>

</div>

</div>
    
</template>