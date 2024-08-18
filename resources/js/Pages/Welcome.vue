<script setup>
import { Head, Link,usePage } from '@inertiajs/vue3';
import {trans} from 'laravel-vue-i18n'
import { ref } from 'vue';
const page = usePage();
const appName = import.meta.env.VITE_APP_NAME;

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
    app_meta:{
        type: Object,
    }
});

var imageList = [
    {src:'/images/show-1.png',alt:''},
    {src:'/images/show-2.jpg',alt:''},
    {src:'/images/mj.jpg', alt:''},
    {src:'/images/show-4.jpg', alt:''}
];
var currentIndex = 0;
const currentDisplayImage = ref(imageList[currentIndex]);
function showImage(index) {
    currentDisplayImage.value = imageList[index];
}

function nextImage() {
    currentIndex = (currentIndex + 1) % imageList.length;
    console.log('currentIndex=',currentIndex);
    showImage(currentIndex);
}

function prevImage() {
    currentIndex = (currentIndex - 1 + imageList.length) % imageList.length;
    showImage(currentIndex);
}
// Auto-rotate carousel
// setInterval(nextImage, 3000); // Change interval as needed




const currentLang = ref('Language');
if(page.props.languages != null){
    if(page.props.locale!=null && page.props.languages[page.props.locale]!=null){
        currentLang.value = page.props.languages[page.props.locale];
    }
}


</script>


