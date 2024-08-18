<script setup>
import { Head, router,usePage } from '@inertiajs/vue3';
import {trans} from 'laravel-vue-i18n'
import { reactive, ref } from 'vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import CheckCircle from '@/Components/CheckCircle.vue';
import { showTopSnackbar,aiHttpRequest } from '@/network';
import TopSnackBar from '@/Uilib/TopSnackbar.vue';

const page = usePage();
const props = defineProps(['clientConfig','localIp','localPort']);
const appName = import.meta.env.VITE_APP_NAME;
const step = ref(1);
const currentLang = ref('Language');
if(page.props.languages != null){
    if(page.props.locale!=null && page.props.languages[page.props.locale]!=null){
        currentLang.value = page.props.languages[page.props.locale];
    }
}

const params = reactive({
    name:'aaa',
    email:'aaa1@qq.com',
    password:'pppppppp',
    password_confirmation:'pppppppp',
    processing:false,
    access_url:window.location.protocol + '//' + props.localIp + ':' + props.localPort,
    atz_token:"",
    together_token:""
});
var previewKeys = ['name','email','access_url','atz_token','together_token'];

function computeStyle(value){
    return value ? 'text-primary h-5 w-5' : 'h-5 w-5';
}
function isEmail(email) {
    const reg = /^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,4})$/;
    return reg.test(email);
}
function isUrl(url) {
    try {
        new URL(url);
        return true;
    } catch (error) {
        return false;
    }
}

async function submit(){
    if(params.name == ''){
        showTopSnackbar(trans('name_notice'), 'error');
        return;
    }
    if(params.email == ''){
        showTopSnackbar(trans('email_notice'), 'error');
        return;
    }
    if(isEmail(params.email) == false){
        showTopSnackbar(trans('The email format is incorrect'), 'error');
        return;
    }
    if(params.password == '' || params.password == false){
        showTopSnackbar(trans('password_notice'), 'error');
        return;
    }
    if(params.password.length < 8){
        showTopSnackbar(trans('The password field must be at least 8 characters'), 'error');
        return;
    }
    if(params.password != params.password_confirmation){
        showTopSnackbar(trans('congirm_password_error'), 'error');
        return;
    }
    if(params.access_url != '' && isUrl(params.access_url) == false){
        showTopSnackbar(trans('Access Url format is incorrent'), 'error');
        return;
    }
    var formdata = new FormData();

    Object.entries(params).forEach(([key, value]) => {
        formdata.append(key,value)
        // console.log(key,'=', value)
    });

    let jsonObj={};
    formdata.forEach((value, key) => (jsonObj[key] = value));
    // console.log(JSON.stringify(jsonObj));

    params.processing = true;
    let res = await aiHttpRequest('post', '/api/setup', formdata);
    console.log(res);
    params.processing = false;
    if(res != null && res.code == 1){
        window.location.href = '/';
    }else{
        showTopSnackbar(res?res.msg:'Steup failed', 'error');
    }

}


function nextStep(num){
    step.value = num;
}

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}
</script>

