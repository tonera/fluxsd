import axios from 'axios';
import {usePage } from '@inertiajs/vue3';
var isRunning = false;
var retryTimes = 10;
const page = usePage();
var socket = null;
var cRetryTimes = 0;

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

export function connectWs(token, callback, wsHost, taskId){
    if(wsHost == '' || wsHost == undefined){
        console.log("wsHost is null , stoped");
        return;
    }
    if(token == '' || token == undefined){
        console.log("token is null , stoped");
        return;
    }
    if(socket != null && socket.readyState==1){
        socket.close();
    }

    console.log('socket=',socket);
    // token = 'eyZV0WJSQhBrqrWRWFNbflEoWW0';
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
                connectWs(token,callback, wsHost, taskId);
            }, 2000); // 2s
        }
    };
    socket.onerror = function(error){
        showTopSnackbar('Server is disconnected', 'error');
        // console.log(error);
        // console.log('Websocket error'+error);
    }

    // Listen for messages
    socket.addEventListener("message", function (event) {
        // console.log(event.data);
        let res = JSON.parse(event.data).body;
        // console.log(JSON.parse(event.data));
        console.log('res=',res);
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
                socket.close();
                showTopSnackbar(res.msg==undefined?'Generate failed':res.msg, 'error');
            break;
            case 'standing':
                isRunning = false;
                // socket.close();
            break;
        }
        //callback
        if(callback != undefined){
            callback(res);
        }
    });
}


//make url
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
    

    let RequestMethod = axios.post;
    let lang = page.props.locale
    let headers = {
        'Content-Type': 'application/x-www-form-urlencoded',
        'Accept': 'application/json',
        'lang':lang
    }
    Object.assign(headers, extraHeaders);


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
                return response.data;
            }).catch(error => {
                return null;
            });    
            break;
    }

    return await RequestMethod(api,formdata, {headers: headers}).then(response => {
            //console.log(response);
            return response.data;
        }).catch(error => {
            let errorRes = {};
            if (error.response) {
                errorRes = {
                    'code':422,
                    'msg':error.response.data.message
                }
              } else if (error.request) {
                errorRes = {
                    'code':423,
                    'msg':error.message
                }
              } else {
                errorRes = {
                    'code':424,
                    'msg':error.message
                }
              }
            // console.log(error);
            return errorRes;
        });    
}

export async function getAccessKey(keyApi){
    //'/api/accesskey'
    let res = await aiHttpRequest('get',keyApi ,null,null);
    if(res == null){
        showTopSnackbar('Api error', 'error');
    }

    if(res && res.code == 1){
        if(res.user.credit <= 0){
            showTopSnackbar('Your balance is insufficient', 'error');
            return false;
        }else{
            return res.token;
        }
    }else{
        showTopSnackbar(res.msg?res.msg:'There was an error requesting access, please try again later', 'error');
        return false;
    }
}


function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

export async function loopQuery(method , apiPath, data = null, extraHeaders = null) {
    var data = null;    
    while (data === null) {
        data = aiHttpRequest(method , apiPath, data, extraHeaders);  
        console.log(data);
        if (response.statusCode === 200 && response.body !== '') {
            data = JSON.parse(response.body);
        } else {
            await sleep(2*1000);
        }
    }  
}

export function buildSubmitParams(input){
    //if seed=0 seed value=random
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
    let required = ['engine', 'width', 'height', 'prompt', 'steps','cfg_scale', 'seed'];
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


export async function uploadInitImage(file, idx, token) {
    var formdata = new FormData();
    formdata.append('idx', idx);
    formdata.append('files', file);
    formdata.append('task_id', store.t_task_id);
    return await axios.post(store.apihost+'/v1/images',formdata, {
        headers: {
            'Content-Type':'multipart/form-data',
            'Authorization':token
        }
        }).then(response => {
            var res = response.data;
            if(res.code == 1){
                return res.data;
            }else{
                store.showTopSnackbar(res.msg??'Upload failed', 'error');
                return null;
            }
           
            //console.log(response.data);
        }).catch(error => {
            console.error('Upload failed', error);
            return null;
        });
}

//remove bg. image=url/base64 act=RBG/SR
export async function editImage(act , params, callback){
    var formdata = new FormData();
    Object.keys(params).forEach((v,k)=>{
        // console.log('k v =',v, params[v]);
        formdata.append(v, params[v]);
    });
    // return;
    // formdata.append('init_img_path', params.init_img_path);
    formdata.append('act', act);
    let res = await aiHttpRequest('post', '/api/task', formdata, {'Content-Type':'multipart/form-data'});
    // console.log(res);
    if(res.code == 1){
        connectTaskWs(res.data.task_id, callback);
    }else{
        console.log('error:editImage()');
        showTopSnackbar(res.msg?res.msg:trans('Server error, please try again later'), 'error');
    }
}

export const connectTaskWs = (task_id, callback) => {
    console.log('listen: channel_task =',task_id);
    window.Echo.private('channel_task.'+task_id)
        .listen('TaskMessage', async (e) => {
            // e.message
            // console.log('e.message =',e.message);
            callback(e.message);
        });
}


 

