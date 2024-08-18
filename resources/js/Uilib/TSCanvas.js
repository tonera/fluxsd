import { reactive } from 'vue'
import {oTask, oImage} from './TSState.js'; 
import {ANGLELIST, LINEHEIGHTLIST, FONTSIZELIST,CHARSPACINGLIST, SHADOWOFFSETLIST,TPL_Photo,TPL_Print,TPL_Illustration} from './TSConstant.js'; 
import {showTopSnackbar, debug} from './TSFunction.js'; 
import { fabric } from 'fabric';
var f = fabric.Image.filters;

// import { trans } from 'laravel-vue-i18n';
//ts公共方法: canvas操作专用
export const keysMap = {
    'objectWidth':'width',
    'objectHeight':'height','objectX':'left','objectY':'top','objectAngleDef':'angle','objectOpacityDef':'opacity',
    'fontFamilyDef':'fontFamily','fontColor':'fill','lineHeightDef':'lineHeight','charSpacingDef':'charSpacing','fontSizeDef':'fontSize','textAlign':'textAlign','fontBackground':'textBackgroundColor','color':'color',
    'shadowXDef':'offsetX','shadowYDef':'offsetY','shadowBlurDef':'blur','strokeWidth':'strokeWidth','stroke':'stroke',
    'skewX':'skewX','skewY':'skewY','flipX':'flipX','flipY':'flipY'
};
export const filterList = reactive({
    Grayscale:{checked:false ,subType:'radio',val:'average',options:[
        {'label':'avt',val:'average'},{label:'lum', val:'luminosity'},{label:'light',val:'lightness'}]
    },
    Invert:{checked:false ,subType:null},
    Sepia:{checked:false ,subType:null},
    BlackWhite:{checked:false ,subType:null},
    Brownie:{checked:false ,subType:null},
    Vintage:{checked:false ,subType:null},
    Kodachrome:{checked:false ,subType:null},
    Technicolor:{checked:false ,subType:null},
    Polaroid:{checked:false ,subType:null},
});
export const EfectsList = reactive({
    RemoveColor:{checked:false ,subType:'colorRange',color:'rgba(0, 0, 0, 0)',val:0.5,min:0,max:1,step:0.02},
    Brightness:{checked:false ,subType:'range',val:0,min:-1,max:1,step:0.01},
    Gamma:{checked:false ,subType:'MultiRange',color:'rgba(0, 0, 0, 0)'},//显示色盘选择红黄蓝
    Contrast:{checked:false ,subType:'range',val:0,min:-1,max:1,step:0.01},
    Saturation:{checked:false ,subType:'range',val:0,min:-1,max:1,step:0.01},
    Vibrance:{checked:false ,subType:'range',val:0,min:-1,max:1,step:0.01},
    Noise:{checked:false ,subType:'range',val:100,min:0,max:1000,step:1},
    Pixelate:{checked:false ,subType:'range',val:4,min:2,max:20,step:1},
    Blur:{checked:false ,subType:'range',val:0.1,min:0,max:1,step:0.01},
    Sharpen:{checked:false ,subType:null},
    Emboss:{checked:false ,subType:null},
    // BlendColor:{checked:false ,subType:'MultiModeRange',color:'rgba(0, 0, 0, 0)',val:'add',mode:[
    //     {'label':'add',val:'add'},
    //     {'label':'diff',val:'diff'},
    //     {'label':'subtract',val:'subtract'},
    //     {'label':'multiply',val:'multiply'},
    //     {'label':'screen',val:'screen'},
    //     {'label':'lighten',val:'lighten'},
    //     {'label':'darken',val:'darken'},
    //     {'label':'overlay',val:'overlay'},
    //     {'label':'exclusion',val:'exclusion'},
    //     {'label':'tint',val:'tint'},
    // ]},
    // BlendImage:{subType:'MultiImageRange',color:'rgba(0, 0, 0, 0)',mode:'add'},
});
var indexMaps = getFilterIndexMap();
export const paramState = reactive({
    opacityMin:0,
    opacityMax:1,
    opacityStep:0.01,

    isObjectLink:false,//对象宽高比约束
    objectWidth:0,
    objectHeight:0,
    objectX:0,
    objectY:0,
    objectAngle:ANGLELIST,
    objectAngleDef:0,
    objectOpacityDef:1,
    scaleX :1,
    scaleY :1,

    fontColor:'rgba(0, 0, 0, 0)',
    fontBackground:'rgba(0, 0, 0, 0)',
    fontFamily:[],
    fontFamilyDef:'Arial',
    fontSize:FONTSIZELIST,
    fontSizeDef:6,
    lineHeight:LINEHEIGHTLIST,
    lineHeightDef:2,
    charSpacing:CHARSPACINGLIST,
    charSpacingDef:2,

    textAlign:'left',

    color: 'rgba(0, 0, 0, 0)',
    shadowX:SHADOWOFFSETLIST,
    shadowXDef:0,
    shadowY:SHADOWOFFSETLIST,
    shadowYDef:0,
    shadowBlurDef:0,
  
    strokeWidth:0,
    stroke:'rgba(0, 0, 0, 0)',

    // opacity:40,
    //图片偏转度
    skewX:0,
    skewY:0,
    flipX:false,
    flipY:false,

    selectable:true,
    visible:true,
    // backgroundColor:backgroundColor,

});

