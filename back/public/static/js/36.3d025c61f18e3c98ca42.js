webpackJsonp([36],{"94lo":function(e,t,n){var a={"./views/user/walletrecieve":"PCNV"};function s(e){return n(o(e))}function o(e){var t=a[e];if(!(t+1))throw new Error("Cannot find module '"+e+"'.");return t}s.keys=function(){return Object.keys(a)},s.resolve=o,e.exports=s,s.id="94lo"},PCNV:function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var a=n("KDzu"),s=(a.a,{components:{Qrcode:a.a},data:function(){return{qrcodeurl:"",amount:"",address:"",username:"",url:""}},mounted:function(){this.getUserinfo()},methods:{getUserinfo:function(){var e=this;this.$http.post("/api/app.user/account/info",{}).then(function(t){e.username=t.data.account_info.username,e.address=t.data.account_info.address}).catch(function(t){t.errcode&&e.$vux.toast.text(t.message)})},changeAmount:function(){this.qrcodeurl="/user/wallet/send?amount="+this.amount+"&to_address="+this.address},checkAmount:function(e){return{valid:e>0,msg:"数量必须大于零"}}}}),o={render:function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"update"},[n("div",{staticClass:"update-box"},[n("div",{staticClass:"update-gxs vux-1px-b"},[n("x-input",{attrs:{type:"number","show-clear":!1,required:!0,"is-type":e.checkAmount,placeholder:"填写接收数量"},on:{"on-change":e.changeAmount},model:{value:e.amount,callback:function(t){e.amount=t},expression:"amount"}})],1),e._v(" "),n("qrcode",{staticClass:"qrcode",attrs:{value:e.qrcodeurl,type:"img"}}),e._v(" "),n("div",{staticClass:"account-name"},[e._v("账户名："+e._s(e.username))])],1)])},staticRenderFns:[]};var r=n("C7Lr")(s,o,!1,function(e){n("xums")},null,null);t.default=r.exports},xums:function(e,t){}});