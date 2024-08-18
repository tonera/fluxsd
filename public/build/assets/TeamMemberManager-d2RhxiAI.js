import{Q as H,T as k,r as x,o as n,d as l,b as a,w as t,e as u,f as r,a as o,u as s,F as w,g as C,n as v,t as _,O as J}from"./app-BTzmeDWY.js";import{_ as K}from"./ActionMessage-O8p70sjX.js";import{_ as A}from"./Modal-DB-uUBDG.js";import{_ as B}from"./ConfirmationModal-BhEkqK7q.js";import{_ as L}from"./DangerButton-Dl9mQx5n.js";import{_ as W}from"./DialogModal-hB0joC3P.js";import{_ as X}from"./FormSection-DtQoaaxe.js";import{_ as P}from"./InputError-j37yojwf.js";import{_ as z}from"./InputLabel-BfE7QRw5.js";import{_ as F}from"./PrimaryButton-JN35i_AC.js";import{_ as $}from"./SecondaryButton-CrEiUNYG.js";import{S}from"./SectionBorder-Cn-8ZzeT.js";import{_ as Y}from"./TextInput-KbFB4Nan.js";import"./SectionTitle-r51r2slp.js";import"./_plugin-vue_export-helper-DlAUqK2U.js";const Z={key:0},ee=o("div",{class:"col-span-6"},[o("div",{class:"max-w-xl text-sm text-gray-600"}," Please provide the email address of the person you would like to add to this team. ")],-1),te={class:"col-span-6 sm:col-span-4"},se={key:0,class:"col-span-6 lg:col-span-4"},oe={class:"relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer"},ae=["onClick"],ne={class:"flex items-center"},le={key:0,class:"ms-2 h-5 w-5 text-green-400",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor"},re=o("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"},null,-1),ie=[re],me={class:"mt-2 text-xs text-gray-600 text-start"},ce={key:1},de={class:"space-y-6"},ue={class:"text-gray-600"},ve={class:"flex items-center"},fe=["onClick"],be={key:2},_e={class:"space-y-6"},ye={class:"flex items-center"},ge=["src","alt"],pe={class:"ms-4"},he={class:"flex items-center"},ke=["onClick"],xe={key:1,class:"ms-2 text-sm text-gray-400"},we=["onClick"],Ce={key:0},Te={class:"relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer"},Re=["onClick"],Me={class:"flex items-center"},$e={key:0,class:"ms-2 h-5 w-5 text-green-400",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor"},Se=o("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"},null,-1),je=[Se],Ae={class:"mt-2 text-xs text-gray-600"},He={__name:"TeamMemberManager",props:{team:Object,availableRoles:Array,userPermissions:Object},setup(i){const g=i,V=H(),c=k({email:"",role:null}),f=k({role:null}),T=k({}),R=k({}),p=x(!1),M=x(null),h=x(!1),y=x(null),O=()=>{c.post(route("team-members.store",g.team),{errorBag:"addTeamMember",preserveScroll:!0,onSuccess:()=>c.reset()})},N=d=>{J.delete(route("team-invitations.destroy",d),{preserveScroll:!0})},E=d=>{M.value=d,f.role=d.membership.role,p.value=!0},I=()=>{f.put(route("team-members.update",[g.team,M.value]),{preserveScroll:!0,onSuccess:()=>p.value=!1})},U=()=>{h.value=!0},D=()=>{T.delete(route("team-members.destroy",[g.team,V.props.auth.user]))},Q=d=>{y.value=d},q=()=>{R.delete(route("team-members.destroy",[g.team,y.value]),{errorBag:"removeTeamMember",preserveScroll:!0,preserveState:!0,onSuccess:()=>y.value=null})},j=d=>g.availableRoles.find(m=>m.key===d).name;return(d,m)=>(n(),l("div",null,[i.userPermissions.canAddTeamMembers?(n(),l("div",Z,[a(S),a(X,{onSubmitted:O},{title:t(()=>[r(" Add Team Member ")]),description:t(()=>[r(" Add a new team member to your team, allowing them to collaborate with you. ")]),form:t(()=>[ee,o("div",te,[a(z,{for:"email",value:"Email"}),a(Y,{id:"email",modelValue:s(c).email,"onUpdate:modelValue":m[0]||(m[0]=e=>s(c).email=e),type:"email",class:"mt-1 block w-full"},null,8,["modelValue"]),a(P,{message:s(c).errors.email,class:"mt-2"},null,8,["message"])]),i.availableRoles.length>0?(n(),l("div",se,[a(z,{for:"roles",value:"Role"}),a(P,{message:s(c).errors.role,class:"mt-2"},null,8,["message"]),o("div",oe,[(n(!0),l(w,null,C(i.availableRoles,(e,b)=>(n(),l("button",{key:e.key,type:"button",class:v(["relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500",{"border-t border-gray-200 focus:border-none rounded-t-none":b>0,"rounded-b-none":b!=Object.keys(i.availableRoles).length-1}]),onClick:G=>s(c).role=e.key},[o("div",{class:v({"opacity-50":s(c).role&&s(c).role!=e.key})},[o("div",ne,[o("div",{class:v(["text-sm text-gray-600",{"font-semibold":s(c).role==e.key}])},_(e.name),3),s(c).role==e.key?(n(),l("svg",le,ie)):u("",!0)]),o("div",me,_(e.description),1)],2)],10,ae))),128))])])):u("",!0)]),actions:t(()=>[a(K,{on:s(c).recentlySuccessful,class:"me-3"},{default:t(()=>[r(" Added. ")]),_:1},8,["on"]),a(F,{class:v({"opacity-25":s(c).processing}),disabled:s(c).processing},{default:t(()=>[r(" Add ")]),_:1},8,["class","disabled"])]),_:1})])):u("",!0),i.team.team_invitations.length>0&&i.userPermissions.canAddTeamMembers?(n(),l("div",ce,[a(S),a(A,{class:"mt-10 sm:mt-0"},{title:t(()=>[r(" Pending Team Invitations ")]),description:t(()=>[r(" These people have been invited to your team and have been sent an invitation email. They may join the team by accepting the email invitation. ")]),content:t(()=>[o("div",de,[(n(!0),l(w,null,C(i.team.team_invitations,e=>(n(),l("div",{key:e.id,class:"flex items-center justify-between"},[o("div",ue,_(e.email),1),o("div",ve,[i.userPermissions.canRemoveTeamMembers?(n(),l("button",{key:0,class:"cursor-pointer ms-6 text-sm text-red-500 focus:outline-none",onClick:b=>N(e)}," Cancel ",8,fe)):u("",!0)])]))),128))])]),_:1})])):u("",!0),i.team.users.length>0?(n(),l("div",be,[a(S),a(A,{class:"mt-10 sm:mt-0"},{title:t(()=>[r(" Team Members ")]),description:t(()=>[r(" All of the people that are part of this team. ")]),content:t(()=>[o("div",_e,[(n(!0),l(w,null,C(i.team.users,e=>(n(),l("div",{key:e.id,class:"flex items-center justify-between"},[o("div",ye,[o("img",{class:"w-8 h-8 rounded-full object-cover",src:e.profile_photo_url,alt:e.name},null,8,ge),o("div",pe,_(e.name),1)]),o("div",he,[i.userPermissions.canUpdateTeamMembers&&i.availableRoles.length?(n(),l("button",{key:0,class:"ms-2 text-sm text-gray-400 underline",onClick:b=>E(e)},_(j(e.membership.role)),9,ke)):i.availableRoles.length?(n(),l("div",xe,_(j(e.membership.role)),1)):u("",!0),d.$page.props.auth.user.id===e.id?(n(),l("button",{key:2,class:"cursor-pointer ms-6 text-sm text-red-500",onClick:U}," Leave ")):i.userPermissions.canRemoveTeamMembers?(n(),l("button",{key:3,class:"cursor-pointer ms-6 text-sm text-red-500",onClick:b=>Q(e)}," Remove ",8,we)):u("",!0)])]))),128))])]),_:1})])):u("",!0),a(W,{show:p.value,onClose:m[2]||(m[2]=e=>p.value=!1)},{title:t(()=>[r(" Manage Role ")]),content:t(()=>[M.value?(n(),l("div",Ce,[o("div",Te,[(n(!0),l(w,null,C(i.availableRoles,(e,b)=>(n(),l("button",{key:e.key,type:"button",class:v(["relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500",{"border-t border-gray-200 focus:border-none rounded-t-none":b>0,"rounded-b-none":b!==Object.keys(i.availableRoles).length-1}]),onClick:G=>s(f).role=e.key},[o("div",{class:v({"opacity-50":s(f).role&&s(f).role!==e.key})},[o("div",Me,[o("div",{class:v(["text-sm text-gray-600",{"font-semibold":s(f).role===e.key}])},_(e.name),3),s(f).role==e.key?(n(),l("svg",$e,je)):u("",!0)]),o("div",Ae,_(e.description),1)],2)],10,Re))),128))])])):u("",!0)]),footer:t(()=>[a($,{onClick:m[1]||(m[1]=e=>p.value=!1)},{default:t(()=>[r(" Cancel ")]),_:1}),a(F,{class:v(["ms-3",{"opacity-25":s(f).processing}]),disabled:s(f).processing,onClick:I},{default:t(()=>[r(" Save ")]),_:1},8,["class","disabled"])]),_:1},8,["show"]),a(B,{show:h.value,onClose:m[4]||(m[4]=e=>h.value=!1)},{title:t(()=>[r(" Leave Team ")]),content:t(()=>[r(" Are you sure you would like to leave this team? ")]),footer:t(()=>[a($,{onClick:m[3]||(m[3]=e=>h.value=!1)},{default:t(()=>[r(" Cancel ")]),_:1}),a(L,{class:v(["ms-3",{"opacity-25":s(T).processing}]),disabled:s(T).processing,onClick:D},{default:t(()=>[r(" Leave ")]),_:1},8,["class","disabled"])]),_:1},8,["show"]),a(B,{show:y.value,onClose:m[6]||(m[6]=e=>y.value=null)},{title:t(()=>[r(" Remove Team Member ")]),content:t(()=>[r(" Are you sure you would like to remove this person from the team? ")]),footer:t(()=>[a($,{onClick:m[5]||(m[5]=e=>y.value=null)},{default:t(()=>[r(" Cancel ")]),_:1}),a(L,{class:v(["ms-3",{"opacity-25":s(R).processing}]),disabled:s(R).processing,onClick:q},{default:t(()=>[r(" Remove ")]),_:1},8,["class","disabled"])]),_:1},8,["show"])]))}};export{He as default};
