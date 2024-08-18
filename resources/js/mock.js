import { reactive } from 'vue'
import { trans } from 'laravel-vue-i18n';

// const selectorInit = [
//     {'label':'PHP', 'value':'php'},
//     {'label':'Java', 'value':'java'},
//     {'label':'Swift', 'value':'Swift'},
//     {'label':'Ruby', 'value':'Ruby'},
//     {'label':'Python', 'value':'Python'},
// ];
// const aspList = [
//     {label:'1:1', height:1024, width:1024, id:0, idx:0},
//     {label:'9:7', height:1152, width:896, id:1, idx:1},
//     {label:'19:13', height:1216, width:832, id:2, idx:2},
//     {label:'7:4', height:1344, width:768, id:3, idx:3},
//     {label:'12:5', height:1526, width:640, id:4, idx:4},
//     {label:'5:12', height:640, width:1526, id:5, idx:5},
//     {label:'4:7', height:768, width:1344, id:6, idx:6},
//     {label:'13:19', height:832, width:1216, id:7, idx:7},
//     {label:'7:9', height:896, width:1152, id:8, idx:8},
// ];


// const imageList = [
//     {thumb:'https://cdn.aitezhu.cn/models/0/a/b/17a9bd84288a478e972720439bbf1.jpeg'},
//     {thumb:'https://cdn.aitezhu.cn/models/e/b/c/cb93e7c3a6ace0cb8ca79ec26f9ce.jpeg'},
//     {thumb:'https://cdn.aitezhu.cn/models/9/0/d/25fd13774507b71067e15de9f5a48.jpeg'},
// ];

const styleList = [];

const nijiStyle = [
    {label:'Normal',val:"normal"},
    {label:'Cute',val:"cute"},
    {label:'Expressive',val:"expressive"},
    {label:'Original',val:"original"},
    {label:'Scenic',val:"scenic"},
];
const upscaleOptions = [
    {label:'None',val:null},
    {label:'2X',val:"2x"},
    {label:'4X',val:"4x"},
];

export const store = reactive({
    // selectorInit:selectorInit,
    // aspList:aspList,
    // imageList:imageList,
    styleList:styleList,
    nijiStyle:nijiStyle,
    upscaleOptions:upscaleOptions,

    mjConfig: {
        denoising_strength:{min:'0', max:'2', def:'1', label:trans('Denoising strength'), val:1,step:'1'},
        seed:{min:'0', max:'4294967295', def:'0', label:trans('Seed'), val:'0'},
        cfg_scale:{min:'0', max:'1000', def:'100', label:trans('Scale'), val:'100'},
        steps:{min:'0.25', max:'1', def:'1', label:trans('Steps'), val:'1', step:'0.25'},
        niji:[{key:'normal', val:trans('Normal')},{key:'cute',val:trans('Cute')},{key:'expressive',val:trans('Expressive')},{key:'original',val:trans('Original')},{key:'scenic',val:trans('Scenic')}],
        weird:{min:'0', max:'3000', def:'100', label:trans('Weird'), val:'0'},
        sampler:{label:trans('Sampler'), val:''},
        cref:{min:'0', max:'100', def:'0', label:trans('cref'), val:'0'},
        sref:{min:'0', max:'10', def:'0', label:trans('sref'), val:'0'},
    },
    sdConfig : {
        denoising_strength:{min:'0', max:'1', def:'0.4', label:trans('Denoising strength'), val:'0.4', step:'0.01'},
        seed:{min:'-1', max:'4294967295', def:'0', label:trans('Seed'), val:'0'},
        cfg_scale:{min:'0', max:'30', def:'7', label:trans('Scale'), val:'7'},
        steps:{min:'0', max:'50', def:'30', label:trans('Steps'), val:'30',step:'1'},
        weird:{min:'0', max:'0', def:'0', label:trans('Weird'), val:'0'},
        sampler:{label:trans('Sampler'), val:'K_DPM_2'}
    },

});