export const fontCheck = new Set([
    // Windows 10
    'Arial', 'Arial Black', 'Bahnschrift', 'Calibri', 'Cambria', 'Cambria Math', 'Candara', 'Comic Sans MS', 'Consolas', 'Constantia', 'Corbel', 'Courier New', 'Ebrima', 'Franklin Gothic Medium', 'Gabriola', 'Gadugi', 'Georgia', 'HoloLens MDL2 Assets', 'Impact', 'Ink Free', 'Javanese Text', 'Leelawadee UI', 'Lucida Console', 'Lucida Sans Unicode', 'Malgun Gothic', 'Marlett', 'Microsoft Himalaya', 'Microsoft JhengHei', 'Microsoft New Tai Lue', 'Microsoft PhagsPa', 'Microsoft Sans Serif', 'Microsoft Tai Le', 'Microsoft YaHei', 'Microsoft Yi Baiti', 'MingLiU-ExtB', 'Mongolian Baiti', 'MS Gothic', 'MV Boli', 'Myanmar Text', 'Nirmala UI', 'Palatino Linotype', 'Segoe MDL2 Assets', 'Segoe Print', 'Segoe Script', 'Segoe UI', 'Segoe UI Historic', 'Segoe UI Emoji', 'Segoe UI Symbol', 'SimSun', 'Sitka', 'Sylfaen', 'Symbol', 'Tahoma', 'Times New Roman', 'Trebuchet MS', 'Verdana', 'Webdings', 'Wingdings', 'Yu Gothic',
    // macOS
    'American Typewriter', 'Andale Mono', 'Arial', 'Arial Black', 'Arial Narrow', 'Arial Rounded MT Bold', 'Arial Unicode MS', 'Avenir', 'Avenir Next', 'Avenir Next Condensed', 'Baskerville', 'Big Caslon', 'Bodoni 72', 'Bodoni 72 Oldstyle', 'Bodoni 72 Smallcaps', 'Bradley Hand', 'Brush Script MT', 'Chalkboard', 'Chalkboard SE', 'Chalkduster', 'Charter', 'Cochin', 'Comic Sans MS', 'Copperplate', 'Courier', 'Courier New', 'Didot', 'DIN Alternate', 'DIN Condensed', 'Futura', 'Geneva', 'Georgia', 'Gill Sans', 'Helvetica', 'Helvetica Neue', 'Herculanum', 'Hoefler Text', 'Impact', 'Lucida Grande', 'Luminari', 'Marker Felt', 'Menlo', 'Microsoft Sans Serif', 'Monaco', 'Noteworthy', 'Optima', 'Palatino', 'Papyrus', 'Phosphate', 'Rockwell', 'Savoye LET', 'SignPainter', 'Skia', 'Snell Roundhand', 'Tahoma', 'Times', 'Times New Roman', 'Trattatello', 'Trebuchet MS', 'Verdana', 'Zapfino',
].sort());

