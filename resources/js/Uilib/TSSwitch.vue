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
<div class="p-2">
    <label class="relative inline-flex items-center cursor-pointer  pr-1">
        <input type="checkbox" 
        :checked="itemValue" 
        @change="handleInput"
        class="sr-only peer">
        <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
        <span class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300 mr-1">{{props.label}}</span>
    </label>
</div>

</template>