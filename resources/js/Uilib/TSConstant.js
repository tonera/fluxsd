export const ANGLELIST = [
    {label:'-180°',val:-180},
    {label:'-150°',val:-150},
    {label:'-120°',val:-120},
    {label:'-90°',val:-90},
    {label:'-60°',val:-60},
    {label:'-45°',val:-45},
    {label:'-30°',val:-30},
    {label:'0°',val:0},
    {label:'30°',val:30},
    {label:'45°',val:45},
    {label:'60°',val:60},
    {label:'90°',val:90},
    {label:'120°',val:120},
    {label:'150°',val:150},
    {label:'180°',val:180},
];
export const LINEHEIGHTLIST = [
    {label:'1',val:1},
    {label:'1.2',val:1.2},
    {label:'1.5',val:1.5},
    {label:'2',val:2},
    {label:'2.5',val:2.5},
    {label:'3',val:3},
    {label:'4',val:4},
    {label:'5',val:5},
    {label:'6',val:6},
];
export const FONTSIZELIST = [
    {label:'10',val:10},
    {label:'15',val:15},
    {label:'18',val:18},
    {label:'20',val:20},
    {label:'24',val:24},
    {label:'28',val:28},
    {label:'36',val:36},
    {label:'40',val:40},
    {label:'44',val:44},
    {label:'50',val:50},
    {label:'60',val:60},
    {label:'70',val:70},
    {label:'90',val:90},
    {label:'100',val:100},
    {label:'200',val:200},
    {label:'300',val:300},
];
// export const FONTFAMILYLIST = [
//     {label:'Arial',val:'Arial'},
//     {label:'Arial Black',val:'Arial Black'},
//     {label:'Courier New',val:'Courier New'},
//     {label:'Georgia',val:'Georgia'},
//     {label:'Impact',val:'Impact'},
//     {label:'Lucida Console',val:'Lucida Console'},
//     {label:'LucidaSans Unicode',val:'Lucida Sans Unicode'},];
export const CHARSPACINGLIST = [
    {label:'1',val:0},
    {label:'50',val:50},
    {label:'100',val:100},
    {label:'200',val:200},
    {label:'300',val:300},
    {label:'400',val:400},
    {label:'500',val:500},
];
export const SHADOWOFFSETLIST = [
    {label:'-50',val:-50},
    {label:'-40',val:-40},
    {label:'-30',val:-30},
    {label:'-20',val:-20},
    {label:'-10',val:-10},
    {label:'0',val:0},
    {label:'10',val:10},
    {label:'20',val:20},
    {label:'30',val:30},
    {label:'40',val:40},
    {label:'50',val:50},
];

export const FILTERLIST = [
    {label:'正常',val:'normal'},
    {label:'老照片',val:'heiti'},
];

//照片 cm ratio:28.3464  inch 72 
export const TPL_Photo = [
    {label:'PS size',w:16,h:12,unit:'cm',vh:true},
    {label:'Mobile poster',w:1080,h:1980,unit:'pixel',vh:false},
    {label:'Common poster',w:13,h:18,unit:'cm',vh:false},
    {label:'3x2 Inch',w:3,h:2,unit:'inch',vh:true},
    {label:'6x4 Inch',w:6,h:4,unit:'inch',vh:true},
    {label:'7x5 Inch',w:7,h:5,unit:'inch',vh:true},
    {label:'8x6 Inch',w:8,h:6,unit:'inch',vh:true},
    {label:'10x8 Inch',w:10,h:8,unit:'inch',vh:true},
    {label:'2x3 Inch',w:2,h:3,unit:'inch',vh:false},
    {label:'4x6 Inch',w:4,h:6,unit:'inch',vh:false},
    {label:'5x7 Inch',w:5,h:7,unit:'inch',vh:false},
    {label:'8x10 Inch',w:8,h:10,unit:'inch',vh:false},
];
//打印
export const TPL_Print = [
    {label:'Letter',w:8.5,h:11,unit:'inch',vh:false},
    {label:'Legal',w:8.5,h:14,unit:'inch',vh:false},
    {label:'Tabloid',w:11,h:17,unit:'inch',vh:false},
    {label:'A4',w:210,h:297,unit:'mm',vh:false},
    {label:'A5',w:148,h:210,unit:'mm',vh:false},
    {label:'A6',w:105,h:148,unit:'mm',vh:false},
    {label:'A3',w:297,h:420,unit:'mm',vh:false},
    {label:'B5',w:176,h:250,unit:'mm',vh:false},
    {label:'B4',w:250,h:353,unit:'mm',vh:false},
    {label:'B3',w:353,h:500,unit:'mm',vh:false},
    {label:'C4',w:229,h:324,unit:'mm',vh:false},
    {label:'C5',w:162,h:229,unit:'mm',vh:false},
    {label:'C6',w:114,h:162,unit:'mm',vh:false},
    {label:'DL',w:110,h:220,unit:'mm',vh:false},
];
export const TPL_Illustration = [
    {label:'1000 pixel',w:1000,h:1000,unit:'pixel',vh:false},
    {label:'2000 pixel',w:2000,h:2000,unit:'pixel',vh:false},
    {label:'Poster',w:18,h:24,unit:'inch',vh:false},
    {label:'1920x1080',w:1920,h:1080,unit:'pixel',vh:true},
    {label:'1280x720',w:1280,h:720,unit:'pixel',vh:true},
];
export const sdAspList = [
    {label:'1:1', height:1024, width:1024, id:0, idx:0},
    {label:'9:7', height:1152, width:896, id:1, idx:1},
    {label:'19:13', height:1216, width:832, id:2, idx:2},
    {label:'7:4', height:1344, width:768, id:3, idx:3},
    {label:'12:5', height:1526, width:640, id:4, idx:4},
    {label:'5:12', height:640, width:1526, id:5, idx:5},
    {label:'4:7', height:768, width:1344, id:6, idx:6},
    {label:'13:19', height:832, width:1216, id:7, idx:7},
    {label:'7:9', height:896, width:1152, id:8, idx:8},
];
export const omAspList = [
    {label:'1:1', height:1024, width:1024, id:0, idx:0},
    {label:'9:16', height:576, width:1024, id:1, idx:1},
    {label:'16:9', height:1024, width:576, id:2, idx:2},
    {label:'4:3', height:1024, width:768, id:3, idx:3},
    {label:'3:2', height:1024, width:680, id:4, idx:4},
    {label:'3:4', height:768, width:1024, id:5, idx:5},
    {label:'2:1', height:1024, width:512, id:6, idx:6},
    {label:'1:2', height:512, width:1024, id:7, idx:7},
];


