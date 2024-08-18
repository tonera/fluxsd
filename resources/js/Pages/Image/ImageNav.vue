<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref ,watch} from 'vue';

const props = defineProps(['init','def','name']);
const emit = defineEmits(['tsComCallback']);
const currentItem = ref(props.def);

// console.log(currentItem.value,props.def);

function setValue(item, index){
    currentItem.value = item.engine;
    try{
        // console.log(currentIndex.value);
        emit('tsComCallback', props.name, item);
    }catch(error){
        console.log(error);
    }
}
watch(() =>props.def, (e)=>{
    currentItem.value = props.def;
});

</script>
<template>
    <div class=" grid">
        <div role="tablist" class="tabs tabs-boxed tabls-sm justify-self-start">
            <a 
                v-for="(item, index) in props.init" 
                :key="index" 
                :class="{'tab-active': item.engine == currentItem }"
                @click="setValue(item, index)"
                role="tab" 
                class="tab">{{item.label}}
                
            </a>
        </div>
    </div>
</template>