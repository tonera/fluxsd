import{r as f,y as J,o as i,d,a as e,t as b,F as O,g as L,p as K,j as ne,n as U,Q as ue,k as z,z as N,u as h,q as c,b as $,f as me,A as we,e as E,B as ke,c as H,w as ae,s as oe,O as ye}from"./app-DZF5fI4k.js";import{_ as Ce}from"./AppLayout-CwRrSe3A.js";import{a as X,s as G,g as $e,c as je,b as Se,e as Te}from"./network-P3ZlaVeY.js";import{_ as q}from"./TSSwitch-BL7Pt5DA.js";import{_ as V}from"./TSSlider-DJC396E-.js";import Ie from"./ImageNav-D5Glp6FP.js";import{_ as ze}from"./_plugin-vue_export-helper-DlAUqK2U.js";import{P as Be,E as Ne}from"./pusher-BgfirmB5.js";import"./TopSnackbar-CzC_MYJf.js";const Me={class:"join py-4"},Oe={class:"font-semibold grid join-item btn items-center justify-items-center text-center"},Le={class:"relative join-item"},Pe={class:"h-12 bg-white flex border border-gray-200 rounded items-center"},Ae=["value"],Ee=e("svg",{class:"w-4 h-4 mx-2 fill-current",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24",stroke:"currentColor","stroke-width":"2","stroke-linecap":"round","stroke-linejoin":"round"},[e("line",{x1:"18",y1:"6",x2:"6",y2:"18"}),e("line",{x1:"6",y1:"6",x2:"18",y2:"18"})],-1),Ve=[Ee],Je=["for"],Re=e("svg",{class:"w-4 h-4 mx-2 fill-current",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24",stroke:"currentColor","stroke-width":"2","stroke-linecap":"round","stroke-linejoin":"round"},[e("polyline",{points:"18 15 12 9 6 15"})],-1),He=[Re],De=["id","checked"],Fe={class:"absolute rounded shadow bg-white overflow-hidden hidden peer-checked:flex flex-col w-full mt-1 border border-gray-200 z-10"},Ge=["onClick"],Ue={class:"block p-2 border-transparent border-l-4 group-hover:border-blue-600 group-hover:bg-gray-100"},We={__name:"TSSelector",props:["name","init","label","def"],emits:["tsComCallback"],setup(k,{emit:C}){const t=k,g=C,a=f(!1),m=f({name:t.name,val:t.def??""});t.init!=null&&t.init.length>0&&(m.value=t.init[0]);function _(n){try{g("tsComCallback",t.name,n),a.value=!1,m.value=n}catch(o){console.log(o)}}function s(){g("tsComCallback",t.name,""),a.value=!0,m.value={name:t.name,val:""}}return J(()=>t.def,n=>{m.value.val=t.def}),(n,o)=>(i(),d("div",Me,[e("label",Oe,b(t.label),1),e("div",Le,[e("div",Pe,[e("input",{onClick:o[0]||(o[0]=u=>a.value=!a.value),value:m.value.val,name:"select",id:"select",class:"peer block min-h-[auto] w-full rounded border-0 bg-transparent px-3 py-[0.32rem] leading-[1.6] outline-none transition-all duration-200 ease-linear focus:placeholder:opacity-100 peer-focus:text-primary data-[twe-input-state-active]:placeholder:opacity-100 motion-reduce:transition-none [&:not([data-twe-input-placeholder-active])]:placeholder:opacity-0",checked:""},null,8,Ae),e("button",{onClick:s,class:"cursor-pointer outline-none focus:outline-none transition-all text-gray-300 hover:text-gray-600"},Ve),e("label",{for:"show_more"+t.name,class:"cursor-pointer outline-none focus:outline-none border-l border-gray-200 transition-all text-gray-300 hover:text-gray-600"},He,8,Je)]),e("input",{type:"checkbox",name:"show_morew",id:"show_more"+t.name,class:"hidden peer",checked:a.value},null,8,De),e("div",Fe,[(i(!0),d(O,null,L(t.init,(u,v)=>(i(),d("div",{key:v+"_Sel",class:"cursor-pointer group border-t",onClick:T=>_(u)},[e("a",Ue,b(u.label),1)],8,Ge))),128))])])]))}},qe={key:0,class:"inline-flex rounded-md shadow-sm overflow-clip",role:"group"},Ke=e("svg",{class:"w-[20px] h-[20px] fill-[#8e8e8e]",viewBox:"0 0 320 512",xmlns:"http://www.w3.org/2000/svg"},[e("path",{d:"M41.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.3 256 246.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"})],-1),Qe=[Ke],Xe=["onClick"],Ye=e("svg",{class:"w-[20px] h-[20px] fill-[#8e8e8e]",viewBox:"0 0 320 512",xmlns:"http://www.w3.org/2000/svg"},[e("path",{d:"M278.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-160 160c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L210.7 256 73.4 118.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l160 160z"})],-1),Ze=[Ye],et={key:1},ie=7,tt={__name:"Paginate",props:["total","cpage"],emits:["handleJump"],setup(k,{emit:C}){const t=C,g=k;var a=0,m=0;K({start:0,end:0});const _=ne(()=>{let o=[];a=g.cpage-Math.floor(ie/2),a=a<1?1:a,m=a+ie,m=m>g.total?g.total:m;for(let u=a;u<=m;u++){let v={label:u,page:u};o.push(v)}return o}),s=ne(()=>o=>({"bg-slate-300 hover:bg-slate-500 focus:bg-slate-300 ":g.cpage==o,"bg-neutral-100 hover:bg-slate-500":g.cpage!=o}));function n(o){o>g.total||o<1||t("handleJump",o)}return(o,u)=>k.total>1?(i(),d("div",qe,[e("button",{type:"button",class:"bg-neutral-100 rounded-l hover:bg-slate-500 inline-block px-3 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-neutral-800 transition duration-150 ease-in-out focus:ring-0 active:bg-neutral-200","data-te-ripple-init":"","data-te-ripple-color":"light",onClick:u[0]||(u[0]=v=>n(parseInt(g.cpage)-1))},Qe),(i(!0),d(O,null,L(_.value,(v,T)=>(i(),d("button",{type:"button",class:U(["inline-block px-6 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-neutral-800 transition duration-150 ease-in-out focus:ring-0 active:bg-neutral-200",s.value(v.page)]),"data-te-ripple-init":"","data-te-ripple-color":"light",onClick:R=>n(v.page),key:T},b(v.label),11,Xe))),128)),e("button",{type:"button",class:"bg-neutral-100 rounded-r hover:bg-slate-500 inline-block px-3 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-neutral-800 transition duration-150 ease-in-out focus:ring-0 active:bg-neutral-200","data-te-ripple-init":"","data-te-ripple-color":"light",onClick:u[1]||(u[1]=v=>n(parseInt(g.cpage)+1))},Ze)])):(i(),d("div",et))}},lt={class:"join py-4"},st={class:"font-semibold grid join-item btn items-center justify-items-center text-center"},nt={class:"relative join-item"},at={class:"h-12 bg-white flex border border-gray-200 rounded items-center"},ot=["src"],it=["value"],rt=e("svg",{class:"w-4 h-4 mx-2 fill-current",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24",stroke:"currentColor","stroke-width":"2","stroke-linecap":"round","stroke-linejoin":"round"},[e("line",{x1:"18",y1:"6",x2:"6",y2:"18"}),e("line",{x1:"6",y1:"6",x2:"18",y2:"18"})],-1),ct=[rt],dt=["for"],ut=e("svg",{class:"w-4 h-4 mx-2 fill-current",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24",stroke:"currentColor","stroke-width":"2","stroke-linecap":"round","stroke-linejoin":"round"},[e("polyline",{points:"18 15 12 9 6 15"})],-1),mt=[ut],pt=["id","checked"],_t={class:"absolute size-[48rem] rounded shadow bg-white overflow-hidden hidden peer-checked:flex flex-col mt-1 border border-gray-200 z-20"},gt={class:"flex justify-end pt-2 pr-4 gap-x-2 items-center"},ht={class:"text-xs",href:"/models",target:"_blank"},vt={class:"p-2 flex justify-between h-16"},ft={class:"menu lg:menu-horizontal z-30 w-40 justify-end"},bt={close:""},xt={class:"w-40 justify-end"},wt=["onClick"],kt={key:0,class:"w-5 h-5 fill-[#0eaf19]",viewBox:"0 0 448 512",xmlns:"http://www.w3.org/2000/svg"},yt=e("path",{d:"M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"},null,-1),Ct=[yt],$t={key:1,class:"w-5"},jt={class:"overscroll-auto overflow-auto"},St={class:"w-full grid grid-cols-3 lg:grid-cols-4 gap-2 px-2"},Tt=["onClick"],It={class:"absolute z-20 inset-0 bg-gray-900 opacity-0 transition-opacity duration-300 hover:opacity-80 flex items-center justify-center text-white whitespace-pre-wrap overflow-wrap-break-word break-all p-2"},zt={class:"absolute inset-0"},Bt=["src"],Nt={class:"p-2 flex justify-center"},re={__name:"TSSelectorModel",props:["name","init","label","def","engine","base"],emits:["tsComCallback"],setup(k,{emit:C}){ue();const t=k,g=C,a=f(!1),m=f({name:t.name,val:t.def??"",thumb:null}),_=f(1),s=f([]),n=f(0);var o=["1.5","pre1.5","sdxl","presdxl","presd3"];const u=K(o);function v(x){let S=u.indexOf(x);S!==-1?u.splice(S,1):u.push(x),T(1,t.init),console.log(u)}T(1,t.init),J(()=>t.engine,x=>{t.def==null&&(m.value={name:t.name,val:t.def??"",thumb:null}),T(1,t.init),_.value=1}),J(()=>t.base,x=>{u.splice(0,u.length),o=[],t.base=="1.5"?(u.push("1.5","pre1.5"),o.push("1.5","pre1.5")):(u.push("sdxl","presdxl","presd3"),o.push("sdxl","presdxl","presd3")),T(1,t.init)}),J(()=>t.def,x=>{m.value.val=t.def});async function T(x,S){let j=await X("get","/api/models",{page:x,engine:t.engine,types:JSON.stringify([S]),options:JSON.stringify(u),service:1});j.code==1&&(s.value=j.data,n.value=j.meta.last_page,o=j.base_models)}function R(x){_.value=x,T(x,t.init)}function D(x){if(t.engine=="lc"&&x.is_download!=1){G("You must download this model before using it","error");return}try{a.value=!1,m.value.val=x.sd_name,m.value.thumb=x.thumb,g("tsComCallback",t.name,x)}catch(S){console.log(S)}}function P(){g("tsComCallback",t.name,""),a.value=!0,m.value={name:t.name,val:""}}return(x,S)=>(i(),d("div",lt,[e("label",st,b(t.label),1),e("div",nt,[e("div",at,[z(e("img",{class:"object-cover w-12 h-12 pr-1",src:m.value.thumb},null,8,ot),[[N,m.value.thumb!=null]]),e("input",{onClick:S[0]||(S[0]=j=>a.value=!a.value),value:m.value.val,name:"select",id:"select",class:"peer block min-h-[auto] w-full rounded border-0 bg-transparent px-3 py-[0.32rem] leading-[1.6] outline-none transition-all duration-200 ease-linear focus:placeholder:opacity-100 peer-focus:text-primary data-[twe-input-state-active]:placeholder:opacity-100 motion-reduce:transition-none [&:not([data-twe-input-placeholder-active])]:placeholder:opacity-0",checked:""},null,8,it),e("button",{onClick:P,class:"cursor-pointer outline-none focus:outline-none transition-all text-gray-300 hover:text-gray-600"},ct),e("label",{for:"show_more"+t.name,class:"cursor-pointer outline-none focus:outline-none border-l border-gray-200 transition-all text-gray-300 hover:text-gray-600"},mt,8,dt)]),e("input",{type:"checkbox",name:"show_morew",id:"show_more"+t.name,class:"hidden peer",checked:a.value},null,8,pt),e("div",_t,[e("div",gt,[e("a",ht,b(h(c)("market"))+">>",1),e("button",{class:"btn btn-outline btn-sm",onClick:S[1]||(S[1]=j=>a.value=!1)},b(h(c)("close")),1)]),e("div",vt,[$(tt,{total:n.value,cpage:_.value,onHandleJump:R},null,8,["total","cpage"]),e("ul",ft,[e("li",null,[e("details",bt,[e("summary",xt,b(h(c)("filter")),1),e("ul",null,[(i(!0),d(O,null,L(h(o),(j,B)=>(i(),d("li",{key:"option_"+B},[e("a",{onClick:W=>v(j)},[u.includes(j)?(i(),d("svg",kt,Ct)):(i(),d("div",$t)),me(" "+b(j),1)],8,wt)]))),128))])])])])]),e("div",jt,[e("div",St,[(i(!0),d(O,null,L(s.value,(j,B)=>(i(),d("div",{key:"pl_"+B,class:"m-4 relative text-sm font-medium bg-gray-200 hover:bg-gray-300 cursor-pointer"},[e("div",{onClick:W=>D(j),class:"relative h-60"},[e("div",It,b(j.sd_name)+":"+b(j.base_model),1),e("div",zt,[e("img",{class:"object-cover h-full w-full",src:j.thumb},null,8,Bt)])],8,Tt),e("div",Nt,b(j.name),1)]))),128))])])])])]))}},Mt={class:"p-1"},Ot=["value","placeholder"],ce={__name:"TSTextArea",props:["name","init","label","def"],emits:["tsComCallback"],setup(k,{emit:C}){const t=k,g=C,a=f(t.def??""),m=_=>{a.value=_.target.value,g("tsComCallback",t.name,a.value)};return J(()=>t.def,_=>{a.value=t.def}),(_,s)=>(i(),d("div",Mt,[e("textarea",{value:a.value,onInput:m,class:"textarea textarea-bordered w-full",placeholder:t.label},`
    `,40,Ot)]))}},Lt={class:"flex gap-2"},Pt=["onClick"],At={class:"h-14 w-14 text-center place-items-center grid"},Et={class:"text-center font-serif text-gray-800"},Vt={__name:"TSAspect",props:["name","init","label","def"],emits:["tsComCallback"],setup(k,{emit:C}){const t=k,g=C,a=f(t.def??0);function m(_,s){g("tsComCallback",t.name,s),a.value=s}return(_,s)=>(i(),d("div",Lt,[(i(!0),d(O,null,L(t.init,(n,o)=>(i(),d("div",{onClick:u=>m(n,o),key:o,class:U(["bg-gray-300 text-center cursor-pointer",a.value==o?"border-solid border-4 border-green-500":""])},[e("div",At,[e("div",{class:"bg-white",style:we({height:n.height/30+"px",width:n.width/30+"px"})},null,4)]),e("div",Et,b(n.label),1)],10,Pt))),128))]))}},Jt={key:0,class:"m-1 h-full w-full relative inline-flex items-center p-3 text-sm font-medium text-center text-white bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700"},Rt=["src"],Ht=e("svg",{class:"w-[20px] h-[20px] fill-[#dd2222]",viewBox:"0 0 512 512",xmlns:"http://www.w3.org/2000/svg"},[e("path",{d:"M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM184 232H328c13.3 0 24 10.7 24 24s-10.7 24-24 24H184c-13.3 0-24-10.7-24-24s10.7-24 24-24z"})],-1),Dt=[Ht],Ft={key:1,for:"dropzone-file",class:"m-1 flex h-full w-full flex-col items-center justify-center border-2 border-gray-400 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-200"},Gt={class:"flex flex-col items-center justify-center p-4"},Ut={class:"mb-4 text-sm text-gray-500"},Wt={class:"font-semibold"},qt=e("p",{class:"text-xs text-gray-500"},"PNG or JPG",-1),Kt={__name:"TSUploadImage",props:["name","desc","def"],emits:["tsComCallback"],setup(k,{emit:C}){const t=k,g=f(t.def),a=C;function m(s){s.target.files[0]!==void 0&&($e(s.target.files[0]).then(n=>{g.value=n}).catch(n=>{console.log(n)}),a("tsComCallback",t.name,s.target.files[0]))}function _(){g.value=null,a("tsComCallback",t.name,null)}return J(()=>t.def,s=>{g.value=t.def}),(s,n)=>g.value!=null?(i(),d("div",Jt,[e("img",{src:g.value,class:"object-cover w-36 h-36"},null,8,Rt),e("button",{type:"button",class:"absolute inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white border-2 border-white rounded-full -top-2 -right-2 dark:border-gray-900",onClick:n[0]||(n[0]=o=>_(s.index))},Dt)])):(i(),d("label",Ft,[e("div",Gt,[e("p",Ut,[e("span",Wt,b(t.desc??""),1)]),e("input",{id:"dropzone-file",type:"file",multiple:"",accept:"image/png, image/jpeg",class:"hidden",onInput:n[1]||(n[1]=o=>m(o))},null,32),qt])]))}},Qt={class:"px-4 py-2 sm:px-6 lg:px-8 lg:py-2 mx-auto flex mb-2 sm:mb-2"},Xt={class:"oneline"},Yt=["width","height"],Zt=e("path",{"fill-rule":"evenodd",d:"M9.224 1.553a.5.5 0 0 1 .223.67L6.56 8l2.888 5.776a.5.5 0 1 1-.894.448l-3-6a.5.5 0 0 1 0-.448l3-6a.5.5 0 0 1 .67-.223z"},null,-1),el=[Zt],tl={id:"style-list",class:"flex flex-nowrap overscroll-auto overflow-auto"},ll=["onClick"],sl=["src"],nl={class:"oneline"},al=["width","height"],ol=e("path",{"fill-rule":"evenodd",d:"M6.776 1.553a.5.5 0 0 1 .671.223l3 6a.5.5 0 0 1 0 .448l-3 6a.5.5 0 1 1-.894-.448L9.44 8 6.553 2.224a.5.5 0 0 1 .223-.671z"},null,-1),il=[ol],rl={__name:"TSImageQueue",props:["name","init","def","tabstyle","moveto"],emits:["tsComCallback"],setup(k,{emit:C}){const t=k,g=C,a=f(t.def??0);J(()=>t.moveto,n=>{const o=t.moveto.split("_");console.log(o),o[0]=="next"?s(a.value+1):s(a.value-1)});const m={small:{btnw:30,btnh:70,blockw:"w-16",img:"w-16 h-16 object-cover cursor-pointer",pix:68},normal:{btnw:50,btnh:120,blockw:"w-32",img:"w-32 h-32 object-cover cursor-pointer",pix:132}},_=t.tabstyle=="normal"?m.normal:m.small;async function s(n){let o=parseInt(t.init.length);n=n<0?0:n,n=n>=o?o-1:n;let u=t.init[n];u.index=n,g("tsComCallback",t.name,u),a.value=n;let v=(n-2)*_.pix;document.getElementById("style-list").scrollTo({left:v,behavior:"smooth"})}return(n,o)=>z((i(),d("div",Qt,[e("div",Xt,[e("button",{class:"movebutton",type:"button",onClick:o[0]||(o[0]=u=>s(a.value-1))},[(i(),d("svg",{width:h(_).btnw,height:h(_).btnh,fill:"currentColor",class:"bi bi-chevron-compact-left",viewBox:"0 0 16 16"},el,8,Yt))])]),e("div",tl,[(i(!0),d(O,null,L(t.init,(u,v)=>(i(),d("div",{class:"text-center m-1",onClick:T=>s(v),key:"L"+v},[e("div",{class:U(h(_).blockw)},[e("img",{class:U([h(_).img,{"border-solid border-4 border-green-500":v==a.value}]),src:u.thumb},null,10,sl)],2)],8,ll))),128))]),e("div",nl,[e("button",{class:"movebutton",type:"button",onClick:o[1]||(o[1]=u=>s(a.value+1))},[(i(),d("svg",{width:h(_).btnw,height:h(_).btnh,fill:"currentColor",class:"bi bi-chevron-compact-right",viewBox:"0 0 16 16"},il,8,al))])])],512)),[[N,t.init!=null&&t.init.length>0]])}},cl={class:"py-2 overflow-scroll"},dl=["onClick"],ul={__name:"TSStyle",props:["name","init","def"],emits:["tsComCallback"],setup(k,{emit:C}){const t=k,g=C;return(a,m)=>(i(),d("div",cl,[(i(!0),d(O,null,L(t.init,(_,s)=>(i(),d("button",{onClick:n=>g("tsComCallback",t.name,_),key:s,class:"btn btn-sm bg-slate-100 m-1"},b(_),9,dl))),128))]))}},ml=["name","checked","onInput"],pl={class:"label-text"},de={__name:"TSRadio",props:["init","def","name"],emits:["tsComCallback"],setup(k,{emit:C}){const t=k,g=C;function a(m){try{g("tsComCallback",t.name,m.val)}catch(_){console.log(_)}}return(m,_)=>(i(!0),d(O,null,L(t.init,(s,n)=>(i(),d("label",{key:n,class:"label cursor-pointer gap-1"},[e("input",{type:"radio",name:t.name,class:"radio checked:bg-red-500",checked:t.def==s.val,onInput:o=>a(s)},null,40,ml),e("span",pl,b(s.label),1)]))),128))}},_l={},gl={id:"snackbar"};function hl(k,C){return i(),d("div",gl,"Some text some message..")}const vl=ze(_l,[["render",hl]]),fl={class:"mt-4"},bl={class:"flex-row sm:w-11/12 lg:w-3/4 mx-auto text-center grid justify-items-center"},xl={key:0,class:"grid justify-items-center"},wl=["onClick","disabled"],kl={__name:"OperationButton",props:["init","name","img"],emits:["btnAction"],setup(k,{emit:C}){const t=k,g=C,a=f({act:null,img:null});function m(_){a.value={act:_.act,img:t.img},g("btnAction",a.value)}return(_,s)=>(i(),d("div",fl,[e("div",bl,[t.init!=null?(i(),d("div",xl,[(i(!0),d(O,null,L(t.init,(n,o)=>(i(),d("div",{key:"IB_"+o,class:"items-center inline-flex"},[(i(!0),d(O,null,L(n,(u,v)=>(i(),d("button",{onClick:T=>m(u),key:"BS_"+v,type:"button",class:U([a.value.act==u.act?"bg-red-800 hover:bg-red-900":"bg-gray-600 hover:bg-gray-900","text-white bg-gray-800 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-full text-sm px-5 py-2.5 mr-2 mb-2"]),disabled:a.value.act==u.act},b(u.label),11,wl))),128))]))),128))])):E("",!0)])]))}},yl=[],Cl=[{label:"Normal",val:"normal"},{label:"Cute",val:"cute"},{label:"Expressive",val:"expressive"},{label:"Original",val:"original"},{label:"Scenic",val:"scenic"}],$l=[{label:"None",val:null},{label:"2X",val:"2x"},{label:"4X",val:"4x"}],A=K({styleList:yl,nijiStyle:Cl,upscaleOptions:$l,mjConfig:{denoising_strength:{min:"0",max:"2",def:"1",label:c("Denoising strength"),val:1,step:"1"},seed:{min:"0",max:"4294967295",def:"0",label:c("Seed"),val:"0"},cfg_scale:{min:"0",max:"1000",def:"100",label:c("Scale"),val:"100"},steps:{min:"0.25",max:"1",def:"1",label:c("Steps"),val:"1",step:"0.25"},niji:[{key:"normal",val:c("Normal")},{key:"cute",val:c("Cute")},{key:"expressive",val:c("Expressive")},{key:"original",val:c("Original")},{key:"scenic",val:c("Scenic")}],weird:{min:"0",max:"3000",def:"100",label:c("Weird"),val:"0"},sampler:{label:c("Sampler"),val:""},cref:{min:"0",max:"100",def:"0",label:c("cref"),val:"0"},sref:{min:"0",max:"10",def:"0",label:c("sref"),val:"0"}},sdConfig:{denoising_strength:{min:"0",max:"1",def:"0.4",label:c("Denoising strength"),val:"0.4",step:"0.01"},seed:{min:"-1",max:"4294967295",def:"0",label:c("Seed"),val:"0"},cfg_scale:{min:"0",max:"30",def:"7",label:c("Scale"),val:"7"},steps:{min:"0",max:"50",def:"30",label:c("Steps"),val:"30",step:"1"},weird:{min:"0",max:"0",def:"0",label:c("Weird"),val:"0"},sampler:{label:c("Sampler"),val:"K_DPM_2"}}}),jl={class:"px-5 bg-white grid grid-cols-2"},Sl={id:"left",class:"basis-1/2"},Tl={class:"flex gap-2"},Il={class:"flex items-center justify-between gap-2"},zl={class:"w-16 whitespace-nowrap text-xs text-right"},Bl={class:"badge badge-neutral badge-sm w-10"},Nl={class:"flex justify-between p-2"},Ml={key:1},Ol={class:"flex items-center gap-2"},Ll={key:0,class:"flex items-center gap-2 w-60"},Pl={class:"text-sm whitespace-nowrap"},Al={class:"flex"},El={class:"flex gap-x-4 py-4"},Vl={class:"flex justify-between items-center"},Jl={class:"relative inline-flex items-center cursor-pointer p-2"},Rl={class:"text-sm font-medium text-gray-900 dark:text-gray-300 mr-1"},Hl={class:"flex justify-start py-4 gap-x-4 items-center"},Dl={class:"w-40 h-40"},Fl={class:"flex flex-col"},Gl={key:0},Ul={class:"flex justify-center p-10"},Wl={id:"right",class:"basis-1/2 bg-gray-100 rounded-xl mx-4"},ql={class:""},Kl={class:"w-full h-screen"},Ql={class:"relative text-sm font-medium cursor-pointer"},Xl={class:"relative"},Yl={class:"flex justify-center pt-52"},Zl={class:"absolute grid z-20 rounded text-accent-content place-content-center"},es={key:0,class:"grid justify-center justify-items-center rounded-xl bg-neutral w-52 py-8 bg-opacity-80"},ts=e("span",{class:"loading loading-dots loading-lg text-error"},null,-1),ls={class:"text-white flex"},ss={class:"w-16 text-center"},ns={class:"absolute inset-0"},as={key:0,class:""},os={class:"flex justify-center"},is=["src"],rs={class:"flex justify-center py-5"},cs=e("progress",{class:"progress w-56"},null,-1),ds=[cs],us={key:1,class:"text-2xl text-stone-700 text-center z-30 pt-20"},ms={id:"my_modal_3",class:"modal bg-gray-900 bg-opacity-80"},ps={class:"h-max w-max"},_s=e("form",{method:"dialog"},[e("button",{class:"btn btn-circle btn-ghost absolute right-2 top-2 text-2xl bg-gray-500 hover:bg-white"},"✕")],-1),gs={class:"flex items-center gap-x-9 w-screen justify-evenly"},hs={class:"text-white pb-20"},vs=e("svg",{class:"w-6 h-6",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M15.75 19.5L8.25 12l7.5-7.5"})],-1),fs=[vs],bs=["src"],xs={class:"text-white pb-20"},ws=e("svg",{class:"w-6 h-6",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M8.25 4.5l7.5 7.5-7.5 7.5"})],-1),ks=[ws],Ns={__name:"ImageIndex",props:["sdratio","token","omSamplers","lcSamplers","sdSamplers","engines"],setup(k){window.Pusher=Be;const C=ue(),t=k,g=f(!1),a=f(!1),m=f(!1),_=f(),s=f(),n=f(!1),o=f(null),u=f([]),v=f({buttonGroups:[],id:null}),T=f(1),R=f("Unknown"),D=f(""),P=f([]),x=f(0),S=f(null);var j=[],B=3e3,W=1,Y="";let Z=localStorage.getItem("aibox_params"),M={};Z!=null?(M=JSON.parse(Z),M.niji!=null&&(a.value=!0),M.model_hash_id=null,M.lora_hash_id=null,M.image_num=M.image_num??1,M.lora_scale=M.lora_scale??.9):M={engine:"lc",model_hash_id:null,lora_hash_id:null,model_name:null,lora_name:null,prompt:null,prompt_en:null,negative_prompt:null,negative_prompt_en:null,asp_id:0,niji:null,steps:null,cfg_scale:null,seed:0,face_enhance:!1,upscale:null,width:null,height:null,weird:null,denoising_strength:null,cref:null,sref:null,sampler_name:null,isRandom:!0,image_num:1,lora_scale:.9};const l=K(M);ke(()=>{te(T.value),P.value=ee(l.engine),l.sampler_name=P.value.length>0?P.value[0].val:null,window.Echo=new Ne({broadcaster:"reverb",key:"4vfwamrpeeeex75poslv",wsHost:C.props.reverbHost,wsPort:"8080",wssPort:"8080",forceTLS:!1,enabledTransports:["ws","wss"]})});function ee(r){switch(r){case"lc":case"atz":return t.lcSamplers;case"sd":return t.sdSamplers;default:return[]}}async function te(r){if(j.includes(r)){console.log(r,"requested this page");return}j.push(r);let p=await X("get","/api/images",{page:r});if(p!=null&&p.code==1){let y=p.data;y!==null&&y.forEach(F=>{u.value.push(F)}),y.length>0&&(T.value=r)}}function w(r,p){switch(r){case"isAdvance":g.value=p;break;case"isNiji":a.value=p;break;case"engine":le(p.engine);break;case"style":_e(p);break;case"init_img_path":o.value=p;break;case"work_image":pe(p);break;case"model_name":l.model_hash_id=p.hash??null,A.styleList=JSON.parse(p.tags??"[]"),l.model_name=p.sd_name,["sdxl","presdxl"].includes(p.base_model)?S.value="sdxl":S.value="1.5";break;case"lora_name":l.lora_hash_id=p.hash??null,l.lora_name=p.sd_name??null;break;case"sampler_name":l[r]=p.val;break;case"asp_id":l.asp_id=p;let y=_.value[p];l.width=parseInt(y.width*W),l.height=parseInt(y.height*W);break;case"prompt":l[r]=p,l.prompt_en=null;break;default:l[r]=p}}function pe(r){if(v.value=r,v.value.index>=u.value.length-3&&te(T.value+1),r.task!=null){let p=r.task;Object.entries(l).forEach(function([y,F]){y in p&&(l[y]=p[y])})}}function _e(r){let p=r;l.prompt.indexOf(p)!==-1||(l.prompt=l.prompt+","+p)}function le(r){switch(l.engine=r,l.model_name=null,l.lora_name=null,l.model_hash_id=null,l.lora_hash_id=null,P.value=ee(l.engine),l.sampler_name=P.value.length>0?P.value[0].val:null,r){case"mj":m.value=!0,_.value=t.sdratio,s.value=A.mjConfig,l.image_num=1;break;case"sd":l.image_num=1;case"atz":case"lc":m.value=!1,_.value=t.sdratio,s.value=A.sdConfig;break;default:l.image_num=1,m.value=!1,_.value=t.sdratio,s.value=A.sdConfig}l.cfg_scale=s.value.cfg_scale.def,l.steps=s.value.steps.def,l.seed=s.value.seed.def,l.weird=s.value.weird.def,l.denoising_strength=s.value.denoising_strength.def}le(l.engine),w("asp_id",l.asp_id??0);async function ge(){if(window.Echo.leave("private-channel_task."+Y),x.value=0,n.value)return;let r="MK",p=je(r,l);if(p!==!0){G(p,"error");return}localStorage.setItem("aibox_params",JSON.stringify(l));let y=Se(l);y.append("act",r),y.append("job_type","MK");var F={};y.forEach((be,xe)=>F[xe]=be),console.log(JSON.stringify(F)),o.value!=null&&y.append("init_img_path",o.value),n.value=!0;let fe=t.token,I=await X("post","/api/task",y,{"Content-Type":"multipart/form-data",Authorization:fe});I!=null&&I.code==1?(B=parseInt(I.execTime)*100,Q(),Y=I.data.task_id,l.prompt_en=I.data.prompt_en,l.negative_prompt_en=I.data.negative_prompt_en,ve(I.data.task_id),x.value=I.data.image_num,I.data!=null&&I.data.init_img!=null&&I.data.init_img!=""&&(o.value=I.data.init_img)):(n.value=!1,G(I.msg??c("Failed to submit AI generation task"),"error"))}function Q(r){B<=-6e3?(n.value==!0&&(n.value=!1,G(c("Over time"),"info")),R.value="Unknown"):(B--,R.value=B/100+"秒",setTimeout(function(){Q()},10))}async function he(r){if(console.log(r),n.value==!0){console.log("Task is running");return}n.value=!0,r.act=="EDIT"?ye.get(route("tools.cropper",{url:r.img.show_url})):(B=1500,Q(),x.value=1,Te(r.act,{init_img_path:r.img.show_url,id:r.img.id,engine:l.engine},se))}function se(r){console.log("ws call back",r.message_type,x.value),r.message_type=="standing"?(u.value.unshift(r),v.value=r,B=3e3,x.value=x.value-1,x.value==0&&(n.value=!1)):r.message_type=="ephemeral"?(n.value=!0,B=parseInt(r.execTime??"1")*100):r.message_type=="failed"&&(n.value=!1,B=3e3,G(r.msg??c("Failed to submit AI generation task"),"error"))}const ve=r=>{console.log("listen: channel_task =",r),window.Echo.private("channel_task."+r).listen("TaskMessage",async p=>{se(p.message)})};return(r,p)=>(i(),H(Ce,{title:"AI images"},{header:ae(()=>[$(Ie,{init:t.engines,def:l.engine,name:"engine",onTsComCallback:w},null,8,["init","def"])]),default:ae(()=>[$(vl),e("div",jl,[e("div",Sl,[e("div",Tl,[["lc","atz"].includes(l.engine)?(i(),H(re,{key:0,name:"model_name",label:h(c)("model"),engine:l.engine,init:"checkpoint",onTsComCallback:w,def:l.model_name},null,8,["label","engine","def"])):E("",!0),e("div",null,[["lc","atz","sd"].includes(l.engine)?(i(),H(re,{key:0,name:"lora_name",label:h(c)("lora"),engine:l.engine,init:"lora",onTsComCallback:w,base:S.value,def:l.lora_name},null,8,["label","engine","base","def"])):E("",!0),z(e("div",Il,[z(e("input",{type:"range",min:"0",max:"1",step:"0.1","onUpdate:modelValue":p[0]||(p[0]=y=>l.lora_scale=y),class:"range range-xs range-success"},null,512),[[oe,l.lora_scale]]),e("label",zl,b(h(c)("scale"))+":",1),e("div",Bl,b(l.lora_scale),1)],512),[[N,l.lora_hash_id!=null&&["lc","atz"].includes(l.engine)]])])]),e("div",null,[$(ce,{name:"prompt",label:h(c)("prompt"),onTsComCallback:w,def:l.prompt},null,8,["label","def"]),h(A).styleList.length>0?(i(),H(ul,{key:0,name:"style",init:h(A).styleList,onTsComCallback:w},null,8,["init"])):E("",!0)]),$(Vt,{name:"asp_id",label:"Aspect",onTsComCallback:w,init:_.value,def:l.asp_id},null,8,["init","def"]),e("div",Nl,[m.value?(i(),H(q,{key:0,name:"isNiji",label:"Niji?",onTsComCallback:w,def:a.value},null,8,["def"])):(i(),d("div",Ml)),e("div",Ol,[["lc","atz"].includes(l.engine)?(i(),d("div",Ll,[e("div",Pl,b(h(c)("image num"))+":"+b(l.image_num),1),z(e("input",{type:"range",min:"1",max:"20",value:"1","onUpdate:modelValue":p[1]||(p[1]=y=>l.image_num=y),class:"range range-success range-sm"},null,512),[[oe,l.image_num]])])):E("",!0),$(q,{name:"isRandom",label:h(c)("random seed"),onTsComCallback:w,def:l.isRandom},null,8,["label","def"]),$(q,{name:"isAdvance",label:h(c)("advance"),onTsComCallback:w,def:g.value},null,8,["label","def"])])]),z(e("div",Al,[$(de,{init:h(A).nijiStyle,def:l.niji,name:"niji",onTsComCallback:w},null,8,["init","def"])],512),[[N,a.value&&m.value]]),z(e("div",{class:"divider"},b(h(c)("advance")),513),[[N,g.value]]),z(e("div",null,[$(ce,{name:"negative_prompt",label:h(c)("neg prompt"),onTsComCallback:w,def:l.negative_prompt},null,8,["label","def"]),e("div",El,[e("div",null,[$(V,{name:"steps",label:h(c)("steps"),onTsComCallback:w,min:s.value.steps.min.toString,max:s.value.steps.max,def:l.steps,step:s.value.steps.step},null,8,["label","min","max","def","step"])]),e("div",null,[$(V,{name:"cfg_scale",label:h(c)("scale"),onTsComCallback:w,min:s.value.cfg_scale.min,max:s.value.cfg_scale.max,def:l.cfg_scale},null,8,["label","min","max","def"])]),e("div",null,[z($(V,{name:"seed",label:h(c)("seed"),onTsComCallback:w,min:s.value.seed.min,max:s.value.seed.max,def:l.seed},null,8,["label","min","max","def"]),[[N,!l.isRandom]])]),e("div",null,[z($(V,{name:"weird",label:"Weird",onTsComCallback:w,min:s.value.weird.min,max:s.value.weird.max,def:l.weird},null,8,["min","max","def"]),[[N,l.engine=="mj"]])])]),z(e("div",Vl,[$(q,{name:"face_enhance",label:h(c)("Face enhance"),onTsComCallback:w,def:l.face_enhance},null,8,["label","def"]),e("label",Jl,[e("span",Rl,b(h(c)("upscale")),1),$(de,{init:h(A).upscaleOptions,def:l.upscale,name:"upscale",onTsComCallback:w},null,8,["init","def"])])],512),[[N,["lc","atz"].includes(l.engine)]]),l.engine!="mj"?(i(),H(We,{key:0,name:"sampler_name",label:h(c)("sampler"),init:P.value,onTsComCallback:w,def:l.sampler_name},null,8,["label","init","def"])):E("",!0),e("div",Hl,[e("div",Dl,[$(Kt,{name:"init_img_path",desc:h(c)("upload a picture"),def:o.value,onTsComCallback:w},null,8,["desc","def"])]),z(e("div",Fl,[$(V,{name:"denoising_strength",label:"Denoising",onTsComCallback:w,min:s.value.denoising_strength.min,max:s.value.denoising_strength.max,def:l.denoising_strength,step:s.value.denoising_strength.step},null,8,["min","max","def","step"]),l.engine=="mj"&&s.value.cref!=null?(i(),d("div",Gl,[$(V,{name:"cref",label:h(c)("cref"),onTsComCallback:w,min:s.value.cref.min,max:s.value.cref.max,def:l.cref,step:s.value.cref.step},null,8,["label","min","max","def","step"]),$(V,{name:"sref",label:h(c)("sref"),onTsComCallback:w,min:s.value.sref.min,max:s.value.sref.max,def:l.sref,step:s.value.sref.step},null,8,["label","min","max","def","step"])])):E("",!0)],512),[[N,o.value!=null]])])],512),[[N,g.value]]),e("div",Ul,[e("button",{onClick:ge,type:"button",class:"text-white bg-gradient-to-r from-green-500 via-green-600 to-green-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 shadow-lg shadow-green-500/50 font-medium rounded-full w-32 text-sm px-5 py-2.5 text-center me-2 mb-2"},b(r.$t("Generate")),1)])]),e("div",Wl,[e("div",ql,[$(rl,{name:"work_image",init:u.value,def:0,tabstyle:"small",onTsComCallback:w,moveto:D.value},null,8,["init","moveto"])]),e("div",Kl,[e("div",Ql,[e("div",Xl,[e("div",Yl,[e("div",Zl,[n.value?(i(),d("div",es,[ts,e("label",ls,[me(" No."+b(x.value)+" "+b(h(c)("estimated"))+":",1),e("div",ss,b(R.value),1)])])):E("",!0)])]),e("div",ns,[v.value.show_url!=null?(i(),d("div",as,[e("div",os,[e("img",{onclick:"my_modal_3.showModal()",src:v.value.show_url},null,8,is)]),z(e("div",rs,ds,512),[[N,n.value]]),$(kl,{init:v.value.buttonGroups,name:"show_image",img:v.value,onBtnAction:he},null,8,["init","img"])])):(i(),d("div",us,b(h(c)("image preview area")),1))])])])])]),e("dialog",ms,[e("div",ps,[_s,e("div",gs,[e("div",hs,[e("button",{onClick:p[2]||(p[2]=y=>D.value="pre_"+v.value.index),class:"btn btn-circle btn-ghost right-2 top-2 text-2xl bg-gray-500 hover:bg-white bg-opacity-55 hover:text-black"},fs)]),e("img",{class:"h-screen",src:v.value.show_url},null,8,bs),e("div",xs,[e("button",{onClick:p[3]||(p[3]=y=>D.value="next_"+v.value.index),class:"btn btn-circle btn-ghost right-2 top-2 text-2xl bg-gray-500 hover:bg-white bg-opacity-55 hover:text-black"},ks)])])])])])]),_:1}))}};export{Ns as default};