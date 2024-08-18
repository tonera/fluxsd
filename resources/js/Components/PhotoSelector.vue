<script setup>
import { onMounted, reactive, ref, watch, inject, computed} from "vue";

const props = defineProps(['photoList','selectedList']);
const emit = defineEmits(['handleChecked']);

// const idPhotoStyle = inject('idPhotoStyle');
const data = reactive({
    currentIndex:0,
});
onMounted(() => {
});

function selectPhoto(item, index){
    data.currentIndex = index;
    emit('handleChecked', item);
}

function isSelected(item){
    let ret = false;
    props.selectedList.forEach(element => {
        if(item.id == element.id){
            ret = true;
        }
    });
    return ret;
}

</script>

<style>

</style>

<template>
<div class="w-full grid grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-4 px-3" >
    <div 
        v-for="(item, index) in props.photoList" 
        :key="'pl_'+index"
        @click="selectPhoto(item)"
        class="m-4 relative inline-flex text-sm font-medium text-white bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 cursor-pointer ">
        <img class=" object-cover h-72 w-56" :src="item.thumb"/>
        <button v-show="isSelected(item)" type="button" class="absolute inline-flex items-center justify-center w-7 h-7 text-xs text-white bg-white border-2 border-white rounded-full -top-2 -right-2 dark:border-gray-900">
            <svg class="w-[30px] h-[30px] fill-[#31a805]" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
            <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"></path>
            </svg>
        </button>
    </div>
</div>

</template>