<template>
    <TopSnackBar></TopSnackBar>
    <Head title="Setup" />
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
        <img id="background" class="absolute -left-20 top-0 max-w-[877px]" src="/images/background.svg" />
        <div class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                    <div class="flex lg:justify-center col-span-2 items-center gap-5">
                        <img class="object-cover w-16 rounded-2xl" src="https://fluxsd.com/images/logo.png" alt="fluxsd.com logo"/>
                        <div class="font-bold text-5xl text-slate-600">{{appName}}</div>
                        <div class="text-slate-500 pt-5">{{trans('apixel_short_title')}}</div>
                    </div>

                    <nav  class="-mx-3 flex flex-1 justify-end items-center">
                        <!-- 语言选择 -->
                        <div class="ms-3 relative">
                            <div class="dropdown dropdown-end px-3 py-2 text-sm">
                                <div tabindex="0" role="button" class="btn btn-sm m-1 bg-slate-200 border-none hover:bg-slate-100">
                                    {{currentLang}}
                                    <svg class="-me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </div>
                                <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                    <li v-for="(item, index) in $page.props.languages" :key="'lang_'+index">
                                        <a :href="'/changeLanguage?lang='+index">{{item}}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                    </nav>
                </header>


                <main class="flex flex-col items-center h-screen">
                    <div class="text-center">
                        <ul class="steps overflow-x-auto gap-4">
                            <li @click="nextStep(1)" class="step cursor-pointer" :class="{'step-primary':step >= 1}">{{trans('Create Administrator Account')}}</li>
                            <li @click="nextStep(2)" class="step cursor-pointer" :class="{'step-primary':step>=2}">{{trans('Storage and Generator')}}</li>
                            <li @click="nextStep(3)" class="step cursor-pointer" :class="{'step-primary':step>=3}">{{trans('Model Token')}}</li>
                            <li @click="nextStep(4)" class="step cursor-pointer" :class="{'step-primary':step>=4}">{{trans('Preview and Save')}}</li>
                        </ul>
                    </div>
                    <!-- step one -->
                    <div v-if="step == 1" class="pt-5 w-72 place-content-center place-items-center">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight py-5">
                            {{trans('Create Administrator Account')}}
                        </h2>
                        <div>
                            <InputLabel for="name" value="Name" />
                            <TextInput
                                id="name"
                                v-model="params.name"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                autofocus
                                autocomplete="name"
                            />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="email" value="Email" />
                            <TextInput
                                id="email"
                                v-model="params.email"
                                type="email"
                                class="mt-1 block w-full"
                                required
                                autocomplete="username"
                            />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="password" value="Password" />
                            <TextInput
                                id="password"
                                v-model="params.password"
                                type="password"
                                class="mt-1 block w-full"
                                required
                                autocomplete="new-password"
                            />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="password_confirmation" value="Confirm Password" />
                            <TextInput
                                id="password_confirmation"
                                v-model="params.password_confirmation"
                                type="password"
                                class="mt-1 block w-full"
                                required
                                autocomplete="new-password"
                            />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <PrimaryButton  @click="nextStep(2)" class="ms-4" :class="{ 'opacity-25': params.processing }" :disabled="params.processing">
                                {{trans('Next')}}
                            </PrimaryButton>
                        </div>
                    </div>

                    <!-- step two -->
                    <div v-if="step == 2" class="pt-5 w-72 place-content-center place-items-center">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight py-5">
                            {{trans('Storage and Generator')}}
                        </h2>
                        <div>
                            <InputLabel for="access_url" :value="trans('access_url')" />
                            <TextInput
                                id="access_url"
                                v-model="params.access_url"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                autofocus
                                autocomplete="access_url"
                            />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <PrimaryButton @click="nextStep(1)" class="ms-4" :class="{ 'opacity-25': params.processing }" :disabled="params.processing">
                                {{trans('Return')}}
                            </PrimaryButton>
                            <PrimaryButton @click="nextStep(3)" class="ms-4" :class="{ 'opacity-25': params.processing }" :disabled="params.processing">
                                {{trans('Next')}}
                            </PrimaryButton>
                        </div>
                    </div>

                     <!-- step three -->
                    <div v-if="step == 3" class="pt-5 w-72 place-content-center place-items-center">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight py-5">
                            {{trans('Model Token')}}
                        </h2>
                        <div>
                            <InputLabel for="atz_token" :value="trans('atz_token')" />
                            <TextInput
                                id="atz_token"
                                v-model="params.atz_token"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                autofocus
                                autocomplete="atz_token"
                            />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="together_token" :value="trans('together_token')" />
                            <TextInput
                                id="together_token"
                                v-model="params.together_token"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                autocomplete="together_token"
                            />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <PrimaryButton @click="nextStep(2)" class="ms-4" :class="{ 'opacity-25': params.processing }" :disabled="params.processing">
                                {{trans('Return')}}
                            </PrimaryButton>
                            <PrimaryButton @click="nextStep(4)" class="ms-4" :class="{ 'opacity-25': params.processing }" :disabled="params.processing">
                                {{trans('Next')}}
                            </PrimaryButton>
                        </div>
                    </div>

                    <!-- step four -->
                    <div v-if="step == 4" class="pt-5 place-content-center place-items-center">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight py-5">
                            {{trans('Preview and Save')}}
                        </h2>
                        <div class="">
                            <ul class="timeline timeline-vertical">
                                <li v-for="(item, index) in previewKeys" :key="index" class="flex gap-1 py-1 items-center">
                                    <div class="timeline-middle">
                                        <CheckCircle :classStyle="computeStyle(params[item])"></CheckCircle>
                                    </div>
                                    <div class="timeline-end timeline-box">{{trans(item)}}</div>
                                    <div class=" text-xs"> - {{trans(item+'_notice')}}</div>
                                    <hr />
                                </li>
                            </ul>
                        </div>

                        <label class="form-control">
                        <div class="label">
                            <span class="label-text">{{trans('Image generator config file')}}</span>
                            <span class="label-text-alt">config/mk_config.ini</span>
                        </div>
                        <textarea class="textarea textarea-bordered h-24" placeholder="" v-model="props.clientConfig">
                        </textarea>
                        <div class="label">
                            <span class="label-text-alt">{{trans('Paste this content into the file:config/mk_config.ini')}}</span>
                        </div>
                        </label>

                        <div class="flex items-center justify-end mt-4">
                            <PrimaryButton @click="nextStep(3)" class="ms-4" :class="{ 'opacity-25': params.processing }" :disabled="params.processing">
                                {{trans('Return')}}
                            </PrimaryButton>
                            <PrimaryButton @click="submit" class="ms-4" :class="{ 'opacity-25': params.processing }" :disabled="params.processing">
                                {{trans('submit')}}
                            </PrimaryButton>
                        </div>
                    </div>

                </main>

                <footer class="py-16 text-center text-sm text-black dark:text-white/70">
                    
                </footer>
            </div>
        </div>
    </div>
</template>