export function getFilterIndexMap(){
    let indexMaps = {};
    let keys = Object.keys(filterList);
    keys.forEach((value, key) => {
        indexMaps[value] = key;
    });
    Object.keys(EfectsList).forEach((value, key) => {
        indexMaps[value] = key;
    });
   return indexMaps;
}

//将选择的对象属性同步到编辑器
export function syncProperties(canvas){
    debug('将选择的对象属性同步到编辑器');
    // let newMap = {}
    //     keysMap.forEach((value, key) => {
    //         newMap[value] = key;
    // });
    // console.log(newMap);
    console.log(checkCanvasObject('object'));
    // if(checkCanvasObject('object') == false) return;
    let cItem = canvas.getActiveObject();
    if(cItem == null) return;
    debug('Active object type = ',cItem.type);
    // console.log("对象原始值:",cItem);
    
    //更新对象通用属性 w h x y angle opacity
    // console.log(keysMap)
    Object.keys(keysMap).forEach(key => {
        // console.log(`Property: ${key}, Value: ${keysMap[key]}`);
        let orgKey = keysMap[key];
        if('objectWidth' == key){
            paramState[key] = (cItem.get(orgKey) * cItem.get('scaleX')).toFixed(0);
        }else if('objectHeight' == key){
            paramState[key] = (cItem.get(orgKey) * cItem.get('scaleY')).toFixed(0);
        }else if (['objectX','objectY','angle'].includes(key)){
            paramState[key] = cItem.get(orgKey).toFixed(0);
        }else if (['color','shadowBlurDef','shadowXDef','shadowYDef'].includes(key)){
            let shadow = cItem.get('shadow');
            // console.log('shadow=',shadow);
            if(shadow != null){
                // console.log('shadow=',key,'=',shadow[orgKey]);
                paramState[key] = shadow[orgKey];
            }else{
                if(key == 'color'){
                    paramState[key] = 'rgba(0,0,0,0)';
                }else{
                    paramState[key] = 0;
                }
            }
        }else{
            paramState[key] = cItem.get(orgKey);
        }
    });
    console.log(paramState);
}

//检测canvas对象是否存在
export function checkCanvasObject(canvas, object){
    if( object == 'canvas'){
        if(canvas == null){
            console.log("Canvas is null");
            showTopSnackbar("Canvas must be created first.");
            return false;
        }
    }else if( object == 'object'){
        if(canvas == null){
            console.log("Canvas is null");
            showTopSnackbar("Canvas must be created first.");
            return false;
        }
        let obj = canvas.getActiveObject() ;
        if(obj == null){
            console.log("Activate object is null");
            showTopSnackbar("You must to select an object.");
            return false;
        }
    }
    return true;
}

