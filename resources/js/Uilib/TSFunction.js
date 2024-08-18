import { reactive } from 'vue'
import {oTask, oImage} from './TSState.js'; 
import { aiHttpRequest } from './network.js';
// import { trans } from 'laravel-vue-i18n';
//ts公共方法
export function debug(...msg) {
    // console.log(msg.join(' '));
}

export function showTopSnackbar(msg ,type='info') {
    // Get the snackbar DIV
    var x = document.getElementById("snackbar");
    x.innerHTML = msg;
    var showClassName = 'show';
    switch(type){
        case 'error':
            showClassName = 'snackerror';
            break;
    }
    // Add the "show" class to DIV
    x.className = showClassName ;
    // After 3 seconds, remove the show class from DIV
    setTimeout(function(){ x.className = x.className.replace(showClassName, ""); }, 3000);
  }
//打包一个formdata
export function buildFormData(input)
{
    var formdata = new FormData();
    let keys = Object.keys(input);
    for(let i = 0; i < keys.length; i++)
    {
        let key = keys[i];
        // console.log(key, input[key]);
        formdata.append(key, input[key]);
    }
    return formdata;
}
//将ws消息转换为图片数据结构
export function convertToImageFromMessage(message){
    oImage.id = message.id??null;
    oImage.execTime = message.execTime??null;
    oImage.buttonGroups = message.buttonGroups??null;
    oImage.init_img_path = message.init_img_path;
    oImage.show_url = message.show_url??null;
    oImage.thumb = message.thumb??null;
    oImage.uri = message.uri??null;
    oImage.task_id = message.task_id??null;
    oImage.user_id = message.user_id??null;
    oImage.task = message.task?convertToTaskFromMessage(message.task):null;
}
//将ws消息转换为task对象
export function convertToTaskFromMessage(taskMessage){
    oTask.prompt_en = taskMessage.prompt_en??'';
    oTask.negative_prompt_en = taskMessage.negative_prompt_en??'';
    // let keys = Object.keys(oTask);
    // for(let i = 0 ; i < keys.length ; i++){
    //     let key = keys[i];
    //     console.log("设置oTask变量 "+ key + '=' + taskMessage[key]);
    //     oTask[key] = taskMessage[key];
    // }
}
//从尺寸对象计算对应的像素值
export function computePixel(size){
    // console.log('size = ',size);
    if(size == null){
        return {width:0, height:0}
    }
    let rate = 1;
    if(size.unit == 'inch'){
        rate = 300;
    }else if (size.unit == 'cm'){
        rate = 118;
    }else if (size.unit == 'mm'){
        rate = 11.8;
    }
    //横还是竖幅
    let width = 0 ;
    let height = 0;
    if(size.vh == true && size.w  < size.h){
        height = size.w ;
        width  = size.h ;
    }else if (size.vh == false && size.w  > size.h ){
        height = size.w ;
        width  = size.h ;
    }else{
        height = size.h ;
        width  = size.w ;
    }
    return {width:width * rate, height:height * rate}
}
//生成随机字符串
export function generateRandomString(length) {
    const possibleCharacters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let result = '';
    for (let i = 0; i < length; i++) {
      result += possibleCharacters.charAt(Math.floor(Math.random() * possibleCharacters.length));
    }
    return result;
}

export function removeBackground(api, url, token){
    let res = aiHttpRequest('post', api, {init_img_path:url}, {'Content-Type':'multipart/form-data',Authorization:token});
}

 //打开一个modal窗口
 export function showModalWindow(modalId){
    debug("打开modal窗口=",modalId);
    let checkbox = document.getElementById(modalId);
    if(checkbox == null){
        debug("Error:Modal窗口 id不存在", modalId);
        return;
    }
    // console.log(checkbox);
    checkbox.checked = true; 
}
export function closeModalWindow(modalId){
    let checkbox = document.getElementById(modalId);
    if(checkbox == null){
        debug("Error:Modal窗口 id不存在", modalId);
        return;
    }
    checkbox.checked = false; 
}
export function copyDoiToClipboard(source){
    navigator.clipboard.writeText(source);
}



