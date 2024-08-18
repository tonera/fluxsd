import{o as r,d as p,a as e,p as S,r as h,B as Q,C as X,q as n,c as Y,w as ee,b as M,k as te,s as se,t as d,u as a,g as C,n as b,F as I}from"./app-BTzmeDWY.js";import{_ as oe}from"./AppLayout-CQqgKRis.js";import{_ as le,a as ne}from"./TSFloatMask-Cbln4xKj.js";import{a as O,s as x}from"./network-ChbNVeWA.js";import"./TSState-B7Socvnz.js";import"./_plugin-vue_export-helper-DlAUqK2U.js";import"./TopSnackbar-Czlq5nYM.js";const ae={class:"bg-slate-500 relative rounded-xl p-2"},de={class:"flex justify-center items-center gap-2 relative"},ie=["for"],re=["src"],ce={__name:"TSScreenImage",props:["init","modalId"],setup(f){const l=f;return(m,v)=>(r(),p("div",ae,[e("div",de,[e("label",{for:l.modalId,class:"btn btn-sm btn-circle btn-ghost absolute right-2 top-2 bg-red-600 text-white"},"✕",8,ie),e("img",{src:l.init,class:"object-cover h-screen rounded-xl"},null,8,re)])]))}};function B(f){let l=document.getElementById(f);l!=null&&(l.checked=!0)}const ue={class:"max-w-7xl mx-auto sm:px-6 lg:px-8 py-2 bg-slate-50"},pe={class:"py-1 flex justify-between h-14"},_e={class:"flex items-center"},me={class:"input input-bordered flex items-center gap-2 input-sm"},ge=e("svg",{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 16 16",fill:"currentColor",class:"w-4 h-4 opacity-70"},[e("path",{"fill-rule":"evenodd",d:"M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z","clip-rule":"evenodd"})],-1),he={class:"form-control"},be={class:"label cursor-pointer gap-2 px-2"},fe={class:"label-text"},ve={class:"dropdown dropdown-end"},xe={tabindex:"0",role:"button",class:"btn m-1 btn-sm"},we={tabindex:"0",class:"dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box"},ye=["onClick"],ke={class:"dropdown dropdown-end"},Se={tabindex:"0",role:"button",class:"btn m-1 btn-sm"},Ce={tabindex:"0",class:"dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-48"},Ie=["onClick"],$e={class:"dropdown dropdown-end"},Me={tabindex:"0",role:"button",class:"btn m-1 btn-sm"},Oe={tabindex:"0",class:"dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-48"},Be={class:"grid grid-cols-6 gap-2"},Te=e("input",{type:"checkbox",id:"show_model_images",class:"modal-toggle"},null,-1),Ee={class:"modal",role:"dialog"},je={class:"modal-box w-11/12 max-w-5xl grid grid-cols-7 gap-1"},Le=e("label",{for:"show_model_images",class:"btn btn-sm btn-circle btn-ghost absolute right-0 top-2"},"✕",-1),Ne=["onClick","src"],ze=e("input",{type:"checkbox",id:"show_full_image",class:"modal-toggle"},null,-1),De={class:"modal",role:"dialog"},Je=e("input",{type:"checkbox",id:"confirm_delete_model",class:"modal-toggle"},null,-1),Ae={class:"modal",role:"dialog"},Pe={class:"modal-box w-11/12 max-w-xl"},qe=e("label",{for:"confirm_delete_model",class:"btn btn-sm btn-circle btn-ghost absolute right-0 top-2"},"✕",-1),Fe={class:"text-lg font-bold"},He={class:"py-4"},Ve={class:"modal-action flex gap-2"},Re={for:"confirm_delete_model",class:"btn btn-neutral"},Ye={__name:"Index",props:{apihost:String,modelIds:Array,failedIds:Array,dlPercent:Object},setup(f){const l=S({engine:"atz",page:1});var m=null;const v=h({meta:{last_page:1,current_page:1},data:[]}),w=f,g=S({});Object.entries(w.dlPercent).forEach(([t,s])=>{g[t]=s});const T=h(null),$=h(null),E=h([]),j=h(null);var L=[];const c=S([]);var N=[];const u=S([]);var z=h(null);Q(()=>{_(1),J()}),X(()=>{D()});async function _(t){l.page=t,c.length>0&&(l.options=JSON.stringify(c)),u.length>0&&(l.types=JSON.stringify(u));let s=await O("get","/api/models",l);if(s==null||s.code!=1){x(s.msg??n("Loading data error"),"error");return}L=s.base_models,N=s.types,v.value=s}function P(t){t.target.checked?(l.engine="lc",u.splice(0,u.length),u.push("checkpoint","lora"),c.splice(0,c.length),c.push("1.5","pre1.5","sdxl","presdxl")):l.engine="atz",_(l.page)}function q(t){E.value=t.images,B("show_model_images")}function y(t){T.value=t,$.value=$.value=="desc"?"asc":"desc",l.sort=t,l.orderby=$.value,_(l.page)}function F(t){j.value=t.thumb,B("show_full_image")}function H(t){l.keyword=t.target.value,_(l.page)}function k(t){return T.value==t?"font-extrabold text-green-500":""}function V(t){return c.indexOf(t)!==-1?"font-extrabold text-green-500":""}function R(t){return u.indexOf(t)!==-1?"font-extrabold text-green-500":""}function U(t){let s=c.indexOf(t);s!==-1?c.splice(s,1):c.push(t),_(l.page)}function Z(t){let s=u.indexOf(t);s!==-1?u.splice(s,1):u.push(t),_(l.page)}async function K(t,s){if(console.log(t,s),t=="crop"){if(w.modelIds.includes(s.hash))return;let o=await O("post","/api/models/download",s);o==null||o.code!=1?x(o.msg??n("Server error"),"error"):(D(),g[s.hash]=0,J(),x(n("It has been into the download queue. Please wait patiently"),"success"))}else t=="delete"&&(z.value=s,B("confirm_delete_model"))}async function W(){let t=await O("delete","/api/models/"+z.value.id);t==null||t.code!=1?x(t?t.msg:n("Delete the model failed"),"error"):(x(n("Successfully deleted"),"success"),_(l.page))}function G(t,s){return w.modelIds.includes(t)?n("Downloaded"):w.failedIds.includes(t)?n("Retry"):s!=null?s+"%":n("download")}const D=()=>{m!=null&&m.close()},J=()=>{let t=Object.keys(g);m=new EventSource("/api/download/progress?ids="+JSON.stringify(t)),m.addEventListener("open",s=>{}),m.addEventListener("message",s=>{let o=JSON.parse(s.data);for(var i in o)o.hasOwnProperty(i)&&(g[i]=o[i])})};return(t,s)=>(r(),Y(oe,{title:"Model market"},{default:ee(()=>[e("div",ue,[e("div",pe,[M(le,{onHandleJump:_,total:v.value.meta.last_page,cpage:v.value.meta.current_page},null,8,["total","cpage"]),e("div",_e,[e("label",me,[te(e("input",{type:"text",class:"grow border-none input-xs",placeholder:"Search",onChange:H,"onUpdate:modelValue":s[0]||(s[0]=o=>l.keyword=o)},null,544),[[se,l.keyword]]),ge]),e("div",he,[e("label",be,[e("span",fe,d(a(n)("Local Models")),1),e("input",{onClick:P,type:"checkbox",class:"checkbox checkbox-sm"})])]),e("div",null,[e("div",ve,[e("div",xe,d(a(n)("category")),1),e("ul",we,[(r(!0),p(I,null,C(a(N),(o,i)=>(r(),p("li",{key:"li_tp_"+i,class:b(R(o))},[e("a",{onClick:A=>Z(o)},d(o),9,ye)],2))),128))])])]),e("div",null,[e("div",ke,[e("div",Se,d(a(n)("filter")),1),e("ul",Ce,[(r(!0),p(I,null,C(a(L),(o,i)=>(r(),p("li",{key:"li_bm_"+i,class:b(V(o))},[e("a",{onClick:A=>U(o)},d(o),9,Ie)],2))),128))])])]),e("div",null,[e("div",$e,[e("div",Me,d(a(n)("sort")),1),e("ul",Oe,[e("li",null,[e("a",{onClick:s[1]||(s[1]=o=>y("type")),class:b(k("type"))},d(a(n)("category")),3)]),e("li",null,[e("a",{onClick:s[2]||(s[2]=o=>y("rating")),class:b(k("rating"))},d(a(n)("rate")),3)]),e("li",null,[e("a",{onClick:s[3]||(s[3]=o=>y("is_active")),class:b(k("is_active"))},d(a(n)("active")),3)]),e("li",null,[e("a",{onClick:s[4]||(s[4]=o=>y("base_model")),class:b(k("base_model"))},d(a(n)("base model")),3)])])])])])]),e("div",Be,[(r(!0),p(I,null,C(v.value.data,(o,i)=>(r(),p("div",{key:i},[M(ne,{init:o,mode:"big",onHandleSelectedImage:q,notice:"Type:"+o.type+"<br/>Name:"+o.name+"<br/>Base model:"+o.base_model,export:o.rating,rbg:o.credit,delete:a(n)("delete"),crop:G(o.hash,g[o.hash]),percent:g[o.hash],onTsComCallback:K},null,8,["init","notice","export","rbg","delete","crop","percent"])]))),128))])]),Te,e("div",Ee,[e("div",je,[Le,(r(!0),p(I,null,C(E.value,(o,i)=>(r(),p("div",{key:i},[e("img",{onClick:A=>F(o),src:o.thumb,class:"object-cover cursor-pointer w-full h-56"},null,8,Ne)]))),128))])]),ze,e("div",De,[M(ce,{init:j.value,"modal-id":"show_full_image",modalId:"show_full_image"},null,8,["init"])]),Je,e("div",Ae,[e("div",Pe,[qe,e("h3",Fe,d(a(n)("Are you sure to delete the model?")),1),e("p",He,d(a(n)("This model will be deleted!")),1),e("div",Ve,[e("label",Re,d(a(n)("cancel")),1),e("label",{for:"confirm_delete_model",class:"btn btn-error",onClick:W},d(a(n)("OK")),1)])])])]),_:1}))}};export{Ye as default};