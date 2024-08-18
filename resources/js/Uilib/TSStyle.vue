<script setup>
import { ref } from 'vue';

const props = defineProps(['name','init','def']);
const emit = defineEmits(['tsComCallback']);


</script>

<template>

<div class="py-2 overflow-scroll">
    <button @click="emit('tsComCallback', props.name, item)"
    v-for="(item, index) in props.init" :key="index" class="btn btn-sm bg-slate-100 m-1">
    {{item}}
</button>
</div>

</template>