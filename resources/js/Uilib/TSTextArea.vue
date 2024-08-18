<script setup>
import { ref,watch } from 'vue';

const props = defineProps(['name','init','label', 'def']);
const emit = defineEmits(['tsComCallback']);
const areaValue = ref(props.def??'');

const handleInput = (event) => {
    areaValue.value = event.target.value;
    emit('tsComCallback', props.name, areaValue.value)
};
watch(()=>props.def, (e)=>{
    areaValue.value = props.def;
});

</script>

<template>
<div class="p-1">
    <textarea 
        :value="areaValue"
        @input="handleInput"
        class="textarea textarea-bordered w-full" 
        :placeholder="props.label">
    </textarea>
</div>
</template>