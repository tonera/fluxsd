<script setup>
import { aiHttpRequest, showTopSnackbar } from '@/Uilib/network';
import { onMounted, reactive, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';

const props = defineProps(['name','def','label']);
const emit = defineEmits(['tsComCallback']);
const itemValue = ref(props.def??false);
const rewardsList = ref([]);
var timerId = null; 

const statusState = reactive({
    status:'Offline',
    credit:'Unknown',
    online_time:'',
});

const handleInput = async (event) => {
    let post = {
        'switch':event.target.checked ? 'on' : 'off',
        'hardware':getGPUInfo(),
    };
    // emit('tsComCallback', props.name, itemValue.value)
    let res = await aiHttpRequest('post', "/api/partjob/toggle", post);
    if(res != null && res.code == 1){
        itemValue.value = event.target.checked;
    }else{
        // console.log(event.target.checked)
        event.target.checked = false;
        itemValue.value = false;
        showTopSnackbar(res.msg??'Switch failed', 'error');
    }
};

// 使用Canvas元素获取GPU信息
function getGPUInfo() {
    var canvas = document.createElement('canvas');
    var gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
    var debugInfo = gl.getExtension('WEBGL_debug_renderer_info');
    var gpuInfo = (debugInfo) ? gl.getParameter(debugInfo.UNMASKED_RENDERER_WEBGL) : 'Unavailable';
    return gpuInfo;
}
function formatTime(time){
    if(time == 0 || time == null){
        return '';
    }
    time = parseInt(time);
    if(time < 60){
        return '('+time + 's)';
    }else if(time < 3600){
        return '('+parseInt(time/60) + 'm)';
    }else{
        return '('+(time/3600).toFixed(1) + 'h)';
    }
}
function formatCredit(val){
    if (val == 'Unknown' || val == null){
        return 'N/A';
    }
    val = parseInt(val);
    if(val < 1000){
        return val;
    }else if(val < 1000000){
        return parseInt(val/1000) + 'k';
    }else{
        return (val/1000000).toFixed(1) + 'm';
    }
}

//status timer clearInterval(timerId);
function statusTimer() {
    timerId = setInterval(async ()=>{
        let res = await aiHttpRequest('get', '/api/partjob/status');
        if(res != null && res.code == 1){
            statusState.status = res.data.status;
            statusState.credit = res.data.credit;
            statusState.online_time = formatTime(res.data.online_time);
            if(statusState.status == 'Online'){
                itemValue.value = true;
            }else{
                itemValue.value = false;
            }
        }else{
            
        }
    }, 1000 * 60);//
}

async function show_deposits(e){
    // console.log(e);
    let res = await aiHttpRequest('get', "/api/partjob/deposits");
    // console.log(res);
    if(res != null && res.code == 1){
        rewardsList.value = res.data;
    }else{
        showTopSnackbar(res.msg??'Get data error', 'error');
    }
    pop_deposits.showModal();
}

onMounted(()=>{
    statusTimer();
});

</script>

<template>
<div class="flex items-center gap-1">
    <span class=" text-xs font-medium text-slate-500 dark:text-gray-300">{{props.label}}</span>
    <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" 
        :checked="itemValue" 
        @change="handleInput"
        class="sr-only peer">
        <div class="w-11 h-5 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[5px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
    </label>
    <span @click="show_deposits" class="text-xs font-medium text-slate-500 cursor-pointer ">
        <div class="stat place-items-center p-1">
        <div class="stat-title">Credits</div>
        <div class="stat-desc text-secondary">↗︎ {{formatCredit(statusState.credit)}} {{statusState.online_time}}</div>
    </div>
    </span>

    <dialog id="pop_deposits" class="modal">
        <div class="modal-box">
            <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <h3 class="text-lg font-bold">{{trans('rewards')}}!</h3>

            <div class="overflow-x-auto">
            <table class="table">
                <!-- head -->
                <thead>
                <tr>
                    <th></th>
                    <th>{{trans('name')}}</th>
                    <th>{{trans('credit')}}</th>
                    <th>{{trans('time')}}</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(item, index) in rewardsList" :key="'r_deposit_'+index">
                    <th class=" text-xs">{{item.id}}</th>
                    <td class=" text-xs">{{item.pay_mode_name}}</td>
                    <td class="text-primary text-lg">{{item.credit}}</td>
                    <td class=" text-xs">{{item.created_at}}</td>
                </tr>

                </tbody>
            </table>
            </div>

            
        </div>
    </dialog>

</div>

</template>