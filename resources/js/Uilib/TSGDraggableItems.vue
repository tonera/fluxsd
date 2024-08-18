<script setup>
import { onMounted, ref,watch } from 'vue';
import draggable from 'vuedraggable'
import {trans} from 'laravel-vue-i18n';
//组件：canvas对象菜单
const props = defineProps(['name','init','label','def']);
const emit = defineEmits(['deleteObject','setActiveObject','tsComCallback','moveObject','showCropObject','removeBG']);
const currentIndex = ref();


function deleteObject(index){
  // console.log('删除', item);
  emit('deleteObject', index);
}
function setActiveObject(index){
  if(index == null) return;
  // console.log('setActiveObject', index);
  emit('setActiveObject', index);
  currentIndex.value = index;
}
watch(() => props.def, () => {
    // console.log('watchwatchwatchwatchwatchwatch');
    currentIndex.value = props.def;
    // console.log(currentIndex.value);
});
var dragging = false;
function checkEnd(e) {
    console.log(e);
    console.log('move, id=', currentIndex.value,'to=',e.newIndex);
    dragging = false;
    emit('moveObject', e.oldIndex,  e.newIndex);
}

//去背景 
// console.log(props.init);<img class="w-8 h-8" :src="item.toDataURL({format:'jpeg',multiplier:1})"/>

