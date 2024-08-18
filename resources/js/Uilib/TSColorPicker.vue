<script setup>
import {ref , watch} from 'vue';
import { ColorPicker } from "vue3-colorpicker";
import "vue3-colorpicker/style.css";
//颜色选择器
const props = defineProps(['name','init','label']);
const emit = defineEmits(['tsComCallback']);
const color = ref(props.init);
//gradient 渐变 pure 纯色 https://github.com/aesoper101/vue3-colorpicker/blob/main/src/App.vue
//format "rgb" | "prgb" | "hex" | "hex6" | "hex3" | "hex4" | "hex8" | "name" | "hsl" | "hsv"
const changeColor = (val) => {
    emit('tsComCallback', props.name, val);
};
watch(() => props.init, () => {
    color.value = props.init;
});

function generateRandomString(length) {
  const possibleCharacters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  let result = '';

  for (let i = 0; i < length; i++) {
    result += possibleCharacters.charAt(Math.floor(Math.random() * possibleCharacters.length));
  }

  return result;
}

// // 示例：生成10位长度的随机字符串
// const randomString = generateRandomString(10);
// console.log(randomString);

</script>
<template>
    <div class="join flex justify-start items-center gap-1">
        <span class="label-text-alt join-item text-xs w-14">{{props.label}}</span>
        <ColorPicker format="rgb" shape="square"
            v-model:pureColor="color"
            @gradientColorChange="changeColor"
            @pureColorChange="changeColor" />
    </div>


</template>