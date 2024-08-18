<script setup>
//组件：文字-字体组件，包括6属性：字体-s，大小-s，行高-s，间距-s，颜色，背景色
import {ref } from 'vue';
import TSDropInput from './TSDropInput.vue';
import TSColorPicker from './TSColorPicker.vue';
import FontDropInput from './FontDropInput.vue';
import {trans} from 'laravel-vue-i18n';

const props = defineProps(['name','init','def','label', 'isDisabled']);
const emit = defineEmits(['tsComCallback']);
const showContent = ref(true);
// console.log(props.def);
// console.log(props.init);


function tsComCallback(name , value){
    console.log(name,'=====',value);
    emit('tsComCallback', name, value);
}

</script>
<style>
@import url('https://fonts.googleapis.com/css2?family=Abril+Fatface&family=Aoboshi+One&family=Archivo+Black&family=Bebas+Neue&family=Bungee+Hairline&family=Chewy&family=Coiny&family=Creepster&family=DM+Serif+Text:ital@0;1&family=Eater&family=Fascinate+Inline&family=Fredericka+the+Great&family=Indie+Flower&family=Londrina+Outline&family=Luckiest+Guy&family=Noto+Sans+SC:wght@100..900&family=Noto+Serif+JP:wght@200..900&family=Noto+Serif+SC:wght@200..900&family=Rammetto+One&family=Reenie+Beanie&family=Righteous&family=Rubik+Beastly&family=Rubik+Doodle+Triangles&family=Rubik+Moonrocks&family=Shojumaru&family=Titan+One&family=ZCOOL+KuaiLe&family=ZCOOL+XiaoWei&family=Zhi+Mang+Xing&display=swap');
.liu-jian-mao-cao-regular {
  font-family: "Liu Jian Mao Cao", cursive;
  font-weight: 400;
  font-style: normal;
}
.zhi-mang-xing-regular {
  font-family: "Zhi Mang Xing", cursive;
  font-weight: 400;
  font-style: normal;
}
.zcool-xiaowei-regular {
  font-family: "ZCOOL XiaoWei", sans-serif;
  font-weight: 400;
  font-style: normal;
}

.zcool-kuaile-regular {
  font-family: "ZCOOL KuaiLe", sans-serif;
  font-weight: 400;
  font-style: normal;
}
.titan-one-regular {
  font-family: "Titan One", sans-serif;
  font-weight: 400;
  font-style: normal;
}
.shojumaru-regular {
  font-family: "Shojumaru", system-ui;
  font-weight: 400;
  font-style: normal;
}
.rubik-moonrocks-regular {
  font-family: "Rubik Moonrocks", sans-serif;
  font-weight: 400;
  font-style: normal;
}
.rubik-doodle-triangles-regular {
  font-family: "Rubik Doodle Triangles", system-ui;
  font-weight: 400;
  font-style: normal;
}
.rubik-beastly-regular {
  font-family: "Rubik Beastly", system-ui;
  font-weight: 400;
  font-style: normal;
}
.righteous-regular {
  font-family: "Righteous", sans-serif;
  font-weight: 400;
  font-style: normal;
}
.reenie-beanie-regular {
  font-family: "Reenie Beanie", cursive;
  font-weight: 400;
  font-style: normal;
}
.rammetto-one-regular {
  font-family: "Rammetto One", sans-serif;
  font-weight: 400;
  font-style: normal;
}
.luckiest-guy-regular {
  font-family: "Luckiest Guy", cursive;
  font-weight: 400;
  font-style: normal;
}
.londrina-outline-regular {
  font-family: "Londrina Outline", sans-serif;
  font-weight: 400;
  font-style: normal;
}
.indie-flower-regular {
  font-family: "Indie Flower", cursive;
  font-weight: 400;
  font-style: normal;
}
.fredericka-the-great-regular {
  font-family: "Fredericka the Great", serif;
  font-weight: 400;
  font-style: normal;
}
.fascinate-inline-regular {
  font-family: "Fascinate Inline", system-ui;
  font-weight: 400;
  font-style: normal;
}
.eater-regular {
  font-family: "Eater", serif;
  font-weight: 400;
  font-style: normal;
}
.dm-serif-text-regular {
  font-family: "DM Serif Text", serif;
  font-weight: 400;
  font-style: normal;
}
.coiny-regular {
  font-family: "Coiny", system-ui;
  font-weight: 400;
  font-style: normal;
}
.chewy-regular {
  font-family: "Chewy", system-ui;
  font-weight: 400;
  font-style: normal;
}
.bungee-hairline-regular {
  font-family: "Bungee Hairline", sans-serif;
  font-weight: 400;
  font-style: normal;
}
.archivo-black-regular {
  font-family: "Archivo Black", sans-serif;
  font-weight: 400;
  font-style: normal;
}
.aoboshi-one-regular {
  font-family: "Aoboshi One", serif;
  font-weight: 400;
  font-style: normal;
}

</style>
<template>
    <div>
        <div class="divider divider-start label-text-alt cursor-pointer">
            {{props.label}}
         
        </div>
        <div v-show="showContent" class="grid grid-cols-2 gap-2 ml-2">
            <!-- 对像宽高 'name','init','label' -->
            <div class="flex flex-col gap-1">
                <FontDropInput :name="'fontFamilyDef'" :init="props.init?.fontFamily" :label="trans('Font')" @tsComCallback="tsComCallback" :def="props.init?.fontFamilyDef"></FontDropInput>
                <TSDropInput :name="'fontSizeDef'" :init="props.init?.fontSize" :label="trans('Size')" @tsComCallback="tsComCallback" :def="props.init?.fontSizeDef"></TSDropInput>
                <TSColorPicker :name="'fontColor'" :init="props.init?.fontColor" :label="trans('Color')" @tsComCallback="tsComCallback"></TSColorPicker>
            </div>
            <!-- 偏移坐标 -->
            <div class="flex flex-col gap-1  grow">
                <TSDropInput :name="'lineHeightDef'" :init="props.init?.lineHeight" :label="trans('Line-height')" @tsComCallback="tsComCallback" :def="props.init?.lineHeightDef"></TSDropInput>
                <TSDropInput :name="'charSpacingDef'" :init="props.init?.charSpacing" :label="trans('Spacing')" @tsComCallback="tsComCallback" :def="props.init?.charSpacingDef"></TSDropInput>
                <TSColorPicker :name="'fontBackground'" :init="props.init?.fontBackground" :label="trans('Background')" @tsComCallback="tsComCallback"></TSColorPicker>
            </div>
            <div class="gap-1">
                
                
            </div>
        </div>
    </div>
</template>