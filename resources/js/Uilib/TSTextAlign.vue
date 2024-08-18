<script setup>
//文本对齐单选项:left center right
import {ref } from 'vue';
const showContent = ref(true);
const props = defineProps(['name','init','label']);
const emit = defineEmits(['tsComCallback']);

</script>
<template>
    <div>
        <div class="divider divider-start label-text-alt cursor-pointer">
            {{props.label}}
         
        </div>
        <div v-show="showContent" class="grid grid-cols-3 gap-2 ml-2">
            <svg class="w-[20px] h-[20px] cursor-pointer" 
                :fill="props.init?.textAlign == 'left' ? 'yellow' : 'currentColor'" 
                @click="emit('tsComCallback','textAlign','left')"
                viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg">
            <path d="M288 64c0 17.7-14.3 32-32 32H32C14.3 96 0 81.7 0 64S14.3 32 32 32H256c17.7 0 32 14.3 32 32zm0 256c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H256c17.7 0 32 14.3 32 32zM0 192c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32zM448 448c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32z"></path>
            </svg>

            <svg class="w-[20px] h-[20px] cursor-pointer" 
                :fill="props.init?.textAlign == 'center' ? 'yellow' : 'currentColor'" 
                @click="emit('tsComCallback','textAlign','center')"
                viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg">
            <path d="M352 64c0-17.7-14.3-32-32-32H128c-17.7 0-32 14.3-32 32s14.3 32 32 32H320c17.7 0 32-14.3 32-32zm96 128c0-17.7-14.3-32-32-32H32c-17.7 0-32 14.3-32 32s14.3 32 32 32H416c17.7 0 32-14.3 32-32zM0 448c0 17.7 14.3 32 32 32H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H32c-17.7 0-32 14.3-32 32zM352 320c0-17.7-14.3-32-32-32H128c-17.7 0-32 14.3-32 32s14.3 32 32 32H320c17.7 0 32-14.3 32-32z"></path>
            </svg>

            <svg class="w-[20px] h-[20px] cursor-pointer" 
                :fill="props.init?.textAlign == 'right' ? 'yellow' : 'currentColor'" 
                @click="emit('tsComCallback','textAlign','right')"
                viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg">
            <path d="M448 64c0 17.7-14.3 32-32 32H192c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32zm0 256c0 17.7-14.3 32-32 32H192c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32zM0 192c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32zM448 448c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32z"></path>
            </svg>

        </div>
    </div>


</template>