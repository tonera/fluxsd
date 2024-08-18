<script setup>
import { watch, ref } from 'vue';


const props = defineProps(['name','init','label','def']);
const emit = defineEmits(['tsComCallback']);
const show_more = ref(false);
const cItem = ref( {name:props.name,val:props.def??''});

if(props.init != undefined && props.init.length > 0){
    cItem.value = props.init[0];
}

// console.log('props.init=',props.init.length);

function setValue(item){
    try{
        emit('tsComCallback', props.name, item);
        show_more.value = false;
        cItem.value = item;
    }catch(error){
        console.log(error);
    }
}
function reset(){
    emit('tsComCallback', props.name, '');
    show_more.value = true;
    cItem.value =  {name:props.name,val:''};
}
watch(()=>props.def, (e)=>{
    cItem.value.val = props.def;
});

</script>

<template>
<div class="join py-4">
    <label class="font-semibold grid join-item btn items-center justify-items-center text-center">{{props.label}} </label>
    <div class="relative join-item">
      <div class=" h-12 bg-white flex border border-gray-200 rounded items-center">
        <input @click="show_more = !show_more" :value="cItem.val" name="select" id="select" class="peer block min-h-[auto] w-full rounded border-0 bg-transparent px-3 py-[0.32rem] leading-[1.6] outline-none transition-all duration-200 ease-linear focus:placeholder:opacity-100 peer-focus:text-primary data-[twe-input-state-active]:placeholder:opacity-100 motion-reduce:transition-none [&:not([data-twe-input-placeholder-active])]:placeholder:opacity-0" checked/>

        <button 
            @click="reset"
            class="cursor-pointer outline-none focus:outline-none transition-all text-gray-300 hover:text-gray-600">
          <svg class="w-4 h-4 mx-2 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
        <label :for="'show_more'+props.name" 
          class="cursor-pointer outline-none focus:outline-none border-l border-gray-200 transition-all text-gray-300 hover:text-gray-600">
          <svg class="w-4 h-4 mx-2 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="18 15 12 9 6 15"></polyline>
          </svg>
        </label>
      </div>

      <input type="checkbox" name="show_morew" :id="'show_more'+props.name" class="hidden peer" :checked="show_more" />

      <div class="absolute rounded shadow bg-white overflow-hidden hidden peer-checked:flex flex-col w-full mt-1 border border-gray-200">
        <div 
            v-for="(item, index) in props.init" :key="index+'_Sel'" 
            class="cursor-pointer group border-t"
            @click="setValue(item)"
        >
          <a class="block p-2 border-transparent border-l-4 group-hover:border-blue-600 group-hover:bg-gray-100">{{item.label}}</a>
        </div>
      </div>

    </div>
  </div>
</template>