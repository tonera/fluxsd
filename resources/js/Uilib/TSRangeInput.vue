<script setup>
//组件：带range的输入框
import { ref,watch } from 'vue';
const props = defineProps(['name','init','max','min','label','step']);
const emit = defineEmits(['tsComCallback']);
const openDropdown = ref(false);
const range = ref(props.init);
function toggleDropdown(e) {
    openDropdown.value = !openDropdown.value;
    if(openDropdown.value == false){
        // console.log(document.activeElement instanceof HTMLElement);
        // if (document.activeElement instanceof HTMLElement) {
        //     document.activeElement.blur();
        //     console.log(openDropdown.value);
        // }
        // openDropdown.value = false
        if (openDropdown.value == false) {
            document.activeElement.blur();
        }
    }
    
}
watch(() => props.init, () => {
    range.value = props.init;
});


function blurEvent(e){
    openDropdown.value = false
}

</script>
<template>
    <div class="join flex justify-start items-center gap-1">
        <span class="join-item label-text-alt w-11">{{props.label}}</span>
        <div class="relative">
            <input type="text" key="range1ts" class="join-item input input-xs input-accent w-full max-w-xs block pr-0"  @change="emit('tsComCallback', props.name, $event.target.value);" v-model="range" max="360" /> 
            <!-- :class="{ 'dropdown-hover': enableDropdownHover }" @mouseleave="enableDropdownHover = true" -->
            <div class="absolute inset-y-0 right-0 flex items-center">
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class=" m-2" 
                        @click="toggleDropdown" 
                        >
                        <svg @blur="blurEvent" class="w-[12px] h-[12px]" fill="currentColor"  viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                        <path d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"></path>
                        </svg>
                    </div>
                    <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-48" >
                        <input type="range" :max="props.max" :min="props.min" :step="props.step??1" key="range2ts" class="range range-xs range-info" v-model="range" @change="emit('tsComCallback', props.name, range);" />
                    </ul>
                </div>
            </div>
        </div>
    </div>

</template>