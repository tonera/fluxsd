<script setup>
import { ref } from 'vue';

const props = defineProps(['label','isRunning']);


</script>

<template>
<div>

    <button class="btn btn-primary" :disabled="isRunning">{{props.label}}</button>
    
</div>

</template>