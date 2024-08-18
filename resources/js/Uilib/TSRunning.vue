<script setup>
//运行中页面
const props = defineProps(['init']);
</script>
<template>
    <div class=" bg-slate-800 w-56 h-28 rounded-xl p-4">
        <div class="flex justify-center items-center h-full gap-2">
                <span class="loading loading-dots loading-lg"></span>
                {{props.init}}
        </div>
    </div>
    
</template>