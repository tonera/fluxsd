<script setup>
import { ref } from 'vue';
import { trans } from 'laravel-vue-i18n';

const props = defineProps(['init','name', 'img']);
const emit = defineEmits(['btnAction']);

// console.log(props.init);

//当前操作动作按钮 u1 u2...
const cBtn = ref({act:null, img:null});

function handleButton(btn){
  cBtn.value = {act:btn.act, img:props.img};
  // console.log(cBtn.value);
  emit('btnAction', cBtn.value);
}

</script>

<style>

</style>

<template>
  <div class="mt-4">
    <div class="flex-row sm:w-11/12 lg:w-3/4 mx-auto text-center grid justify-items-center">
        <!--图片操作按钮-->
            <div v-if="props.init != null" class="grid justify-items-center">
              <div v-for="(buttons, index) in props.init" :key="'IB_'+index" class="items-center inline-flex">
              <button v-for="(btn, idx) in buttons" @click="handleButton(btn)" :key="'BS_'+idx" type="button" :class="cBtn.act == btn.act?'bg-red-800 hover:bg-red-900':'bg-gray-600 hover:bg-gray-900'" class="text-white bg-gray-800  focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-full text-sm px-5 py-2.5 mr-2 mb-2" :disabled="cBtn.act == btn.act">{{btn.label}}</button>
            </div>
            </div>
      </div>
    </div>
</template>