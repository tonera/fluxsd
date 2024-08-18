<script setup>
//全屏展示图片
const props = defineProps(['init','modalId']);
</script>
<template>
    <div class=" bg-slate-500 relative rounded-xl p-2">
        <div class="flex justify-center items-center gap-2 relative">
            <label :for="props.modalId" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 bg-red-600 text-white">✕</label>
            <img :src="props.init" class=" object-cover h-screen rounded-xl">
        </div>
    </div>

    
</template>