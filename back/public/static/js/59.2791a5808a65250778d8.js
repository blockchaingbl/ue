webpackJsonp([59],{"9D4b":function(t,i,a){"use strict";Object.defineProperty(i,"__esModule",{value:!0});var e=a("XaLk"),s=a("ky06"),o=a("DEX7"),n=a("eT+W"),c=a("jH8X"),r=a("mAm1"),l=a("NyFS"),u=a("oa6C"),_=a("xSLg"),h=a("Wf59"),p=(c.a,e.a,s.a,o.a,n.a,r.a,l.a,u.a,h.a,{directives:{TransferDom:c.a},components:{Confirm:e.a,Group:s.a,XSwitch:o.a,XButton:n.a,Flexbox:r.a,FlexboxItem:l.a,Divider:u.a,"vue-core-image-upload":h.a},data:function(){return{show5:!0,useravatar:"",usermobile:"",username:"",editname:"",uploadUrl:"/api/app.util/upload",address:"",params:{type:"avatar",_user_token:this.$store.state.token},lock_transfer_auth:0,url:"https://shop.bmweixin.com/app/index.php?r=qa&i=20&do=mobile&wxref=mp.weixin.qq.com&c=entry&m=ewei_shopv2#wechat_redirect"}},mounted:function(){this.getUserinfo()},methods:{onConfirm5:function(){},onShow5:function(){},getUserinfo:function(){var t=this;this.$http.post("/api/app.user/account/info",{}).then(function(i){t.usermobile=i.data.account_info.mobile,t.username=i.data.account_info.username,t.useravatar=i.data.account_info.avatar,t.address=i.data.account_info.address,t.$store.state.sugar_auth=i.data.account_info.sugar_auth,t.$store.state.lock_transfer_auth=parseInt(i.data.account_info.lock_transfer_auth),t.lock_transfer_auth=parseInt(i.data.account_info.lock_transfer_auth)}).catch(function(i){i.errcode&&t.$vux.toast.text(i.message)})},test:function(){localStorage.clear(),Object(_.a)()},logout:function(){var t=this;this.$vux.confirm.show({title:"确定要退出吗？",onCancel:function(){},onConfirm:function(){localStorage.removeItem("token"),localStorage.clear(),Object(_.a)(),Object(_.d)("isSign",""),t.$store.commit("reset_state"),t.$vux.toast.text("退出成功"),t.$router.push({path:"/login"})}})},tis_btn:function(){this.$vux.toast.text("敬请期待")},open:function(){this.$store.state.init.is_app?App.open_type('{"url":"'+this.url+'"}'):location.href=this.url}}}),f={render:function(){var t=this,i=t.$createElement,a=t._self._c||i;return a("div",{staticClass:"page-setting"},[a("div",{staticClass:"user-list-box"},[a("group",[a("cell",{attrs:{title:"支付设置",link:{path:"/user/setpay"}}},[a("i",{staticClass:"iconfont icon_payset",staticStyle:{display:"block","margin-right":"1rem"},attrs:{slot:"icon",width:"20"},slot:"icon"},[t._v("")])]),t._v(" "),a("cell",{attrs:{title:"资产密码",link:{path:"/user/edit/security"}}},[a("i",{staticClass:"iconfont icon_accset",staticStyle:{display:"block","margin-right":"1rem"},attrs:{slot:"icon",width:"20"},slot:"icon"},[t._v("")])]),t._v(" "),t.$store.state.sugar_auth>0?a("cell",{attrs:{title:"发糖果",link:{path:"/candy/distribute"}}},[a("i",{staticClass:"iconfont  icon_wallet",staticStyle:{display:"block","margin-right":"1rem"},attrs:{slot:"icon",width:"20"},slot:"icon"},[t._v("")])]):t._e(),t._v(" "),a("cell",{directives:[{name:"show",rawName:"v-show",value:t.lock_transfer_auth>0||t.$store.state.lock_transfer_auth>0,expression:"lock_transfer_auth >0 || $store.state.lock_transfer_auth>0"}],attrs:{title:"锁仓转出",link:{path:"/lock_transfer/grant/0"}}},[a("i",{staticClass:"iconfont  icon_assets",staticStyle:{display:"block","margin-right":"1rem"},attrs:{slot:"icon",width:"20"},slot:"icon"},[t._v("")])]),t._v(" "),a("cell",{attrs:{title:"我的糖果",link:{path:"/candy/trans"}}},[a("i",{staticClass:"iconfont icon_payset",staticStyle:{display:"block","margin-right":"1rem"},attrs:{slot:"icon",width:"20"},slot:"icon"},[t._v("")])]),t._v(" "),a("cell",{attrs:{title:"矿机商城",link:{path:"/mine_machine"}}},[a("i",{staticClass:"iconfont icon_assets",staticStyle:{display:"block","margin-right":"1rem"},attrs:{slot:"icon",width:"20"},slot:"icon"},[t._v("")])]),t._v(" "),t.$store.state.init.about_open?a("cell",{attrs:{title:"关于我们",link:{path:"/about"}}},[a("i",{staticClass:"iconfont icon_wallet",staticStyle:{display:"block","margin-right":"1rem"},attrs:{slot:"icon",width:"20"},slot:"icon"},[t._v("")])]):t._e(),t._v(" "),t.$store.state.init.connect_open?a("cell",{attrs:{title:"意见反馈",link:{path:"/connect"}}},[a("i",{staticClass:"iconfont icon_accset",staticStyle:{display:"block","margin-right":"1rem"},attrs:{slot:"icon",width:"20"},slot:"icon"},[t._v("")])]):t._e(),t._v(" "),t.$store.state.init.connect_open?a("cell",{attrs:{title:"联系我们",link:{path:"/connect_text"}}},[a("i",{staticClass:"iconfont icon_payset",staticStyle:{display:"block","margin-right":"1rem"},attrs:{slot:"icon",width:"20"},slot:"icon"},[t._v("")])]):t._e(),t._v(" "),t.$store.state.init.notice_open?a("cell",{attrs:{title:"系统公告",link:{path:"/notice"}}},[a("i",{staticClass:"iconfont icon_accset",staticStyle:{display:"block","margin-right":"1rem"},attrs:{slot:"icon",width:"20"},slot:"icon"},[t._v("")])]):t._e(),t._v(" "),a("cell",{attrs:{title:"帮助中心","is-link":""},nativeOn:{click:function(i){return t.open(i)}}},[a("i",{staticClass:"iconfont icon_accset",staticStyle:{display:"block","margin-right":"1rem"},attrs:{slot:"icon",width:"20"},slot:"icon"},[t._v("")])])],1)],1),t._v(" "),a("div",{staticClass:"user-list-box"},[a("group",[a("cell",{attrs:{title:"退出登录"},nativeOn:{click:function(i){t.logout()}}},[a("i",{staticClass:"iconfont icon_back",staticStyle:{display:"block","margin-right":"1rem"},attrs:{slot:"icon",width:"20"},slot:"icon"},[t._v("")])])],1)],1)])},staticRenderFns:[]};var d=a("C7Lr")(p,f,!1,function(t){a("kI4W")},null,null);i.default=d.exports},CX6Y:function(t,i,a){var e={"./views/user/setting":"9D4b"};function s(t){return a(o(t))}function o(t){var i=e[t];if(!(i+1))throw new Error("Cannot find module '"+t+"'.");return i}s.keys=function(){return Object.keys(e)},s.resolve=o,t.exports=s,s.id="CX6Y"},kI4W:function(t,i){}});