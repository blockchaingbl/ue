webpackJsonp([66],{"8j4s":function(e,t,o){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var s=o("jH8X"),a=o("1a94"),r=o("ZXFd"),i=o("tXkb"),n=o("j996"),p=o("eT+W"),c=o("CltZ"),d=o("xmUH"),l=o("5zIW"),u=o("ULxt"),v=o("KDzu"),w=o("mAm1"),x=o("NyFS"),h=o("YaEn"),_=(o("5s1k"),o("sbZO")),m=o("sd/l"),f=(s.a,a.a,r.a,i.a,n.a,p.a,c.a,d.a,l.a,v.a,w.a,x.a,{directives:{TransferDom:s.a},components:{"x-header":a.a,actionsheet:r.a,cell:i.a,badge:n.a,"x-button":p.a,box:c.a,"popup-header":d.a,popup:l.a,qrcode:v.a,Flexbox:w.a,FlexboxItem:x.a},data:function(){this.$store.commit("loadWallets");var e=localStorage.default_address;this.$store.state.wallets[e];return{page_title:h.a.currentRoute.meta.title,page_name:h.a.currentRoute.name,wallet:{},address:"",showChangeName:!1,inputWalletName:"",showChangePassword:!1,old_password:"",password:"",confirm_password:"",need_password:"",showNeedPassword:!1,needPasswordFor:"",export_private_key:"",export_keystore:"",showExportPrivate:!1,showExportKeystore:!1}},mounted:function(){this.getinfo()},methods:{getinfo:function(){var e=this;this.$http.post("/api/app.user/account/info",{}).then(function(t){e.inputWalletName=t.data.account_info.username,e.address=t.data.account_info.address,e.wallet.address=t.data.account_info.address,e.wallet.enc_privateKey=t.data.account_info.privatekey}).catch(function(t){t.errcode&&e.$vux.toast.text(t.message)})},changePassword:function(){this.showChangePassword=!0},doChangePassword:function(){var e=this,t=this.checkPassword(this.old_password);if(""!=t)if(this.$refs.password.validate(),this.$refs.confirm_password.validate(),this.$refs.password.valid&&this.$refs.confirm_password.valid)if(this.password==this.old_password)u.a.show({title:"错误",content:"新密码与旧密码相同",onShow:function(){},onHide:function(){}});else{var o=m.a.encrypt(t,this.password);this.$store.commit("updateWallet",this.wallet),this.$http.post("/api/app.wallet/wallet/setsecurity",{security:this.old_password,new_security:this.password,privatekey:o}).then(function(t){e.old_password="",e.password="",e.confirm_password="",u.a.show({title:"提示",content:t.message,onShow:function(){},onHide:function(){h.a.replace({path:"/user/wallet"})}}),e.cancelPop()}).catch(function(t){t.errcode&&e.$vux.toast.text(t.message)})}else this.$refs.password.forceShowError=!0,this.$refs.confirm_password.forceShowError=!0,this.confirm_password||(this.$refs.confirm_password.errors.equal="输入不一致",this.$refs.confirm_password.getError())},exportPrivate:function(){this.needPasswordFor="export_private",this.showNeedPassword=!0},exportKeystore:function(){this.needPasswordFor="export_keystore",this.showNeedPassword=!0},doNeedPassword:function(){var e=this.checkPassword(this.need_password);if(""!=e){var t=this.$loading({text:"导出中"}),o=this;setTimeout(function(){"export_private"==o.needPasswordFor?(o.export_private_key=e,o.showExportPrivate=!0):(o.export_keystore=_.a.wallet.privatekeyToKeystore(e,o.need_password),o.showExportKeystore=!0),o.showNeedPassword=!1,o.need_password="",t.close()},500)}},cancelPop:function(){this.showChangeName=!1,this.showChangePassword=!1,this.showNeedPassword=!1,this.showExportPrivate=!1,this.showExportKeystore=!1},checkPassword:function(e){var t="",o="";try{t=m.a.decrypt(this.wallet.enc_privateKey,e);o=_.a.wallet.privatekeyToAddress(t)}catch(e){}return o.toLowerCase()!=this.wallet.address.toLowerCase()?(u.a.show({title:"错误",content:"钱包密码错误",onShow:function(){},onHide:function(){}}),""):t},onCopy:function(e){this.$vux.toast.text("复制成功")},onError:function(e){this.$vux.toast.text("复制失败，您可以尝试手动记录")}}}),y={render:function(){var e=this,t=e.$createElement,o=e._self._c||t;return o("div",{attrs:{id:e.page_name}},[o("div",{staticClass:"wallet-head flex-box"},[e._m(0),e._v(" "),o("div",{directives:[{name:"clipboard",rawName:"v-clipboard:copy",value:e.address,expression:"address",arg:"copy"},{name:"clipboard",rawName:"v-clipboard:success",value:e.onCopy,expression:"onCopy",arg:"success"},{name:"clipboard",rawName:"v-clipboard:error",value:e.onError,expression:"onError",arg:"error"}],staticClass:"wallet-private_key"},[e._v(e._s(e.address))])]),e._v(" "),o("group",[o("div",{on:{click:e.changePassword}},[o("cell",{attrs:{"is-link":""}},[o("span",{attrs:{slot:"title"},slot:"title"},[e._v("修改密码")])])],1)]),e._v(" "),o("group",[o("div",{staticClass:"op_row vux-1px-b",on:{click:e.exportPrivate}},[o("cell",{attrs:{"is-link":""}},[o("span",{attrs:{slot:"title"},slot:"title"},[e._v("导出私钥")])])],1),e._v(" "),o("div",{on:{click:e.exportKeystore}},[o("cell",{attrs:{"is-link":""}},[o("span",{attrs:{slot:"title"},slot:"title"},[e._v("导出Keystore")])])],1)]),e._v(" "),o("div",{directives:[{name:"transfer-dom",rawName:"v-transfer-dom"}]},[o("popup",{model:{value:e.showChangeName,callback:function(t){e.showChangeName=t},expression:"showChangeName"}},[o("popup-header",{attrs:{"left-text":"","right-text":"",title:"请输入钱包名称","show-bottom-border":!1},on:{"on-click-left":function(t){e.show1=!1},"on-click-right":function(t){e.show1=!1}}}),e._v(" "),o("box",{attrs:{gap:"0 10px 20px 10px"}},[o("group",[o("x-input",{ref:"wallet_name",attrs:{max:16,min:4,required:!0,placeholder:"请输入钱包的新名称"},model:{value:e.inputWalletName,callback:function(t){e.inputWalletName=t},expression:"inputWalletName"}})],1),e._v(" "),o("flexbox",[o("flexbox-item",[o("x-button",{nativeOn:{click:function(t){return e.cancelPop(t)}}},[e._v("取消")])],1),e._v(" "),o("flexbox-item",[o("x-button",{attrs:{type:"primary"},nativeOn:{click:function(t){return e.doChangeName(t)}}},[e._v("确认")])],1)],1)],1)],1)],1),e._v(" "),o("div",{directives:[{name:"transfer-dom",rawName:"v-transfer-dom"}]},[o("popup",{model:{value:e.showChangePassword,callback:function(t){e.showChangePassword=t},expression:"showChangePassword"}},[o("popup-header",{attrs:{"left-text":"","right-text":"",title:"修改钱包密码","show-bottom-border":!1},on:{"on-click-left":function(t){e.show1=!1},"on-click-right":function(t){e.show1=!1}}}),e._v(" "),o("box",{attrs:{gap:"0 10px 20px 10px"}},[o("group",[o("x-input",{ref:"wallet_password",attrs:{max:16,min:4,required:!0,type:"password",placeholder:"请输入钱包的原密码"},model:{value:e.old_password,callback:function(t){e.old_password=t},expression:"old_password"}}),e._v(" "),o("x-input",{ref:"password",attrs:{placeholder:"钱包新密码",type:"password",min:4,max:16,required:!0},model:{value:e.password,callback:function(t){e.password=t},expression:"password"}}),e._v(" "),o("x-input",{ref:"confirm_password",attrs:{placeholder:"再次输入钱包新密码",type:"password",min:4,max:16,"equal-with":e.password,required:!0},model:{value:e.confirm_password,callback:function(t){e.confirm_password=t},expression:"confirm_password"}})],1),e._v(" "),o("group"),e._v(" "),o("flexbox",[o("flexbox-item",[o("x-button",{nativeOn:{click:function(t){return e.cancelPop(t)}}},[e._v("取消")])],1),e._v(" "),o("flexbox-item",[o("x-button",{attrs:{type:"primary"},nativeOn:{click:function(t){return e.doChangePassword(t)}}},[e._v("确认")])],1)],1)],1)],1)],1),e._v(" "),o("div",{directives:[{name:"transfer-dom",rawName:"v-transfer-dom"}]},[o("popup",{model:{value:e.showNeedPassword,callback:function(t){e.showNeedPassword=t},expression:"showNeedPassword"}},[o("popup-header",{attrs:{"left-text":"","right-text":"",title:"请确认钱包密码","show-bottom-border":!1},on:{"on-click-left":function(t){e.show1=!1},"on-click-right":function(t){e.show1=!1}}}),e._v(" "),o("box",{attrs:{gap:"0 10px 20px 10px"}},[o("group",{staticClass:"password_input"},[o("x-input",{ref:"need_password",attrs:{max:16,min:4,required:!0,type:"password",placeholder:"请输入钱包的密码"},model:{value:e.need_password,callback:function(t){e.need_password=t},expression:"need_password"}})],1),e._v(" "),o("group"),e._v(" "),o("flexbox",[o("flexbox-item",[o("x-button",{nativeOn:{click:function(t){return e.cancelPop(t)}}},[e._v("取消")])],1),e._v(" "),o("flexbox-item",[o("x-button",{attrs:{type:"primary"},nativeOn:{click:function(t){return e.doNeedPassword(t)}}},[e._v("确认")])],1)],1)],1)],1)],1),e._v(" "),o("div",{directives:[{name:"transfer-dom",rawName:"v-transfer-dom"}],staticClass:"dcsy"},[o("popup",{attrs:{height:"100%"},model:{value:e.showExportPrivate,callback:function(t){e.showExportPrivate=t},expression:"showExportPrivate"}},[o("div",{staticClass:"tip"},[o("div",{staticClass:"export_title"},[e._v("私钥导出成功,私钥不可更改，请妥善保存你的私钥")])]),e._v(" "),o("input",{directives:[{name:"model",rawName:"v-model",value:e.export_private_key,expression:"export_private_key"}],attrs:{type:"hidden"},domProps:{value:e.export_private_key},on:{input:function(t){t.target.composing||(e.export_private_key=t.target.value)}}}),e._v(" "),o("div",{directives:[{name:"clipboard",rawName:"v-clipboard:copy",value:e.export_private_key,expression:"export_private_key",arg:"copy"},{name:"clipboard",rawName:"v-clipboard:success",value:e.onCopy,expression:"onCopy",arg:"success"},{name:"clipboard",rawName:"v-clipboard:error",value:e.onError,expression:"onError",arg:"error"}],staticClass:"export_show"},[e._v("\n                "+e._s(e.export_private_key)+"\n                "),o("span",{staticClass:"copyd"},[e._v("点击复制")])]),e._v(" "),o("qrcode",{staticClass:"qrcode",attrs:{value:e.export_private_key}}),e._v(" "),o("box",{staticClass:"export_save"},[o("x-button",{attrs:{type:"primary"},nativeOn:{click:function(t){return e.cancelPop(t)}}},[e._v("已保存好")])],1)],1)],1),e._v(" "),o("div",{directives:[{name:"transfer-dom",rawName:"v-transfer-dom"}],staticClass:"dcsy"},[o("popup",{attrs:{height:"100%"},model:{value:e.showExportKeystore,callback:function(t){e.showExportKeystore=t},expression:"showExportKeystore"}},[o("div",{staticClass:"tip"},[o("div",{staticClass:"export_title"},[e._v("Keystore导出成功,请妥善保存你的Keystore")])]),e._v(" "),o("input",{directives:[{name:"model",rawName:"v-model",value:e.export_keystore,expression:"export_keystore"}],attrs:{type:"hidden"},domProps:{value:e.export_keystore},on:{input:function(t){t.target.composing||(e.export_keystore=t.target.value)}}}),e._v(" "),o("div",{directives:[{name:"clipboard",rawName:"v-clipboard:copy",value:e.export_keystore,expression:"export_keystore",arg:"copy"},{name:"clipboard",rawName:"v-clipboard:success",value:e.onCopy,expression:"onCopy",arg:"success"},{name:"clipboard",rawName:"v-clipboard:error",value:e.onError,expression:"onError",arg:"error"}],staticClass:"export_show_keystore"},[e._v("\n            "+e._s(e.export_keystore)+"\n            "),o("span",{staticClass:"copyd"},[e._v("点击复制")])]),e._v(" "),o("qrcode",{staticClass:"qrcode",attrs:{value:e.export_keystore}}),e._v(" "),o("box",{staticClass:"export_save"},[o("x-button",{attrs:{type:"primary"},nativeOn:{click:function(t){return e.cancelPop(t)}}},[e._v("已保存好")])],1)],1)],1)],1)},staticRenderFns:[function(){var e=this.$createElement,t=this._self._c||e;return t("div",{staticClass:"head-top flex-box"},[t("div",{staticClass:"fangkuai flex-1"},[t("div",{staticClass:"lingxing"})])])}]};var b=o("C7Lr")(f,y,!1,function(e){o("Pxvd")},"data-v-09637953",null);t.default=b.exports},Pxvd:function(e,t){},Zg1t:function(e,t,o){var s={"./views/user/walletmanage":"8j4s"};function a(e){return o(r(e))}function r(e){var t=s[e];if(!(t+1))throw new Error("Cannot find module '"+e+"'.");return t}a.keys=function(){return Object.keys(s)},a.resolve=r,e.exports=a,a.id="Zg1t"}});