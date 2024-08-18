import{o,d as i,a as e,Q as R,r as C,L as J,n as k,e as v,t as u,h as E,B as V,C as K,j as P,m as x,M as A,u as h,k as F,z as O,b as f,w as d,E as Q,c as $,i as z,N as L,p as Z,q as b,F as S,g as N,Z as X,f as g,O as U}from"./app-BTzmeDWY.js";import{_ as Y}from"./_plugin-vue_export-helper-DlAUqK2U.js";import{T as ee}from"./TopSnackbar-Czlq5nYM.js";const te={},se={class:"bg-white"},oe=e("img",{class:"object-cover w-9 h-9 rounded-xl",src:"/images/logo.png"},null,-1),re=[oe];function ne(n,l){return o(),i("div",se,re)}const ae=Y(te,[["render",ne]]),le={class:"max-w-screen-xl mx-auto py-2 px-3 sm:px-6 lg:px-8"},ie={class:"flex items-center justify-between flex-wrap"},ue={class:"w-0 flex-1 flex items-center min-w-0"},de={key:0,class:"h-5 w-5 text-white",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor"},ce=e("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"},null,-1),he=[ce],pe={key:1,class:"h-5 w-5 text-white",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor"},fe=e("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"},null,-1),ge=[fe],me={class:"ms-3 font-medium text-sm text-white truncate"},_e={class:"shrink-0 sm:ms-3"},ve=e("svg",{class:"h-5 w-5 text-white",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M6 18L18 6M6 6l12 12"})],-1),be=[ve],we={__name:"Banner",setup(n){const l=R(),s=C(!0),r=C("success"),m=C("");return J(async()=>{var p,t;r.value=((p=l.props.jetstream.flash)==null?void 0:p.bannerStyle)||"success",m.value=((t=l.props.jetstream.flash)==null?void 0:t.banner)||"",s.value=!0}),(p,t)=>(o(),i("div",null,[s.value&&m.value?(o(),i("div",{key:0,class:k({"bg-indigo-500":r.value=="success","bg-red-700":r.value=="danger"})},[e("div",le,[e("div",ie,[e("div",ue,[e("span",{class:k(["flex p-2 rounded-lg",{"bg-indigo-600":r.value=="success","bg-red-600":r.value=="danger"}])},[r.value=="success"?(o(),i("svg",de,he)):v("",!0),r.value=="danger"?(o(),i("svg",pe,ge)):v("",!0)],2),e("p",me,u(m.value),1)]),e("div",_e,[e("button",{type:"button",class:k(["-me-1 flex p-2 rounded-md focus:outline-none sm:-me-2 transition",{"hover:bg-indigo-600 focus:bg-indigo-600":r.value=="success","hover:bg-red-600 focus:bg-red-600":r.value=="danger"}]),"aria-label":"Dismiss",onClick:t[0]||(t[0]=E(_=>s.value=!1,["prevent"]))},be,2)])])])],2)):v("",!0)]))}},ye={class:"relative"},G={__name:"Dropdown",props:{align:{type:String,default:"right"},width:{type:String,default:"48"},contentClasses:{type:Array,default:()=>["py-1","bg-white"]}},setup(n){const l=n;let s=C(!1);const r=t=>{s.value&&t.key==="Escape"&&(s.value=!1)};V(()=>document.addEventListener("keydown",r)),K(()=>document.removeEventListener("keydown",r));const m=P(()=>({48:"w-48"})[l.width.toString()]),p=P(()=>l.align==="left"?"ltr:origin-top-left rtl:origin-top-right start-0":l.align==="right"?"ltr:origin-top-right rtl:origin-top-left end-0":"origin-top");return(t,_)=>(o(),i("div",ye,[e("div",{onClick:_[0]||(_[0]=c=>A(s)?s.value=!h(s):s=!h(s))},[x(t.$slots,"trigger")]),F(e("div",{class:"fixed inset-0 z-40",onClick:_[1]||(_[1]=c=>A(s)?s.value=!1:s=!1)},null,512),[[O,h(s)]]),f(Q,{"enter-active-class":"transition ease-out duration-200","enter-from-class":"transform opacity-0 scale-95","enter-to-class":"transform opacity-100 scale-100","leave-active-class":"transition ease-in duration-75","leave-from-class":"transform opacity-100 scale-100","leave-to-class":"transform opacity-0 scale-95"},{default:d(()=>[F(e("div",{class:k(["absolute z-50 mt-2 rounded-md shadow-lg",[m.value,p.value]]),style:{display:"none"},onClick:_[2]||(_[2]=c=>A(s)?s.value=!1:s=!1)},[e("div",{class:k(["rounded-md ring-1 ring-black ring-opacity-5",n.contentClasses])},[x(t.$slots,"content")],2)],2),[[O,h(s)]])]),_:3})]))}},ke={key:0,type:"submit",class:"block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"},xe=["href"],M={__name:"DropdownLink",props:{href:String,as:String},setup(n){return(l,s)=>(o(),i("div",null,[n.as=="button"?(o(),i("button",ke,[x(l.$slots,"default")])):n.as=="a"?(o(),i("a",{key:1,href:n.href,class:"block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"},[x(l.$slots,"default")],8,xe)):(o(),$(h(z),{key:2,href:n.href,class:"block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"},{default:d(()=>[x(l.$slots,"default")]),_:3},8,["href"]))]))}},B={__name:"NavLink",props:{href:String,active:Boolean},setup(n){const l=n,s=P(()=>l.active?"inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out":"inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out");return(r,m)=>(o(),$(h(z),{href:n.href,class:k(s.value)},{default:d(()=>[x(r.$slots,"default")]),_:3},8,["href","class"]))}},j={__name:"ResponsiveNavLink",props:{active:Boolean,href:String,as:String},setup(n){const l=n,s=P(()=>l.active?"block w-full ps-3 pe-4 py-2 border-l-4 border-indigo-400 text-start text-base font-medium text-indigo-700 bg-indigo-50 focus:outline-none focus:text-indigo-800 focus:bg-indigo-100 focus:border-indigo-700 transition duration-150 ease-in-out":"block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out");return(r,m)=>(o(),i("div",null,[n.as=="button"?(o(),i("button",{key:0,class:k([s.value,"w-full text-start"])},[x(r.$slots,"default")],2)):(o(),$(h(z),{key:1,href:n.href,class:k(s.value)},{default:d(()=>[x(r.$slots,"default")]),_:3},8,["href","class"]))]))}},$e=R();function H(n,l="info"){var s=document.getElementById("snackbar");if(s==null||s==null){console.log("error:",n);return}s.innerHTML=n;var r="show";switch(l){case"error":r="snackerror";break}s.className=r,setTimeout(function(){s.className=s.className.replace(r,"")},3e3)}function Ce(n){return n+="",n=n.replace(/&/g,"%26"),n=n.replace(/\=/g,"%3D"),n}function je(n){const l=[];for(let s in n)l.push(`${s}=${Ce(n[s])}`);return l.join("&")}async function D(n,l,s=null,r=null){let m=s;if(n==="get"&&s!==null){let c=je(s);l+="?"+c,m=null}let p=L.post,_={"Content-Type":"application/x-www-form-urlencoded",Accept:"application/json",lang:$e.props.locale};switch(Object.assign(_,r),n){case"post":p=L.post;break;case"put":p=L.post,m._method="put";break;case"delete":p=L.delete;break;case"get":return p=L.get,await p(l,{headers:_}).then(c=>c.data).catch(c=>null)}return await p(l,m,{headers:_}).then(c=>c.data).catch(c=>null)}const Se={class:"flex items-center gap-1"},Te={class:"text-xs font-medium text-slate-500 dark:text-gray-300"},Me={class:"relative inline-flex items-center cursor-pointer"},Le=["checked"],Be=e("div",{class:"w-11 h-5 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[5px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"},null,-1),Ee={class:"stat place-items-center p-1"},Ne=e("div",{class:"stat-title"},"Credits",-1),Pe={class:"stat-desc text-secondary"},ze={id:"pop_deposits",class:"modal"},Ie={class:"modal-box"},Ae=e("form",{method:"dialog"},[e("button",{class:"btn btn-sm btn-circle btn-ghost absolute right-2 top-2"},"✕")],-1),De={class:"text-lg font-bold"},Fe={class:"overflow-x-auto"},Oe={class:"table"},Re=e("th",null,null,-1),Ue={class:"text-xs"},Ge={class:"text-xs"},He={class:"text-primary text-lg"},Ve={class:"text-xs"},qe={__name:"PartJob",props:["name","def","label"],emits:["tsComCallback"],setup(n,{emit:l}){const s=n,r=C(s.def??!1),m=C([]),p=Z({status:"Offline",credit:"Unknown",online_time:""}),t=async a=>{let w={switch:a.target.checked?"on":"off",hardware:_()},y=await D("post","/api/partjob/toggle",w);y!=null&&y.code==1?r.value=a.target.checked:(a.target.checked=!1,r.value=!1,H(y.msg??"Switch failed","error"))};function _(){var a=document.createElement("canvas"),w=a.getContext("webgl")||a.getContext("experimental-webgl"),y=w.getExtension("WEBGL_debug_renderer_info"),I=y?w.getParameter(y.UNMASKED_RENDERER_WEBGL):"Unavailable";return I}function c(a){return a==0||a==null?"":(a=parseInt(a),a<60?"("+a+"s)":a<3600?"("+parseInt(a/60)+"m)":"("+(a/3600).toFixed(1)+"h)")}function T(a){return a=="Unknown"||a==null?"N/A":(a=parseInt(a),a<1e3?a:a<1e6?parseInt(a/1e3)+"k":(a/1e6).toFixed(1)+"m")}function q(){setInterval(async()=>{let a=await D("get","/api/partjob/status");a!=null&&a.code==1&&(p.status=a.data.status,p.credit=a.data.credit,p.online_time=c(a.data.online_time),p.status=="Online"?r.value=!0:r.value=!1)},1e3*60)}async function W(a){let w=await D("get","/api/partjob/deposits");console.log(w),w!=null&&w.code==1?m.value=w.data:H(w.msg??"Get data error","error"),pop_deposits.showModal()}return V(()=>{q()}),(a,w)=>(o(),i("div",Se,[e("span",Te,u(s.label),1),e("label",Me,[e("input",{type:"checkbox",checked:r.value,onChange:t,class:"sr-only peer"},null,40,Le),Be]),e("span",{onClick:W,class:"text-xs font-medium text-slate-500 cursor-pointer"},[e("div",Ee,[Ne,e("div",Pe,"↗︎ "+u(T(p.credit))+" "+u(p.online_time),1)])]),e("dialog",ze,[e("div",Ie,[Ae,e("h3",De,u(h(b)("rewards"))+"!",1),e("div",Fe,[e("table",Oe,[e("thead",null,[e("tr",null,[Re,e("th",null,u(h(b)("name")),1),e("th",null,u(h(b)("credit")),1),e("th",null,u(h(b)("time")),1)])]),e("tbody",null,[(o(!0),i(S,null,N(m.value,(y,I)=>(o(),i("tr",{key:"r_deposit_"+I},[e("th",Ue,u(y.id),1),e("td",Ge,u(y.pay_mode_name),1),e("td",He,u(y.credit),1),e("td",Ve,u(y.created_at),1)]))),128))])])])])])]))}},We={class:"min-h-screen"},Je={class:"bg-white border-b border-gray-100"},Ke={class:"mx-auto px-4 sm:px-6 lg:px-8"},Qe={class:"flex justify-between h-16"},Ze={class:"flex"},Xe={class:"shrink-0 flex items-center"},Ye={class:"hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"},et={class:"hidden lg:flex items-center cursor-pointer z-10"},tt={class:"menu menu-horizontal"},st={class:"w-48"},ot=["href"],rt=["href"],nt=["href"],at=["href"],lt={class:"hidden sm:flex sm:items-center sm:ms-6"},it={class:"ms-3 relative"},ut={class:"dropdown dropdown-end px-3 py-2 text-sm"},dt={tabindex:"0",role:"button",class:"btn btn-sm m-1 bg-slate-200 border-none hover:bg-slate-100"},ct=e("svg",{class:"-me-0.5 h-4 w-4",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M19.5 8.25l-7.5 7.5-7.5-7.5"})],-1),ht={tabindex:"0",class:"dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52"},pt=["href"],ft={class:"ms-3 relative"},gt={class:"inline-flex rounded-md"},mt={type:"button",class:"inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150"},_t=e("svg",{class:"ms-2 -me-0.5 h-4 w-4",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"})],-1),vt={class:"w-60"},bt=e("div",{class:"block px-4 py-2 text-xs text-gray-400"}," Manage Team ",-1),wt=e("div",{class:"border-t border-gray-200"},null,-1),yt=e("div",{class:"block px-4 py-2 text-xs text-gray-400"}," Switch Teams ",-1),kt=["onSubmit"],xt={class:"flex items-center"},$t={key:0,class:"me-2 h-5 w-5 text-green-400",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor"},Ct=e("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"},null,-1),jt=[Ct],St={class:"ms-3 relative"},Tt={key:0,class:"flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition"},Mt=["src","alt"],Lt={key:1,class:"inline-flex rounded-md"},Bt={type:"button",class:"inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150"},Et=e("svg",{class:"ms-2 -me-0.5 h-4 w-4",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M19.5 8.25l-7.5 7.5-7.5-7.5"})],-1),Nt=e("div",{class:"block px-4 py-2 text-xs text-gray-400"}," Manage Account ",-1),Pt=e("div",{class:"border-t border-gray-200"},null,-1),zt={class:"ms-3 relative"},It={class:"-me-2 flex items-center sm:hidden"},At={class:"h-6 w-6",stroke:"currentColor",fill:"none",viewBox:"0 0 24 24"},Dt={class:"pt-2 pb-3 space-y-1"},Ft={class:"pt-4 pb-1 border-t border-gray-200"},Ot={class:"flex items-center px-4"},Rt={key:0,class:"shrink-0 me-3"},Ut=["src","alt"],Gt={class:"font-medium text-base text-gray-800"},Ht={class:"font-medium text-sm text-gray-500"},Vt={class:"mt-3 space-y-1"},qt=e("div",{class:"border-t border-gray-200"},null,-1),Wt=e("div",{class:"block px-4 py-2 text-xs text-gray-400"}," Manage Team ",-1),Jt=e("div",{class:"border-t border-gray-200"},null,-1),Kt=e("div",{class:"block px-4 py-2 text-xs text-gray-400"}," Switch Teams ",-1),Qt=["onSubmit"],Zt={class:"flex items-center"},Xt={key:0,class:"me-2 h-5 w-5 text-green-400",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor"},Yt=e("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"},null,-1),es=[Yt],ts={key:0,class:"bg-white"},ss={class:"mx-auto py-4 px-4 sm:px-6"},as={__name:"AppLayout",props:{title:String},setup(n){const l=R(),s=C("Language");l.props.languages!=null&&l.props.locale!=null&&l.props.languages[l.props.locale]!=null&&(s.value=l.props.languages[l.props.locale]);const r=C(!1),m=t=>{U.put(route("current-team.update"),{team_id:t.id},{preserveState:!1})},p=()=>{U.post(route("logout"))};return(t,_)=>(o(),i("div",null,[f(h(X),{title:n.title},null,8,["title"]),f(we),f(ee),e("div",We,[e("nav",Je,[e("div",Ke,[e("div",Qe,[e("div",Ze,[e("div",Xe,[f(h(z),{href:t.route("index")},{default:d(()=>[f(ae,{class:"block h-9 w-auto"})]),_:1},8,["href"])]),e("div",Ye,[f(B,{href:t.route("image.index"),active:t.route().current("image.index")},{default:d(()=>[g(u(h(b)("image")),1)]),_:1},8,["href","active"]),f(B,{href:t.route("text.index"),active:t.route().current("text.index")},{default:d(()=>[g(u(h(b)("Text")),1)]),_:1},8,["href","active"]),e("div",et,[e("ul",tt,[e("li",null,[e("details",null,[e("summary",null,u(h(b)("Tool")),1),e("ul",st,[e("li",null,[e("a",{href:t.route("tools.cropper")},u(h(b)("Edit image")),9,ot)]),e("li",null,[e("a",{href:t.route("tools.upscale")},u(h(b)("HD image")),9,rt)]),e("li",null,[e("a",{href:t.route("tools.removebg")},u(h(b)("Remove BG")),9,nt)]),e("li",null,[e("a",{href:t.route("tools.faceswap")},u(h(b)("Face swap")),9,at)])])])])])]),f(B,{href:t.route("works.index"),active:t.route().current("works.index")},{default:d(()=>[g(u(h(b)("works")),1)]),_:1},8,["href","active"]),f(B,{href:t.route("models"),active:t.route().current("models")},{default:d(()=>[g(u(h(b)("model")),1)]),_:1},8,["href","active"]),f(B,{href:t.route("config"),active:t.route().current("config")},{default:d(()=>[g(u(h(b)("config")),1)]),_:1},8,["href","active"])])]),e("div",lt,[e("div",it,[e("div",ut,[e("div",dt,[g(u(s.value)+" ",1),ct]),e("ul",ht,[(o(!0),i(S,null,N(t.$page.props.languages,(c,T)=>(o(),i("li",{key:"lang_"+T},[e("a",{href:"/changeLanguage?lang="+T},u(c),9,pt)]))),128))])])]),e("div",ft,[t.$page.props.jetstream.hasTeamFeatures?(o(),$(G,{key:0,align:"right",width:"60"},{trigger:d(()=>[e("span",gt,[e("button",mt,[g(u(t.$page.props.auth.user.current_team.name)+" ",1),_t])])]),content:d(()=>[e("div",vt,[bt,f(M,{href:t.route("teams.show",t.$page.props.auth.user.current_team)},{default:d(()=>[g(" Team Settings ")]),_:1},8,["href"]),t.$page.props.jetstream.canCreateTeams?(o(),$(M,{key:0,href:t.route("teams.create")},{default:d(()=>[g(" Create New Team ")]),_:1},8,["href"])):v("",!0),t.$page.props.auth.user.all_teams.length>1?(o(),i(S,{key:1},[wt,yt,(o(!0),i(S,null,N(t.$page.props.auth.user.all_teams,c=>(o(),i("form",{key:c.id,onSubmit:E(T=>m(c),["prevent"])},[f(M,{as:"button"},{default:d(()=>[e("div",xt,[c.id==t.$page.props.auth.user.current_team_id?(o(),i("svg",$t,jt)):v("",!0),e("div",null,u(c.name),1)])]),_:2},1024)],40,kt))),128))],64)):v("",!0)])]),_:1})):v("",!0)]),e("div",St,[f(G,{align:"right",width:"48"},{trigger:d(()=>[t.$page.props.jetstream.managesProfilePhotos?(o(),i("button",Tt,[e("img",{class:"h-8 w-8 rounded-full object-cover",src:t.$page.props.auth.user.profile_photo_url,alt:t.$page.props.auth.user.name},null,8,Mt)])):(o(),i("span",Lt,[e("button",Bt,[g(u(t.$page.props.auth.user.name)+" ",1),Et])]))]),content:d(()=>[Nt,f(M,{href:t.route("profile.show")},{default:d(()=>[g(" Profile ")]),_:1},8,["href"]),t.$page.props.jetstream.hasApiFeatures?(o(),$(M,{key:0,href:t.route("api-tokens.index")},{default:d(()=>[g(" API Tokens ")]),_:1},8,["href"])):v("",!0),Pt,e("form",{onSubmit:E(p,["prevent"])},[f(M,{as:"button"},{default:d(()=>[g(" Log Out ")]),_:1})],32)]),_:1})]),F(e("div",zt,[f(qe,{label:h(b)("odd"),def:t.$page.props.oddStatus},null,8,["label","def"])],512),[[O,t.$page.props.tuseHasRegister]])]),e("div",It,[e("button",{class:"inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out",onClick:_[0]||(_[0]=c=>r.value=!r.value)},[(o(),i("svg",At,[e("path",{class:k({hidden:r.value,"inline-flex":!r.value}),"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M4 6h16M4 12h16M4 18h16"},null,2),e("path",{class:k({hidden:!r.value,"inline-flex":r.value}),"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M6 18L18 6M6 6l12 12"},null,2)]))])])])]),e("div",{class:k([{block:r.value,hidden:!r.value},"sm:hidden"])},[e("div",Dt,[f(j,{href:t.route("dashboard"),active:t.route().current("dashboard")},{default:d(()=>[g(" Dashboard ")]),_:1},8,["href","active"])]),e("div",Ft,[e("div",Ot,[t.$page.props.jetstream.managesProfilePhotos?(o(),i("div",Rt,[e("img",{class:"h-10 w-10 rounded-full object-cover",src:t.$page.props.auth.user.profile_photo_url,alt:t.$page.props.auth.user.name},null,8,Ut)])):v("",!0),e("div",null,[e("div",Gt,u(t.$page.props.auth.user.name),1),e("div",Ht,u(t.$page.props.auth.user.email),1)])]),e("div",Vt,[f(j,{href:t.route("profile.show"),active:t.route().current("profile.show")},{default:d(()=>[g(" Profile ")]),_:1},8,["href","active"]),t.$page.props.jetstream.hasApiFeatures?(o(),$(j,{key:0,href:t.route("api-tokens.index"),active:t.route().current("api-tokens.index")},{default:d(()=>[g(" API Tokens ")]),_:1},8,["href","active"])):v("",!0),e("form",{method:"POST",onSubmit:E(p,["prevent"])},[f(j,{as:"button"},{default:d(()=>[g(" Log Out ")]),_:1})],32),t.$page.props.jetstream.hasTeamFeatures?(o(),i(S,{key:1},[qt,Wt,f(j,{href:t.route("teams.show",t.$page.props.auth.user.current_team),active:t.route().current("teams.show")},{default:d(()=>[g(" Team Settings ")]),_:1},8,["href","active"]),t.$page.props.jetstream.canCreateTeams?(o(),$(j,{key:0,href:t.route("teams.create"),active:t.route().current("teams.create")},{default:d(()=>[g(" Create New Team ")]),_:1},8,["href","active"])):v("",!0),t.$page.props.auth.user.all_teams.length>1?(o(),i(S,{key:1},[Jt,Kt,(o(!0),i(S,null,N(t.$page.props.auth.user.all_teams,c=>(o(),i("form",{key:c.id,onSubmit:E(T=>m(c),["prevent"])},[f(j,{as:"button"},{default:d(()=>[e("div",Zt,[c.id==t.$page.props.auth.user.current_team_id?(o(),i("svg",Xt,es)):v("",!0),e("div",null,u(c.name),1)])]),_:2},1024)],40,Qt))),128))],64)):v("",!0)],64)):v("",!0)])])],2)]),t.$slots.header?(o(),i("header",ts,[e("div",ss,[x(t.$slots,"header")])])):v("",!0),e("main",null,[x(t.$slots,"default")])])]))}};export{as as _};