export function canvasAddImage(canvas,image, type='url'){
    // console.log('添加图片',image, type);
    if(checkCanvasObject(canvas,'canvas') == false) return;
    if(image == null || image == ''){
        // console.log('图片为空');
        return;
    }
    if(type == 'url'){
        fabric.Image.fromURL(image, function (img) {    
            if(img.width > canvas.getWidth()){
                img.scale(canvas.getWidth()/img.width);
                // img.scaleToHeight(canvas.getHeight());
            }
            
            canvas.add(img);
            canvas.setActiveObject(img);
        }, {crossOrigin: 'anonymous'});
    }
}
export function addText(canvas){
    if(checkCanvasObject(canvas,'canvas') == false) return;
    let text = new fabric.IText('Text', { 
        fontFamily: 'arial black',
        left: 100, 
        top: 100 ,
        });
    canvas.add(text);
    // console.log(text.type);
}
export function canvasAddSvg(canvas, url){
    debug("添加svg对象:", url);
    fabric.loadSVGFromURL(url, function(objects, options) {
        var obj = fabric.util.groupSVGElements(objects, options);
        canvas.add(obj).renderAll();
      });
    
}
export function addShapes(canvas, shape){
    if(checkCanvasObject(canvas,'canvas') == false) return;
    var shapeObject = null;
    let oLeft = canvas.getWidth()/2;
    let oTop = canvas.getHeight()/2;
    console.log("添加图形",shape);
    switch(shape){
        case 'Rect':
            shapeObject = new fabric.Rect({
                left: oLeft,
                top: oTop,
                fill: 'red',
                width: 100,
                height: 100
            });
        break;
        case 'Circle':
            shapeObject = new fabric.Circle({
                radius: 20, fill: 'yellow', left: oLeft, top: oTop
              });
        break;
        case 'Triangle':
            shapeObject = new fabric.Triangle({
                width: 20, height: 30, fill: 'blue', left: oLeft, top: oTop
              });
        break;
        case 'Line':
            shapeObject = new fabric.Line([50, 50, 200, 100], {
                stroke: 'blue',
                strokeWidth: 2, // 必有欄位
                top: oTop,
                left: oLeft
              });
        break;
        case 'Ellipse':
            shapeObject = new fabric.Ellipse({
                rx: 40, // 必有欄位
                ry: 30, // 必有欄位
                top: oTop,
                left: oLeft,
                fill: 'green'
              })
        break;
        default:
            return;
    }
    console.log(shapeObject.type);
    canvas.add(shapeObject); 
    canvas.renderAll();
}
//将目标对象移动到指定层
export function canvasMoveObject(canvas, oldIndex, newIndex){
    if(checkCanvasObject(canvas,'canvas') == false) return;
    // console.log("moveObject from", oldIndex, newIndex);
    let obj = canvas.item(oldIndex);
    obj.moveTo(newIndex);
    
    canvas.setActiveObject(obj);
    return obj;
}

//组件显示与锁定回调
export function canvasLockShowCallback(canvas, name, value, index){
    // console.log(name, value, index);
    if(checkCanvasObject(canvas,'canvas') == false) return;
    let obj = canvas.item(index);
    if(obj == null) return;
    obj.set(name, value);
    if(value == true){
        // canvasSetActiveObject(canvas,index);
        canvas.setActiveObject(obj);
        syncProperties(canvas);
        syncObjectFilterStatus(canvas);
        canvas.renderAll();
    }else{
        canvas.discardActiveObject();
    }
    canvas.renderAll();
}

//setActiveObject(object, eopt) → {fabric.Canvas}
// export function canvasSetActiveObject(canvas,index){
//     // console.log('设置当前活跃对象', index);
//     if(index == null) return;
//     if(checkCanvasObject('object') == false) return;
//     let obj = canvas.item(index);
//     canvas.setActiveObject(obj);
    
//     syncProperties(canvas);
//     syncObjectFilterStatus(canvas);
//     canvas.renderAll();
//     return obj;
//     // console.log('当前活跃对象', canvas.getActiveObject());
//     // canvas.setZoom(0.4);
// }

//同步对象的滤镜状态
export function syncObjectFilterStatus(canvas){
    // if(checkCanvasObject(canvas,'canvas') == false) return;
    //canvas.getActiveObject().filters[i])
    let currentObject = canvas.getActiveObject();
    if(currentObject == null) return;
    if(currentObject.type != 'image'){
        // console.log("此对象不是图片，不能使用滤镜");
        return;
    }
    // console.log(currentObject); EfectsList filterList
    Object.keys(indexMaps).forEach((value, key) => {
        if(Object.keys(filterList).includes(value)){
            filterList[value].checked = !!currentObject.filters[key];
        }
        if(Object.keys(EfectsList).includes(value)){
            EfectsList[value].checked = !!currentObject.filters[key];
        }
        // console.log('key = ',key, 'value =',value, 'filterList[value].checked =',filterList[value].checked );
    });
}

