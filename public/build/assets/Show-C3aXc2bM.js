import{_ as i}from"./AppLayout-BP3DaxNV.js";import o from"./DeleteTeamForm-Cb9MwP8Q.js";import{S as r}from"./SectionBorder-Fz5d2IuU.js";import l from"./TeamMemberManager-GfdhzA1d.js";import n from"./UpdateTeamNameForm-CZPxmXSc.js";import{o as a,c,w as s,a as m,b as t,d as p,F as d,e as f}from"./app-DEtDzQpV.js";import"./_plugin-vue_export-helper-DlAUqK2U.js";import"./TopSnackbar-DBCITb_-.js";import"./Modal-B3AX_zl6.js";import"./SectionTitle-CHyWJoy_.js";import"./ConfirmationModal-B0GFv6-1.js";import"./DangerButton-cHLCYJdQ.js";import"./SecondaryButton-CDCOdB5Q.js";import"./ActionMessage-Bi5H-d82.js";import"./DialogModal-CrVTYt2s.js";import"./FormSection-c_NvuJfp.js";import"./InputError-CxrX9btH.js";import"./InputLabel-CFSlcqYA.js";import"./PrimaryButton-BqfY-Fmu.js";import"./TextInput-CqZVcz2K.js";const u=m("h2",{class:"font-semibold text-xl text-gray-800 leading-tight"}," Team Settings ",-1),x={class:"max-w-7xl mx-auto py-10 sm:px-6 lg:px-8"},q={__name:"Show",props:{team:Object,availableRoles:Array,permissions:Object},setup(e){return(b,h)=>(a(),c(i,{title:"Team Settings"},{header:s(()=>[u]),default:s(()=>[m("div",null,[m("div",x,[t(n,{team:e.team,permissions:e.permissions},null,8,["team","permissions"]),t(l,{class:"mt-10 sm:mt-0",team:e.team,"available-roles":e.availableRoles,"user-permissions":e.permissions},null,8,["team","available-roles","user-permissions"]),e.permissions.canDeleteTeam&&!e.team.personal_team?(a(),p(d,{key:0},[t(r),t(o,{class:"mt-10 sm:mt-0",team:e.team},null,8,["team"])],64)):f("",!0)])])]),_:1}))}};export{q as default};