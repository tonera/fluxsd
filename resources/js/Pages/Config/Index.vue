<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { aiHttpRequest, showTopSnackbar } from '@/network';
import { trans } from 'laravel-vue-i18n';
import { reactive } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps(['ai_config','config_options','clientConfig']);
const aiConfig = reactive(props.ai_config);
const page = usePage();
console.log(page.props.auth.user.is_admin);
 
const state = reactive({
    configOption:'engine',
});

Object.entries(aiConfig).forEach(([key, value]) => {
    Object.entries(value).forEach(([key2, value2]) => {
        // console.log(typeof(value) == 'object', typeof(value2), value2, key);
        Object.entries(value2).forEach(([key3, value3]) => {
            // console.log(typeof(value) == 'object', typeof(value2), value2, key);
            if(key3 != 'label'){
                let inputKey = key +'.' + key2 + '.' + key3;
                state[inputKey] = value3;
            }
            // console.log(key3,inputKey);
        });
    });
    
});

function switchConfig(option){
    state.configOption = option;
}

async function setKeyValue(pkey, skey){
    console.log(pkey,skey, state);
    let params = {
        pkey:pkey,
        skey:skey,
        config:JSON.stringify(state)
    };
    let res = await aiHttpRequest("post", '/api/config/update',params);
    console.log(res);
    if(res!=null && res.code == 1){
        showTopSnackbar(trans("Operation successfully"), "success");
    }else{
        showTopSnackbar(res.msg??trans("Operation failed"), "error");
        return;
    }
    
}

async function setActive(pkey, skey){
    let activeKey = pkey +'.' + skey + '.is_active';
    // console.log(state);
    state[activeKey] = state[activeKey] == 1 ? 0 :1;
    //only 1 is active for storage option
    if(pkey == 'storage'){
        if(state[activeKey] == 1){
            Object.entries(aiConfig[pkey]).forEach(([key, value]) => {
                console.log(value);
                aiConfig[pkey][key]['is_active'] = 0;
                let tmpkey = pkey +'.' + key + '.is_active';
                state[tmpkey] = 0;
            });
            state[activeKey] = 1;
        }else{
            //at least one is active
            let hasActive = false;
            Object.entries(aiConfig[pkey]).forEach(([key, value]) => {
                // console.log(state[tmpkey]);
                let tmpkey = pkey +'.' + key + '.is_active';
                console.log(state[tmpkey]);
                if(state[tmpkey] == 1) {
                    hasActive = true;
                    return;
                }
            });
            if(!hasActive){
                showTopSnackbar(trans("At least one active storage is required"),'info');
                state[activeKey] = 1;
                return;
            }
        }
        
    }
    
    let params = {
        c_key:activeKey,
        c_value:state[activeKey],
    };
    let res = await aiHttpRequest("post", '/api/config/active',params);
    if(res!=null && res.code == 1){
        aiConfig[pkey][skey]['is_active'] = state[activeKey];
    }else{
        showTopSnackbar(res.msg??trans("Operation failed"), "error");
        return;
    }
    console.log(res);
}


// get gpu info using canvas
function getGPUInfo() {
    var canvas = document.createElement('canvas');
    var gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
    var debugInfo = gl.getExtension('WEBGL_debug_renderer_info');
    var gpuInfo = (debugInfo) ? gl.getParameter(debugInfo.UNMASKED_RENDERER_WEBGL) : 'Unavailable';
    return gpuInfo;
}
 
var gpuInfo = getGPUInfo();
console.log(gpuInfo);

</script>

<template>
    <AppLayout title="Config Panel">
 
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-2">
        <div class="grid ">
            <div role="tablist" class="tabs tabs-boxed tabls-sm justify-self-start min-w-96">
                <a 
                    v-for="(item, index) in props.config_options" 
                    :key="index" 
                    :class="{'tab-active': item.option == state.configOption }"
                    @click="switchConfig(item.option)"
                    role="tab" 
                    class="tab">{{item.label}}
                </a>
            </div>
        </div>

        <div v-if="page.props.auth.user.is_admin == 1" class=" grid grid-cols-2">
            <div v-for="(item,engineKey) in aiConfig[state.configOption]" :key="'e_'+engineKey" class="m-2">
                
                <div class=" rounded-xl h-full" :class="item.is_active == 1 ? 'bg-base-200 border border-green-600':'bg-base-200'"> 
                    <div class="collapse-title text-xl font-medium flex justify-between">
                        <div>
                            {{trans(engineKey)}}
                        </div>
                        <div class="flex gap-1 items-center">
                            <button v-if="item.is_active == 0" class="btn btn-primary btn-sm" @click="setActive(state.configOption, engineKey)">{{trans('active')}}</button>
                            <button v-else class="btn btn-neutral btn-sm" @click="setActive(state.configOption, engineKey)">{{trans('disable')}}</button>
                            <button class="btn btn-primary btn-sm" @click="setKeyValue(state.configOption, engineKey)">{{trans('save')}}</button>
                        </div>
                    </div>
                    <div class="p-2"> 
                        <div v-for="(def, inputkey) in item" :key="engineKey+inputkey">
                            <div class="join join-vertical lg:join-horizontal py-1 items-center w-full">
                                <label class="btn join-item w-28 bg-base-300 mr-1">{{trans(inputkey)}}</label>
                                <div v-if="inputkey=='is_active'">
                                    <div v-if="def == '1'" class="badge badge-success text-white font-bold h-8">{{trans('actived')}}</div>
                                    <div v-else class="badge badge-neutral">{{trans('disabled')}}</div>
                                </div>
                                <label v-else-if="inputkey == 'label'" class="text-sm p-1">{{trans(def)}}</label>
                                <label v-else-if="inputkey == 'apply_url'" class="text-sm p-1"><a :href="def" target="_blank">{{def}}</a></label>
                                <input v-else type="text" placeholder="Type here" :name="state.configOption+'.'+engineKey+'.'+inputkey" class="join-item input input-bordered w-full max-w-xs" v-model="state[state.configOption+'.'+engineKey+'.'+inputkey]" />
                            </div>
                            
                        </div>
                        <hr/>
                        <label v-if="state.configOption == 'engine' && engineKey=='lc'" class="form-control">
                            <div class="label">
                                <span class="label-text">{{trans('Image generator config file')}}</span>
                                <span class="label-text-alt">config/mk_config.ini</span>
                            </div>
                            <textarea class="textarea textarea-bordered h-24" placeholder="" v-model="props.clientConfig">
                            </textarea>
                            <div class="label">
                                <span class="label-text-alt">{{trans('Paste this content into the file:config/mk_config.ini')}}</span>
                                {{engineKey}}
                            </div>
                        </label>
                        

                    </div>

                </div>

            </div>
        </div>
        <div v-else>
            <p class="stat text-sm font-semibold text-red-500">{{trans('Only administrators can manage configuration')}}</p>
        </div>

    </div>
  
    </AppLayout>
</template>
