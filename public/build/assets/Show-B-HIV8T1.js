import{_ as i}from"./AppLayout-De7LQLNn.js";import o from"./DeleteTeamForm-tP138lci.js";import{S as r}from"./SectionBorder-8xJwVTRQ.js";import l from"./TeamMemberManager-Ca01rAEs.js";import n from"./UpdateTeamNameForm-Cf-OO0Gz.js";import{o as a,c,w as s,a as m,b as t,d as p,F as d,e as f}from"./app-lo3yCkmC.js";import"./_plugin-vue_export-helper-DlAUqK2U.js";import"./TopSnackbar-B1OIxORe.js";import"./Modal-D_GscKQd.js";import"./SectionTitle-q-Vy2Ce_.js";import"./ConfirmationModal-kEopRWUv.js";import"./DangerButton-CFPgDKoE.js";import"./SecondaryButton-Bo6H2dva.js";import"./ActionMessage-C_k2CCjr.js";import"./DialogModal-B5IE2WH3.js";import"./FormSection-B4TTtQ-2.js";import"./InputError-Dhk5TVv7.js";import"./InputLabel-RT0G5a1Z.js";import"./PrimaryButton-BcUpNeSW.js";import"./TextInput-lnQV9y4E.js";const u=m("h2",{class:"font-semibold text-xl text-gray-800 leading-tight"}," Team Settings ",-1),x={class:"max-w-7xl mx-auto py-10 sm:px-6 lg:px-8"},q={__name:"Show",props:{team:Object,availableRoles:Array,permissions:Object},setup(e){return(b,h)=>(a(),c(i,{title:"Team Settings"},{header:s(()=>[u]),default:s(()=>[m("div",null,[m("div",x,[t(n,{team:e.team,permissions:e.permissions},null,8,["team","permissions"]),t(l,{class:"mt-10 sm:mt-0",team:e.team,"available-roles":e.availableRoles,"user-permissions":e.permissions},null,8,["team","available-roles","user-permissions"]),e.permissions.canDeleteTeam&&!e.team.personal_team?(a(),p(d,{key:0},[t(r),t(o,{class:"mt-10 sm:mt-0",team:e.team},null,8,["team"])],64)):f("",!0)])])]),_:1}))}};export{q as default};