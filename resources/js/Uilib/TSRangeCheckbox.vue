<script setup>
//组件：带range的checkbox
import {ref,watch } from 'vue';
const props = defineProps(['name','init','max','min','label','step','isChecked']);
const emit = defineEmits(['tsComCallback', 'handelCheckBox']);

const openDropdown = ref(false);
const range = ref(props.init);
const isChecked = ref(props.isChecked);
function toggleDropdown(e) {
    openDropdown.value = !openDropdown.value;
    if(openDropdown.value == false){
        document.activeElement.blur();
    }
}
watch(() => props.init, () => {
    range.value = props.init;
});
watch(() => props.isChecked, () => {
    isChecked.value = props.isChecked;
});

function handelCheckBox(value){
    isChecked.value = value;
    emit('handelCheckBox', props.name, isChecked.value );
}

function blurEvent(e){
    openDropdown.value = false
}

</script>
<template>
    <div class="flex flex-row items-center gap-2">
    <input type="checkbox" checked="checked" class="checkbox checkbox-sm" @click="handelCheckBox($event.target.checked)"/>   
    <div class="dropdown w-32">  
          
        <div tabindex="0" role="button" class="btn btn-active text-white" :class="isChecked?'bg-primary':'bg-gray-500'" @click="toggleDropdown" @blur="blurEvent" >Clickderr:{{range}}</div>
        <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
            <li>
                <input type="range" :max="100" :min="0" :step="1" key="range2ts" class="range range-xs range-info" v-model="range"  @change="emit('tsComCallback', props.name, range);"/>
            </li>
        </ul>
    </div>
</div>

</template>