<template>
    <Head title="FluxSD index" />
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
        <img id="background" class="absolute -left-20 top-0 max-w-[877px]" src="/images/background.svg" />
        <div class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                    <div class="flex lg:justify-center col-span-2 items-center gap-5">
                        <img class="object-cover w-16 rounded-2xl" src="https://FluxSD.com/images/logo.png" alt="FluxSD.com logo"/>
                        <div class="font-bold text-5xl text-slate-600">{{appName}}</div>
                        <div class="text-slate-500 pt-5">{{trans('apixel_short_title')}}</div>
                    </div>

                    

                    <nav v-if="canLogin" class="-mx-3 flex flex-1 justify-end items-center">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                        >
                            {{trans('Start')}}
                        </Link>

                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >
                                Log in
                            </Link>

                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >
                                Register
                            </Link>
                        </template>

                        <!-- language select -->
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

                <main class="mt-6">
                    <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">
                        <div id=""
                            class="flex flex-col items-start gap-6 overflow-hidden rounded-lg bg-white shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] md:row-span-3 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]"
                        >
                            <div id="screenshot-container" class="relative flex w-full flex-1 items-stretch">
                                <!-- images show -->
                                <div class="carousel w-full">
                                    <div id="slide1" class="carousel-item relative w-full">
                                        <img
                                        :src="currentDisplayImage.src"
                                        class="w-full object-cover h-[850px]" />
                                        <div class="absolute left-5 right-5 top-1/2 flex -translate-y-1/2 transform justify-between">
                                        <a @click="prevImage" class="btn btn-circle opacity-25">❮</a>
                                        <a @click="nextImage" class="btn btn-circle opacity-25">❯</a>
                                        </div>
                                    </div>

                                </div>

                            
                                <div
                                    class="absolute -bottom-16 -left-16 h-40 w-[calc(100%+8rem)] bg-gradient-to-b from-transparent via-white to-white dark:via-zinc-900 dark:to-zinc-900"
                                ></div>
                            </div>
                            
                            <a href="https://FluxSD.com/docs">
                                <div class="relative flex items-center gap-6 lg:items-end p-6">
                                    <div id="docs-card-content" class="flex items-start gap-6 lg:flex-col">
                                    

                                        <div class="pt-3 sm:pt-5 lg:pt-0">
                                            <h2 class="text-xl font-semibold text-black dark:text-white">{{trans('Documentation')}}</h2>

                                            <p class="mt-4 text-sm/relaxed">
                                                Laravel has wonderful documentation covering every aspect of the framework. Whether you are a newcomer or have prior experience with Laravel, we recommend reading our documentation from beginning to end.
                                            </p>
                                        </div>
                                    </div>

                                    <svg class="size-6 shrink-0 stroke-[#FF2D20]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75"/></svg>
                                </div>
                            </a>
                        </div>


                        <a
                            href="/image"
                            class="flex items-start gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]"
                        >
                            <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-[#FF2D20]/10 sm:size-16">
                                <svg class="size-5 sm:size-6 fill-[#FF2D20]" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0 96C0 60.7 28.7 32 64 32H448c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6h96 32H424c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"></path>
                                </svg>
                            </div>

                            <div class="pt-3 sm:pt-5 grow">
                                <h2 class="text-xl font-semibold text-black dark:text-white">{{trans('image')}}</h2>

                                <div class="mt-4 flex gap-4 justify-around">
                                    <div v-for="(item,index) in app_meta.home_engine" :key="index" class="card card-compact  bg-base-100 shadow-xl">
                                        <figure>
                                            <img class="object-cover w-40 h-40" :src="item.img" :alt="item.name+' ai generation image'" />
                                        </figure>
                                        <div class="card-body">
                                            <p>{{trans(item.name)}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- <svg class="size-6 shrink-0 self-center stroke-[#FF2D20]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75"/></svg> -->
                        </a>

                        <a
                            href="/text"
                            class="flex items-start gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]"
                        >
                            <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-[#FF2D20]/10 sm:size-16">
                                <svg class="size-5 sm:size-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><g fill="#FF2D20"><path d="M8.75 4.5H5.5c-.69 0-1.25.56-1.25 1.25v4.75c0 .69.56 1.25 1.25 1.25h3.25c.69 0 1.25-.56 1.25-1.25V5.75c0-.69-.56-1.25-1.25-1.25Z"/><path d="M24 10a3 3 0 0 0-3-3h-2V2.5a2 2 0 0 0-2-2H2a2 2 0 0 0-2 2V20a3.5 3.5 0 0 0 3.5 3.5h17A3.5 3.5 0 0 0 24 20V10ZM3.5 21.5A1.5 1.5 0 0 1 2 20V3a.5.5 0 0 1 .5-.5h14a.5.5 0 0 1 .5.5v17c0 .295.037.588.11.874a.5.5 0 0 1-.484.625L3.5 21.5ZM22 20a1.5 1.5 0 1 1-3 0V9.5a.5.5 0 0 1 .5-.5H21a1 1 0 0 1 1 1v10Z"/><path d="M12.751 6.047h2a.75.75 0 0 1 .75.75v.5a.75.75 0 0 1-.75.75h-2A.75.75 0 0 1 12 7.3v-.5a.75.75 0 0 1 .751-.753ZM12.751 10.047h2a.75.75 0 0 1 .75.75v.5a.75.75 0 0 1-.75.75h-2A.75.75 0 0 1 12 11.3v-.5a.75.75 0 0 1 .751-.753ZM4.751 14.047h10a.75.75 0 0 1 .75.75v.5a.75.75 0 0 1-.75.75h-10A.75.75 0 0 1 4 15.3v-.5a.75.75 0 0 1 .751-.753ZM4.75 18.047h7.5a.75.75 0 0 1 .75.75v.5a.75.75 0 0 1-.75.75h-7.5A.75.75 0 0 1 4 19.3v-.5a.75.75 0 0 1 .75-.753Z"/></g></svg>
                            </div>

                            <div  class="pt-3 sm:pt-5">
                                <div v-for="(item,index) in app_meta.home_text" :key="index">
                                    <h2 class="text-xl font-semibold text-black dark:text-white">{{trans(item.name)}}</h2>

                                    <div class="mt-4 mb-4 text-sm/relaxed">
                                        {{trans(item.label)}}
                                    </div>
                                </div>
                            </div>

                            <svg class="size-6 shrink-0 self-center stroke-[#FF2D20]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75"/></svg>
                        </a>

                        <div class="flex items-start gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800">
                            <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-[#FF2D20]/10 sm:size-16">
                                <svg class="size-5 sm:size-6 fill-[#FF2D20]" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                <path d="M272 96c-78.6 0-145.1 51.5-167.7 122.5c33.6-17 71.5-26.5 111.7-26.5h88c8.8 0 16 7.2 16 16s-7.2 16-16 16H288 216s0 0 0 0c-16.6 0-32.7 1.9-48.2 5.4c-25.9 5.9-50 16.4-71.4 30.7c0 0 0 0 0 0C38.3 298.8 0 364.9 0 440v16c0 13.3 10.7 24 24 24s24-10.7 24-24V440c0-48.7 20.7-92.5 53.8-123.2C121.6 392.3 190.3 448 272 448l1 0c132.1-.7 239-130.9 239-291.4c0-42.6-7.5-83.1-21.1-119.6c-2.6-6.9-12.7-6.6-16.2-.1C455.9 72.1 418.7 96 376 96L272 96z"></path>

                                </svg>
                            </div>

                            <div class="pt-3 sm:pt-5">
                                <h2 class="text-xl font-semibold text-black dark:text-white">{{trans('Ecosystem')}}</h2>

                                <div class="mt-4">
                                    <ul class="flex gap-5">
                                        <li class=" flex flex-col items-center gap-2 border w-28 rounded-xl p-1 shadow-xl">
                                            <a href="https://tuse.ai"><img class="w-16" src="/images/tuseai.png"/></a>
                                            <p class="text-slate-600"><a href="https://tuse.ai">Tuse.ai</a></p>
                                        </li>
                                        <li class=" flex flex-col items-center gap-2 border w-28 rounded-xl p-1 shadow-xl">
                                            <a href="https://aiposter.cc"><img class="w-16" src="/images/aiposter.jpeg"/></a>
                                            <a href="https://aiposter.cc"><p class="text-slate-600">aiposter.cc</p></a>
                                        </li>
                                        <li class=" flex flex-col items-center gap-2 border w-28 rounded-xl p-1 shadow-xl">
                                            <a href="https://FluxSD.com"><img class="w-16" src="/images/logo.png"/></a>
                                            <a href="https://FluxSD.com"><p class="text-slate-600">FluxSD.com</p></a>
                                        </li>
                                    </ul>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </main>

                <div class="p-10 text-center">
                    <a data-sveltekit-preload-data="" href="/docs/install/" class="btn btn-neutral group w-60 rounded-full px-12">Github <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="hidden h-6 w-6 transition-transform duration-300 group-hover:translate-x-1 rtl:rotate-180 rtl:group-hover:-translate-x-1 md:inline-block"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3"></path></svg></a>
                </div>

                <hr>
                <footer class="py-10 text-center text-sm text-black dark:text-white/70">
                    <a href="https://FluxSD.com">Powered by {{trans('FluxSD.com')}}</a>
                </footer>
            </div>
        </div>
    </div>
</template>
