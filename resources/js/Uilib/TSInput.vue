<script setup>
const props = defineProps(['name','init','label','iclass','sclass']);
const emit = defineEmits(['tsComCallback']);
var iclass = props.iclass ? props.iclass : 'w-20';
var sclass = props.iclass ? props.iclass : 'w-10';

</script>
<template>
    <!-- <div class="flex items-center gap-1">
        <span class="label-text-alt ">{{props.label}}</span>
        <input type="text" class="input input-xs input-accent w-full max-w-xs" @change="emit('tsComCallback', props.name, $event.target.value);" :value="props.init"/>
    </div> -->


    <div class="join flex justify-center items-center gap-1">
        <span class="label-text-alt join-item text-xs" :class="sclass">{{props.label}}</span>
        <input type="text" class="join-item input input-xs input-accent max-w-xs" :class="iclass" @input="emit('tsComCallback', props.name, $event.target.value);" :value="props.init"/>
    </div>

    <!-- <div class="join grid grid-cols-2 place-items-center">
        <span class="label-text-alt join-item text-xs">{{props.label}}</span>
        <input type="text" class="join-item input input-xs input-accent max-w-xs" :class="iclass" @change="emit('tsComCallback', props.name, $event.target.value);" :value="props.init"/>
    </div> -->

</template>