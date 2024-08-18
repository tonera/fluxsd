import { reactive } from 'vue'
// import { trans } from 'laravel-vue-i18n';
//ts公共数据结构
//用于生成图片的参数
export const oTask = reactive({
    act:'MK',
    engine:'atz',
    steps:30,
    seed:0,
    cfg_scale:7,
    prompt:'',
    negative_prompt:'',
    width:768,
    height:1024,
    model_hash_id:'3af8e8198954317e706b306f6817ca35',
    image_num:2,
    upscale:2,

    task_id:'',//返回数据
    prompt_en:'',
    negative_prompt_en:'',
    short_name:'',
});
//图片对象
export const oImage = reactive({
    id:'',
    show_url:'',
    thumb:'',
    uri:'',
    width:0,
    height:0,
    seed:0,
    buttonGroups:[],
});

