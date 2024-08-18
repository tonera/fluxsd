<script setup>
import { ref } from 'vue';

const props = defineProps(['name','def','label']);
const emit = defineEmits(['tsComCallback']);
const itemValue = ref(props.def??false);

const handleInput = (event) => {
    itemValue.value = event.target.checked;
    emit('tsComCallback', props.name, itemValue.value)
};

</script>

<template>
<div class="flex items-center">
    <!-- <input type="checkbox" 
    :checked="itemValue" 
    @change="handleInput"
    class="join-item toggle toggle-primary" />
    <span class="join-item  ml-2 text-sm font-medium text-gray-900 dark:text-gray-300 mr-1">{{props.label}}</span> -->
    <label class="relative inline-flex items-center cursor-pointer pr-1">
        <input type="checkbox" 
        :checked="itemValue" 
        @change="handleInput"
        class="sr-only peer">
        <div class="w-11 h-5 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[6px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
        <span class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300 mr-1">{{props.label}}</span>
    </label>
</div>

</template>