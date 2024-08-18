import axios from 'axios';
import {usePage } from '@inertiajs/vue3';
import {trans} from 'laravel-vue-i18n';
var isRunning = false;//是否异常断开，决定是否需要重新连接
var retryTimes = 10;//断开重试次数
const page = usePage();
var socket = null;
var cRetryTimes = 0;

export function showTopSnackbar(msg ,type='info') {
    // Get the snackbar DIV
    var x = document.getElementById("snackbar");
    if(x == null || x == undefined){
        console.log("error:",msg);
        return;
    }
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

export function connectWs(token, callback, wsHost, taskId){
    if(socket != null){
        isRunning = false;
        socket.close();
    }
    if(wsHost == '' || wsHost == undefined){
        console.log("wsHost is null , stoped");
        return;
    }
    if(token == '' || token == undefined){
        console.log("token is null , stoped");
        return;
    }
    // token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJmaXNoYXBpIiwiYXVkIjoiIiwiaWF0IjoxNjk5MzQxMzY3LCJuYmYiOjE2OTkzNDEzNjcsImV4cCI6MTcwMDIwNTM2NywiZGF0YSI6eyJ1c2VyX2lkIjoxNywidXNlcl9uYW1lIjoiVG9ueSIsImN0aW1lIjoxNjk5MzQxMzY3fX0.S8MKFiXVHm2-gMEd3gZV0WJSQhBrqrWRWFNbflEoWW0';
    // taskId = 'bfeb07ee-86fa-49f5-92dc-2adb0eff5fcb';
    
    console.log('Connect string='+wsHost+"/?token="+token+"&task_id="+taskId);
    socket = new WebSocket(wsHost+"/?token="+token+"&task_id="+taskId);

    socket.addEventListener("open", function (event) {
        isRunning = true;
        console.log('open socket');
    });
    socket.onclose = function(){
        if(cRetryTimes >= retryTimes){
            isRunning = false;
        }
        console.log('Websocket closed',isRunning);
        if(isRunning){
            cRetryTimes = cRetryTimes+1;
            console.log('Websocket connect retry...');
            setTimeout(function() {
                connectWs(token);
            }, 2000); // 参数为要执行的函数和等待的时间（单位为毫秒）
        }
        
        
    };
    socket.onerror = function(error){
        showTopSnackbar(trans('Server is disconnected, please try again later'), 'error');
        // console.log(error);
        // console.log('Websocket error'+error);
    }

    // Listen for messages
    socket.addEventListener("message", function (event) {
        // console.log(event.data);
        let res = JSON.parse(event.data).body;
        // console.log(JSON.parse(event.data));
        console.log('res=%s',res);
        if(res == null || res.message_type == undefined){
            return;
        }
   
        switch(res.message_type){
            case 'ephemeral':
                if(res.show_url != '' && res.show_url != undefined){
                    ;
                }
            break;
            case 'failed':
                isRunning = false;
                // socket.close();
                showTopSnackbar(res.msg==undefined?trans('Generating picture is failed'):res.msg, 'error');
            break;
            case 'standing':
                isRunning = false;
                // socket.close();
            break;
        }
        //回调
        if(callback != undefined){
            callback(res);
        }
    });
}


//对象拼成url
function filter(str) { 
    str += ''; 
    str = str.replace(/&/g, '%26');
    str = str.replace(/\=/g, '%3D');
    return str;
}
function formateObjToParamStr(paramObj) {
    const sdata = [];
    for (let attr in paramObj) {
        sdata.push(`${attr}=${filter(paramObj[attr])}`);
    }
    return sdata.join('&');
};

export async function aiHttpRequest(method , api, data = null, extraHeaders = null){
    let formdata = data;
    if(method === 'get'){
        if(data !== null){
            let queryString = formateObjToParamStr(data);
            api += '?'+queryString;
            formdata = null;
        }
    }
    
    // let token = Cookies.get('Aitoken');
    // console.log('Aitoken='+token);
    
    let RequestMethod = axios.post;
    let lang = page.props.locale
    let headers = {
        'Content-Type': 'application/x-www-form-urlencoded',
        'Accept': 'application/json',
        'lang':lang
    }
    Object.assign(headers, extraHeaders);

    // console.log(headers)

    switch (method){
        case 'post':
            RequestMethod = axios.post;
            break;
        case 'put':
            RequestMethod = axios.post;
            formdata._method = 'put';
            break;
        case 'delete':
            RequestMethod = axios.delete;
            break;
        case 'get':
            RequestMethod = axios.get;
            return await RequestMethod(api,{headers: headers}).then(response => {
                //console.log(response.data);
                // if(response != undefined && response.data.code == 3000){
                //     router.post(route('changetoken')); 
                // }
                return response.data;
            }).catch(error => {
                //console.error('失败', error);
                return null;
            });    
            break;
    }
    //console.log(formdata);
    //console.log(apiPath, headers);
    return await RequestMethod(api,formdata, {headers: headers}).then(response => {
            //console.log(response);
            return response.data;
        }).catch(error => {
            //console.error('失败', error);
            return null;
        });    
}

export async function getAccessKey(keyApi){
    //'/api/accesskey'
    let res = await aiHttpRequest('get',keyApi ,null,null);
    if(res == null){
        showTopSnackbar(trans('Call the api is failed'), 'error');
    }

    if(res && res.code == 1){
        if(res.user.credit <= 0){
            showTopSnackbar(trans('Insufficient balance,Please go to aitezhu.cn to recharge'), 'error');
            return false;
        }else{
            return res.token;
        }
    }else{
        showTopSnackbar(trans('Call the api is failed'), 'error');
        return false;
    }
}


function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

//以阻塞方式查询服务器端的任务状态（ws不稳定）
//此方法就是比aiHttpRequest方法包装了一个轮询器
export async function loopQuery(method , apiPath, data = null, extraHeaders = null) {
    var data = null;    
    while (data === null) {
        // 模拟向服务器发送查询请求并接收响应
        
        data = aiHttpRequest(method , apiPath, data, extraHeaders);  
        console.log(data);
        
        // 假设服务器返回的数据为response
        if (response.statusCode === 200 && response.body !== '') {
            data = JSON.parse(response.body);
            
            console.log('成功获取到数据', data);
        } else {
            console.log('没有获取到数据，休息2秒继续');
            await sleep(2*1000);
            
        }
    }  
}

export function buildSubmitParams(input){
    //如果seed是0或者-1则随机
    if(input.isRandom == true){
        input.seed = Math.floor((Math.random() * 100000000) + 1);
    }
    var formdata = new FormData();
    for (let key in input) {
        if (input.hasOwnProperty(key) && input[key]!=null) {
            // console.log(key, input[key]);
            if(key == 'face_enhance'){
                input[key] = input[key] ? 'yes':'no';
            }
            formdata.append( key , input[key]);
        }
    }
    return formdata;
}
export function checkSubmitParams(act , input){
    if(!input.hasOwnProperty('engine') || input['engine'] == null){
        return 'engine can not be null';
    }
    let required = ['engine', 'width', 'height', 'prompt', 'steps','scale', 'seed'];
    if(input['engine'] == 'local'){
        required.push('model_name');
    }
    if(input['engine'] == 'om'){
        required.push('sampler_name');
        required.push('model_name');
    }

    // console.log(input['engine']);
    for (let key of required) {
        // console.log(key, typeof(key),input[key]);
        if (!input.hasOwnProperty(key) || input[key]===null || input[key]==='') {
            return key + ' can not be null';
        }
    }
    return true;
}

export function getSrc(file) {
    return new Promise(function (resolve, reject) {
      var reader = new FileReader();
      reader.readAsDataURL(file);
      reader.onload = () => {
        resolve(reader.result);
      };
    });
  }


export async function uploadInitImage(apiPath,file, idx, token) {
    //console.log('初始图片标识符='+idx);
    //eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJmaXNoYXBpIiwiYXVkIjoiIiwiaWF0IjoxNjk5MzcyMDcxLCJuYmYiOjE2OTkzNzIwNzEsImV4cCI6MTcwMDIzNjA3MSwiZGF0YSI6eyJ1c2VyX2lkIjoxNywidXNlcl9uYW1lIjoidG9ueSIsImN0aW1lIjpmYWxzZX19.zWyI8EkNSFUHV5KBi2QSCSHHzYIDfwkDBuIUGAXW-0c
    var formdata = new FormData();
    formdata.append('idx', idx);
    formdata.append('files', file);
    return await axios.post(apiPath,formdata, {
        headers: {
            'Content-Type':'multipart/form-data',
            'Authorization':token
        }
        }).then(response => {
            var res = response.data;
            if(res.code == 1){
                return res.data;
            }else{
                showTopSnackbar(res.msg??trans('Upload image failed'), 'error');
                //remove 失败的图片
                //removeImage(idx);
                return null;
            }
           
            //console.log(response.data);
        }).catch(error => {
            console.error(trans('Upload image failed'), error);
            return null;
        });
}


 