</script>
<template>
<div class="overflow-y-scroll max-h-[420px] overflow-x-hidden">
<!-- 图文名称 -->
<draggable :list="props.init" itemKey="id" :disabled="false" class="list-group" ghost-class="ghost" 
    @start="dragging = true" @end="checkEnd" :animation="200">
    <template #item="{element, index}">
        <div class="list-group-item" :class="{ 'not-draggable': false }">
            <li class="p-3 my-1.5 flex justify-between items-center bg-gray-700 hover:bg-gray-600 shadow rounded-lg" :class=" {'dark:bg-gray-600':currentIndex == index}">
              <div class="flex cursor-pointer">
                <!-- 隐藏显示对象 -->
                <svg v-if="element.get('visible')" @click="emit('tsComCallback','visible',false,index)" class="w-[18px] h-[18px] mr-1 dark:hover:fill-slate-50" fill="currentColor" viewBox="0 0 576 512" xmlns="http://www.w3.org/2000/svg">
                <path d="M288 80c-65.2 0-118.8 29.6-159.9 67.7C89.6 183.5 63 226 49.4 256c13.6 30 40.2 72.5 78.6 108.3C169.2 402.4 222.8 432 288 432s118.8-29.6 159.9-67.7C486.4 328.5 513 286 526.6 256c-13.6-30-40.2-72.5-78.6-108.3C406.8 109.6 353.2 80 288 80zM95.4 112.6C142.5 68.8 207.2 32 288 32s145.5 36.8 192.6 80.6c46.8 43.5 78.1 95.4 93 131.1c3.3 7.9 3.3 16.7 0 24.6c-14.9 35.7-46.2 87.7-93 131.1C433.5 443.2 368.8 480 288 480s-145.5-36.8-192.6-80.6C48.6 356 17.3 304 2.5 268.3c-3.3-7.9-3.3-16.7 0-24.6C17.3 208 48.6 156 95.4 112.6zM288 336c44.2 0 80-35.8 80-80s-35.8-80-80-80c-.7 0-1.3 0-2 0c1.3 5.1 2 10.5 2 16c0 35.3-28.7 64-64 64c-5.5 0-10.9-.7-16-2c0 .7 0 1.3 0 2c0 44.2 35.8 80 80 80zm0-208a128 128 0 1 1 0 256 128 128 0 1 1 0-256z"></path>
                </svg>
                <svg v-else  @click="emit('tsComCallback','visible',true,index)" class="w-[18px] h-[18px] mr-1 dark:hover:fill-slate-50" fill="currentColor" viewBox="0 0 640 512" xmlns="http://www.w3.org/2000/svg">
                <path d="M38.8 5.1C28.4-3.1 13.3-1.2 5.1 9.2S-1.2 34.7 9.2 42.9l592 464c10.4 8.2 25.5 6.3 33.7-4.1s6.3-25.5-4.1-33.7L525.6 386.7c39.6-40.6 66.4-86.1 79.9-118.4c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C465.5 68.8 400.8 32 320 32c-68.2 0-125 26.3-169.3 60.8L38.8 5.1zm151 118.3C226 97.7 269.5 80 320 80c65.2 0 118.8 29.6 159.9 67.7C518.4 183.5 545 226 558.6 256c-12.6 28-36.6 66.8-70.9 100.9l-53.8-42.2c9.1-17.6 14.2-37.5 14.2-58.7c0-70.7-57.3-128-128-128c-32.2 0-61.7 11.9-84.2 31.5l-46.1-36.1zM394.9 284.2l-81.5-63.9c4.2-8.5 6.6-18.2 6.6-28.3c0-5.5-.7-10.9-2-16c.7 0 1.3 0 2 0c44.2 0 80 35.8 80 80c0 9.9-1.8 19.4-5.1 28.2zm9.4 130.3C378.8 425.4 350.7 432 320 432c-65.2 0-118.8-29.6-159.9-67.7C121.6 328.5 95 286 81.4 256c8.3-18.4 21.5-41.5 39.4-64.8L83.1 161.5C60.3 191.2 44 220.8 34.5 243.7c-3.3 7.9-3.3 16.7 0 24.6c14.9 35.7 46.2 87.7 93 131.1C174.5 443.2 239.2 480 320 480c47.8 0 89.9-12.9 126.2-32.5l-41.9-33zM192 256c0 70.7 57.3 128 128 128c13.3 0 26.1-2 38.2-5.8L302 334c-23.5-5.4-43.1-21.2-53.7-42.3l-56.1-44.2c-.2 2.8-.3 5.6-.3 8.5z"></path>
                </svg>
                <!-- 锁定对象 -->
                <svg v-if="element.get('selectable')" @click="emit('tsComCallback','selectable',false,index)" class="mr-1 w-[18px] h-[18px] dark:hover:fill-slate-50" fill="currentColor" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg">
                <path d="M144 144c0-44.2 35.8-80 80-80c31.9 0 59.4 18.6 72.3 45.7c7.6 16 26.7 22.8 42.6 15.2s22.8-26.7 15.2-42.6C331 33.7 281.5 0 224 0C144.5 0 80 64.5 80 144v48H64c-35.3 0-64 28.7-64 64V448c0 35.3 28.7 64 64 64H384c35.3 0 64-28.7 64-64V256c0-35.3-28.7-64-64-64H144V144z"></path>
                </svg>
                <svg v-else @click="emit('tsComCallback','selectable',true,index)" class="mr-1 w-[18px] h-[18px] dark:hover:fill-slate-50" fill="yellow" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg">
                <path d="M144 144v48H304V144c0-44.2-35.8-80-80-80s-80 35.8-80 80zM80 192V144C80 64.5 144.5 0 224 0s144 64.5 144 144v48h16c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V256c0-35.3 28.7-64 64-64H80z"></path>
                </svg>

              </div>
           
                <div class="flex justify-start items-center w-full overflow-clip cursor-pointer ml-2 h-8"  @click="setActiveObject(index)">
                  <div v-if="element.type == 'image'">
                    <img class="w-8 h-8" :src="element.getSrc()"/>
                  </div>
                  <div v-else-if="element.type == 'i-text' || element.type == 'text'" class="whitespace-nowrap overflow-clip">
                    {{element.text}}
                  </div>
                  <div v-else>
                    {{element.type}}
                  </div>
                </div>
    
                <div class="flex gap-2">
                  <!-- 去背景 -->
                  <div v-show="element.type == 'image'" class="tooltip tooltip-accent" :data-tip="trans('Remove BG')">
                    <svg @click="emit('removeBG', index)" class="dark:hover:fill-slate-50 w-[20px] h-[20px] cursor-pointer" fill="currentColor" viewBox="0 0 576 512" xmlns="http://www.w3.org/2000/svg">
                    <path d="M290.7 57.4L57.4 290.7c-25 25-25 65.5 0 90.5l80 80c12 12 28.3 18.7 45.3 18.7H288h9.4H512c17.7 0 32-14.3 32-32s-14.3-32-32-32H387.9L518.6 285.3c25-25 25-65.5 0-90.5L381.3 57.4c-25-25-65.5-25-90.5 0zM297.4 416H288l-105.4 0-80-80L227.3 211.3 364.7 348.7 297.4 416z"></path>
                    </svg>
                  </div>
                  
                  <div v-show="element.type == 'image'" class="tooltip tooltip-accent" :data-tip="trans('Crop')">
                    <svg @click="emit('showCropObject', index)" class="dark:hover:fill-slate-50 w-[18px] h-[18px] cursor-pointer" fill="currentColor"  viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                    <path d="M128 32c0-17.7-14.3-32-32-32S64 14.3 64 32V64H32C14.3 64 0 78.3 0 96s14.3 32 32 32H64V384c0 35.3 28.7 64 64 64H352V384H128V32zM384 480c0 17.7 14.3 32 32 32s32-14.3 32-32V448h32c17.7 0 32-14.3 32-32s-14.3-32-32-32H448l0-256c0-35.3-28.7-64-64-64L160 64v64l224 0 0 352z"></path>
                    </svg>
                  </div>
                  

                    <!-- 删除对象 -->
                    <div class="tooltip tooltip-accent" :data-tip="trans('Delete')">
                      <svg @click="deleteObject(index)" class="dark:hover:fill-slate-50 w-[18px] h-[18px] cursor-pointer" fill="currentColor"  viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg">
                      <path d="M170.5 51.6L151.5 80h145l-19-28.4c-1.5-2.2-4-3.6-6.7-3.6H177.1c-2.7 0-5.2 1.3-6.7 3.6zm147-26.6L354.2 80H368h48 8c13.3 0 24 10.7 24 24s-10.7 24-24 24h-8V432c0 44.2-35.8 80-80 80H112c-44.2 0-80-35.8-80-80V128H24c-13.3 0-24-10.7-24-24S10.7 80 24 80h8H80 93.8l36.7-55.1C140.9 9.4 158.4 0 177.1 0h93.7c18.7 0 36.2 9.4 46.6 24.9zM80 128V432c0 17.7 14.3 32 32 32H336c17.7 0 32-14.3 32-32V128H80zm80 64V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16zm80 0V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16zm80 0V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16z"></path>
                      </svg>
                    </div>
                    
                </div>
            </li>
        </div>
    </template>
</draggable>


</div>




</template>