<script setup>
import { ref, watch } from 'vue';

const props = defineProps(['name','init','label','def','min','max','step']);
const emit = defineEmits(['tsComCallback']);
const itemValue = ref(props.def??0);

// console.log(props.min, typeof(props.min),props.name,);
// console.log(props.def, typeof(props.def),props.name);
// console.log(props.max, typeof(props.max),props.name);

function adjustValue(act){
    // console.log(itemValue.value, typeof(itemValue.value));
    let ival = Number(itemValue.value);
    // console.log(ival, typeof(ival));
    let step = props.step? Number(props.step) : 1;
    // console.log(step, typeof(step));
    // console.log(ival, typeof(ival));
    if(act == 'up'){
        ival += step ;
    }else{
        ival -= step ;
    }
    ival = ival > Number(props.max) ?  Number(props.max)  : ival;
    ival = ival < Number(props.min) ? Number(props.min)  : ival;
    itemValue.value = ival.toFixed(2).toString();
    // emit('tsComCallback', props.name, itemValue.value)
}
// console.log(props.name,' props.def = ',itemValue.value, typeof(itemValue.value),'min=',props.min,'max=',props.max);

watch(()=>itemValue.value, (e)=>{
    emit('tsComCallback', props.name, Number(itemValue.value));
    // console.log('watch itemValue = ',itemValue.value, typeof(itemValue.value));
});

watch(()=>props.def, (e)=>{
    itemValue.value = props.def;
    // console.log(props.name,'watch props.def = ',itemValue.value, typeof(itemValue.value),'min=',props.min,'max=',props.max);
});

// const handleInput = (event) => {
//     itemValue.value = event.target.value;
//     emit('tsComCallback', props.name, itemValue.value)
//     // console.log( event.target.value);
// };


</script>

<template>

<label class="form-control w-full max-w-xs">
  <div class="label gap-x-2">
    <span class="label-text">{{props.label}}</span>
    <span class="label-text-alt">
        <!-- 右侧点击调整 -->
        <div class="flex flex-row gap-[2px]">
           
            <div class=" bg-gray-100 rounded-xl md:text-base flex items-center justify-center cursor-default">
                <span class="">
                    <input :value="itemValue" :name="props.name" class="peer text-xs block min-h-[auto] rounded border-0 bg-transparent px-3 py-[0.32rem] leading-[1.6] transition-all duration-200 ease-linear focus:placeholder:opacity-100 peer-focus:text-primary data-[twe-input-state-active]:placeholder:opacity-100 motion-reduce:transition-none [&:not([data-twe-input-placeholder-active])]:placeholder:opacity-0"/>
                </span>
            </div>

            <div class="buttons-wrap flex flex-col items-center gap-[1px]">
                <button @click="adjustValue('up')" class=" h-[19px] text-[#647484] w-[18px] bg-[#edeff1] hover:bg-gray-400 flex items-center justify-center">
                    △
                </button>
                <button @click="adjustValue('down')" class="h-[19px] text-[#647484] w-[18px] bg-[#edeff1] hover:bg-gray-400 flex items-center justify-center">
                    ▽
                </button>
            </div>
        </div>
    </span>
  </div>
  <!-- v-model="itemValue"   @change="handleInput" -->
  <input type="range" :min="props.min" :max="props.max" :id="props.name+'_line'" :step="props.step" class="range range-primary range-xs" v-model="itemValue"  /> 
  <!-- {{itemValue}}，{{props.min}}，{{props.max}} -->
</label>



</template>