//删除对象
export function canvasDeleteObject(canvas, index){
    // console.log("删除对象 index=",index);
    if(checkCanvasObject(canvas,'canvas') == false) return;
    canvas.remove(canvas.item(index));
}

// 缩放画布-所有对象一起缩放
export function canvasZoom(canvas, value){
    if(checkCanvasObject(canvas,'canvas') == false) return;
    // console.log("zoom ", value);
    // scaleRatio = Math.min(containerWidth/canvasWidth, containerHeight/canvasHeight);
    let scaleRatio = value;
    canvas.setDimensions({ width: (canvas.getWidth() * scaleRatio), height: (canvas.getHeight() * scaleRatio) });
    // currentCanvas.value.width = canvas.getWidth();
    // currentCanvas.value.height = canvas.getHeight();
    // canvas.setZoom(scaleRatio);
    if (canvas.backgroundImage) {
        //var bi = canvas.backgroundImage;
        console.log('canvas.backgroundImage=',canvas.backgroundImage.scaleX,canvas.backgroundImage.scaleY);
        // canvas.backgroundImage.scaleToWidth(canvas.getWidth());
        // canvas.backgroundImage.scaleToHeight(canvas.getHeight());
        canvas.backgroundImage.scaleX = canvas.backgroundImage.scaleX * scaleRatio;
        canvas.backgroundImage.scaleY = canvas.backgroundImage.scaleY * scaleRatio;
        canvas.backgroundImage.setCoords();
    }
    var objects = canvas.getObjects();
    for (var i in objects) {
        var scaleX = objects[i].scaleX;
        var scaleY = objects[i].scaleY;
        var left = objects[i].left;
        var top = objects[i].top;

        var tempScaleX = scaleX * scaleRatio;
        var tempScaleY = scaleY * scaleRatio;
        var tempLeft = left * scaleRatio;
        var tempTop = top * scaleRatio;

        objects[i].scaleX = tempScaleX;
        objects[i].scaleY = tempScaleY;
        objects[i].left = tempLeft;
        objects[i].top = tempTop;

        objects[i].setCoords();
    }
    canvas.renderAll();
    canvas.calcOffset();
}

//添加或者移除背景图片
export function canvasSetBackgroundImage(canvas, isAdd){
    // console.log("设置/删除背景",isAdd);
    if(checkCanvasObject(canvas,'canvas') == false) return;
    let bgUrl = null;
    if(isAdd){
        if(checkCanvasObject(canvas,'object') == false) return;
        let activeObj = canvas.getActiveObject();
        console.log("设置背景",activeObj);
        let rate = Math.max(canvas.width / activeObj.width, canvas.height / activeObj.height);
        console.log("rate max = ",rate,activeObj.width,activeObj.height,activeObj.scaleX, activeObj.scaleY);
        console.log("rate min = ",Math.min(canvas.width / (activeObj.width * activeObj.scaleX), canvas.height / (activeObj.height * activeObj.scaleY)));
        //设置一个图片对象为背景图
        bgUrl = activeObj.getSrc();
        canvas.setBackgroundImage(activeObj, canvas.renderAll.bind(canvas), {
            // scaleX: canvas.width / activeObj.width,
            // scaleY: canvas.height / activeObj.height
            left:0,
            top:0,
            scaleX:rate+0.01,//fabric在序列化时会自动保留两位小数，导致背景可能会丢失一些像素尺寸
            scaleY:rate+0.01,
        });        
        canvas.remove(activeObj);
        // canvas.setActiveObject(false);
    }else{
        if(checkCanvasObject(canvas,'canvas') == false) return;
        //移除背景
        let obj = canvas.backgroundImage;
        // console.log('背景对象=',obj);
        if(obj == null) return false;
        canvas.add(obj);
        canvas.setBackgroundImage(false,canvas.renderAll.bind(canvas));
    }
    canvas.renderAll();
    console.log("保存时背景",canvas.backgroundImage);
    return bgUrl;
}


