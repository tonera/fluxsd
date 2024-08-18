<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import TSPaginate from '@/Uilib/TSPaginate.vue';
import TSFloatMask from '@/Uilib/TSFloatMask.vue';
import TSScreenImage from '@/Uilib/TSScreenImage.vue';
import { aiHttpRequest, showTopSnackbar } from "@/network";
import {debug,showModalWindow} from '@/Uilib/TSFunction.js'; 
import {trans} from 'laravel-vue-i18n';
import { onMounted, onUnmounted,reactive, ref } from "vue";

// const params = reactive({engine:'atz',page:1});
const props = defineProps([]);



onMounted(()=>{
    // SSEListen();
});
onUnmounted(() => {
    // SSEClose();
});

async function loadData(page){

}


const SSEClose = ()=>{
    if(evtSource != null){
        // console.log("close sse");
        evtSource.close();
    }
}
const SSEListen = ()=>{
    let progress_ids = Object.keys(downloadStatus);
    evtSource = new EventSource("/api/download/progress?ids="+JSON.stringify(progress_ids));

    evtSource.addEventListener("open", (e) => {
        // console.log("Connection opened");
    });

    evtSource.addEventListener("message", (e) => {
        // console.log("Data: " + e.data);
        let statusJson = JSON.parse(e.data);
        for (var key in statusJson) {
            if (statusJson.hasOwnProperty(key)) {
                // console.log(key + " -> " + statusJson[key]);
                downloadStatus[key] = statusJson[key];
            }
        }

    });

    // evtSource.addEventListener("error", (e) => {
    //     console.log("Error: " + e.message);
    // });

    // evtSource.addEventListener("notice", (e) => {
    //     console.log("Notice: " + e.data);
    // });
}

</script>
<template>
<AppLayout>
    
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-2 bg-slate-50">
   
    </div>



</AppLayout>
</template>
