<script setup>
import { ref } from 'vue';
const props = defineProps(['name','init','def','label']);
const emit = defineEmits(['tsComCallback']);
const openDropdown = ref(false);

function toggleDropdown(e) {
    openDropdown.value = !openDropdown.value;
    // console.log(openDropdown.value);
    if(openDropdown.value == false){
        handleSelected();
    }
}
const handleSelected = (item) => {
  if (document.activeElement instanceof HTMLElement) {
    document.activeElement.blur()
  }
  openDropdown.value = false
  if(item != undefined){
    emit('tsComCallback', props.name, item);
  }
}

</script>
<template>
    <div class="join flex justify-start items-center gap-1">
        <span class="join-item label-text-alt w-11">{{props.label}}</span>

        <div class="relative">
            <input type="text" class="join-item input input-xs input-accent w-full max-w-xs block pr-0"  @change="emit('tsComCallback', props.name, {val:$event.target.value});" :value="props.def" max="360" /> 
            <!-- :class="{ 'dropdown-hover': enableDropdownHover }" @mouseleave="enableDropdownHover = true" -->
            <div class="absolute inset-y-0 right-0 flex items-center">
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class=" m-2" @click="toggleDropdown" >
                        <svg class="w-[12px] h-[12px]" fill="currentColor" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                        <path d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"></path>
                        </svg>
                    </div>
                    <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-48 h-80 overflow-y-auto" >
                        <li v-for="(item,index) in props.init" :key="'TSD_'+index" @click="handleSelected(item)">
                            <a :style="'font-family:'+item.label">{{item.label}}</a>
                        </li>
                        <!-- <li><a>Item 2</a></li>
                        <li><a @click="handleSelected()">Item 1</a></li> -->
                    </ul>
                </div>
            </div>
        </div>
    </div>

</template>