//滤镜回调
export function canvasCallbackFilters(canvas, name, val){
    console.log('滤镜回调:name =',name, 'val =', val);
    if(checkCanvasObject(canvas,'object') == false) return;
    var obj = canvas.getActiveObject();
    if(obj.type != 'image'){
        showTopSnackbar("Object is not an image", 'info');
        console.log('当前操作的对象不是图片',obj.type);
        return;
    }
    
    let filter = false;
    switch(name){
        case 'Grayscale':
            filter = (val.isChecked && new f.Grayscale());
            if(filter != false){
                filter['mode'] = val.val;
            }
            break;
        case 'Invert':
            filter = (val.isChecked && new f.Invert());
            break;
        case 'Sepia':
            filter = (val.isChecked && new f.Sepia());
            break;
        case 'BlackWhite':
            filter = (val.isChecked && new f.BlackWhite());
            break;
        case 'Brownie':
            filter = (val.isChecked && new f.Brownie());
            break;
        case 'Vintage':
            filter = (val.isChecked && new f.Vintage());
            break;
        case 'Kodachrome':
            filter = (val.isChecked && new f.Kodachrome());
            break;
        case 'Technicolor':
            filter = (val.isChecked && new f.Technicolor());
            break;
        case 'Polaroid':
            filter = (val.isChecked && new f.Polaroid());
            break;
        case 'RemoveColor':
            filter = (val.isChecked && new f.RemoveColor({
                distance: parseFloat(val.val),
                color: val.color,
            }));
            break;
        case 'Brightness':
            filter = (val.isChecked && new f.Brightness({
                brightness: parseFloat(val.val),
            }));
            break;
        case 'Gamma':
            //todo 从颜色表里取 val.color
            var v1 = 0.1;
            var v2 = 0.1;
            var v3 = 0.1;
            filter = (val.isChecked && new f.Gamma({
                gamma: [v1, v2, v3]
            }));
            break;
        case 'Contrast':
            filter = (val.isChecked && new f.Contrast({
                contrast: parseFloat(val.val),
            }));
            break;
        case 'Saturation':
            filter = (val.isChecked && new f.Saturation({
                saturation: parseFloat(val.val),
            }));
            break;
        case 'Vibrance':
            filter = (val.isChecked && new f.Vibrance({
                vibrance: parseFloat(val.val),
            }));
            break;
        case 'Noise':
            filter = (val.isChecked && new f.Noise({
                noise: parseInt(val.val),
            }));
            break;
        case 'Pixelate':
            filter = (val.isChecked && new f.Pixelate({
                blocksize: parseInt(val.val),
            }));
            break;
        case 'Blur':
            filter = (val.isChecked && new f.Blur({
                blur: parseFloat(val.val),
            }));
            break;
        case 'Sharpen':
            filter = (val.isChecked && new f.Convolute({
            matrix: [  0, -1,  0,
                    -1,  5, -1,
                    0, -1,  0 ]
            }));
            break;
        case 'Emboss':
            filter = (val.isChecked && new f.Convolute({
            matrix: [ 1,   1,  1,
                    1, 0.7, -1,
                    -1,  -1, -1 ]
            }));
            break;
        case 'BlendColor':
            //todo alpha从color里取出
            filter = (val.isChecked && new f.BlendColor({
                color: val.color,
                mode:  val.mode,
                alpha: 1,
            }));
            break;
        default:
            console.log("未知的滤镜操作:", name);
            return;
    }
    let index = indexMaps[name];
    obj.filters[index] = filter;
    obj.applyFilters();
    canvas.renderAll();
}
