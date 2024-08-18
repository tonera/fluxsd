<script setup>

const props = defineProps(['init', 'def','name']);
const emit = defineEmits(['tsComCallback']);

// console.log(' def=',props.name, props.def, props.init);
function setValue(item){
    try{
        emit('tsComCallback', props.name, item.val);
    }catch(error){
        console.log(error);
    }
}
// console.log(' def=',props.def == item.val);

</script>
<template>
    <label v-for="(item, index) in props.init" :key="index" class="label cursor-pointer gap-1">
        <input 
            type="radio" :name="props.name" 
            class="radio checked:bg-red-500" 
            :checked="props.def == item.val" 
            @input="setValue(item)"
        />
        <span class="label-text">{{item.label}}</span> 

    </label>
</template>