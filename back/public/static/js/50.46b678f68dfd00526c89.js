webpackJsonp([50],{PS3y:function(t,e){},tMbk:function(t,e,a){var n={"./views/wallet/manage":"xAU4"};function r(t){return a(s(t))}function s(t){var e=n[t];if(!(e+1))throw new Error("Cannot find module '"+t+"'.");return e}r.keys=function(){return Object.keys(n)},r.resolve=s,t.exports=r,r.id="tMbk"},xAU4:function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var n=a("1a94"),r=a("ZXFd"),s=a("tXkb"),o=a("j996"),i=a("YaEn"),l=(n.a,r.a,s.a,o.a,{components:{"x-header":n.a,actionsheet:r.a,cell:s.a,badge:o.a},data:function(){return{page_title:i.a.currentRoute.meta.title,page_name:i.a.currentRoute.name}},mounted:function(){},methods:{}}),u={render:function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{attrs:{id:t.page_name}},[a("x-header",{attrs:{"left-options":{showBack:!0,backText:""},"right-options":{showMore:!1}}},[t._v(t._s(t.page_title))]),t._v(" "),a("group",t._l(this.$store.state.wallets,function(e,n){return a("cell",{key:n,attrs:{"is-link":"",link:{path:"/wallet/manage/"+n}}},[a("span",{attrs:{slot:"title"},slot:"title"},[a("div",{staticClass:"wallet_name"},[t._v(t._s(e.name))]),t._v(" "),a("div",{staticClass:"wallet_address"},[t._v(t._s(n))])])])}))],1)},staticRenderFns:[]};var c=a("C7Lr")(l,u,!1,function(t){a("PS3y")},"data-v-525bd454",null);e.default=c.exports}});