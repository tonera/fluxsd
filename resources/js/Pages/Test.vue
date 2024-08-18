<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';
import { onMounted, reactive } from 'vue';
const messages = reactive([]);
var evtSource = null;

// window.Echo.channel("delivery").listen("PackageSent", (event) => {
//     console.log(event);
// });

const webSocketChannel = 'channel_for_everyone';
const connectWebSocket = () => {
    window.Echo.private(webSocketChannel)
        .listen('GotMessage', async (e) => {
            // e.message
            console.log('e.message =',e.message);
        });
}

onMounted(()=>{
    // connectWebSocket();
    // window.Echo.leave(webSocketChannel);
});


const connectTaskWs = () => {
    window.Echo.private('channel_task.240406480935120896')
        .listen('TaskMessage', async (e) => {
            // e.message
            console.log('e.message =',e.message);
        });
}

const connectSSE = ()=>{
    evtSource = new EventSource("/api/download/progress");

    evtSource.addEventListener("open", (e) => {
    console.log("Connection opened");
    });

    evtSource.addEventListener("message", (e) => {
    console.log("Data: " + e.data);
    });

    evtSource.addEventListener("error", (e) => {
    console.log("Error: " + e.message);
    });

    evtSource.addEventListener("notice", (e) => {
    console.log("Notice: " + e.data);
    });

}

const closeSSE = ()=>{
    evtSource.close();
}



</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="btn" @click="connectWebSocket">连接task</div>
                    <div class="btn" @click="connectSSE">连接SSE</div>
                    <div class="btn" @click="closeSSE">关闭SSE</div>
                    <div v-for="(item, index) in messages" :key="index">
                        <div class="flex">
                            {{item.text}}
                            <div class="text-xs">{{item.created_at}}</div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
