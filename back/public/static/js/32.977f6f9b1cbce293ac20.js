webpackJsonp([32],{Fzl9:function(t,e,a){"use strict";(function(t){var e=a("CHv/"),s=a("eT+W"),i=a("CltZ"),o=a("hpCS"),n=a("DEX7"),c=a("ocpe"),r=a("jH8X"),l=a("EOtl");r.a,e.a,s.a,i.a,o.a,n.a,c.a,l.a}).call(e,a("L7Pj"))},LSm6:function(t,e,a){"use strict";(function(t){var s=a("CHv/"),i=a("eT+W"),o=a("CltZ"),n=a("hpCS"),c=a("DEX7"),r=a("ocpe"),l=a("jH8X"),u=a("EOtl");e.a={directives:{TransferDom:l.a},components:{XInput:s.a,XButton:i.a,Box:o.a,PopupRadio:n.a,XSwitch:c.a,Loading:r.a,Radio:u.a},data:function(){return{coin_info:[],lock:!1,chose_coin:"",coin_info_detail:{},vc_total:{},show_coin:{},asset:0,lock_time:"",disable:!1,amount:"",disable_desc:"",coin_unit:this.$store.state.init.coin_uint,loading:!0,sugar_type:"day",mobile:"",sugar_type_chose:[{key:"day",value:"按天"},{key:"week",value:"按周"},{key:"month",value:"按月"}],show_time_type:"天",security:"",auth_type:2,vc_type:[],vc_chose_type:"vc_normal",vc_normal:"",vc_untrade:"",start_day:1}},created:function(){this.get_auth_info()},methods:{get_auth_info:function(){var t=this;this.$http.post("/api/app.user/locktransfer/auth",{}).then(function(e){var a=e.data.auth_info;for(var s in a)t.coin_info.push({key:s,value:a[s].coin_unit}),0==s&&(t.vc_normal=a[s].assets.vc_normal,t.vc_untrade=a[s].assets.vc_untrade);t.chose_coin=t.$route.params.coin_type.toString(),t.coin_info_detail=a,t.vc_total=parseFloat(a[0].assets.amount),t.hide_change(),e.data.has_sec||(t.$vux.toast.text("您还未设置资产密码,请前去设置"),setTimeout(function(){t.$router.push({name:"editSecurity"})},2e3)),t.loading=!1}).catch(function(e){e.errcode&&t.$vux.toast.text(e.message)})},hide_change:function(){var t=this.chose_coin;if(this.show_coin=this.coin_info_detail[t],this.asset="剩余资产："+this.show_coin.assets.amount,1==this.show_coin.auth_info.status){if(this.disable=!1,parseFloat(this.vc_total)<parseFloat(this.show_coin.auth_info.min_limit)){this.disable=!0;var e=parseFloat(this.show_coin.auth_info.min_limit).toFixed(5);return this.disable_desc="您的"+this.coin_unit+"实时资产低于"+e+",已被限制锁仓转出",this.lock=!1,!1}this.lock=!0,1==this.show_coin.auth_info.auth_type&&(this.lock_time=this.show_coin.auth_info.limit_day),this.auth_type=this.show_coin.auth_info.auth_type}else this.disable=!0,this.lock=!1,this.disable_desc="您没有锁仓权限"},grant:function(){var t=this,e=this.lock_time,a=this;this.lock||(e=0);var s={coin_type:this.chose_coin,amount:this.amount,lock_time:e,mobile:this.mobile,sugar_type:this.sugar_type,security:this.security,vc_chose_type:this.vc_chose_type,start_day:this.start_day};return this.lock_time!=Math.round(this.lock_time)?(this.$vux.toast.text("请输入整的锁仓时间"),!1):this.lock_time.length>5?(this.$vux.toast.text("您输入的锁仓天数太久，请重新输入"),!1):this.lock&&!this.lock_time?(this.$vux.toast.text("请输入锁仓时间"),!1):this.security?(this.loading=!0,void this.$http.post("/api/app.user/locktransfer/grant",s).then(function(e){if(t.loading=!1,0==e.errcode){var s={coin_unit:a.show_coin.coin_unit,sugar_info:e.data.sugar,time_type:t.show_time_type};a.$router.push({name:"lock_transfer_success",params:s})}else t.$vux.toast.text(e.message)}).catch(function(e){t.loading=!1,e.errcode&&t.$vux.toast.text(e.message)})):(this.$vux.toast.text("请输入密码"),!1)},change_show_text:function(t,e){this.show_time_type=e[1]},radio_btn:function(e){var a=e.currentTarget;this.vc_chose_type=t(a).attr("data-type"),t(a).addClass("active").siblings().removeClass("active")}},computed:{sugar_fee:function(){if(this.lock&&this.show_coin.hasOwnProperty("auth_info")){var t=this.show_coin.price/parseFloat(this.coin_info_detail[0].price);return"共需冲消"+(this.amount*this.show_coin.auth_info.receive_fee*t).toFixed(5)+" "+this.coin_unit}return""},day_release:function(){var t=parseFloat(this.amount),e=parseInt(this.lock_time);return t>0&&e?"每日释放"+(t/e).toFixed(5):""}}}}).call(e,a("L7Pj"))},"f/XE":function(t,e){},n9lO:function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0});a("Fzl9");var s=a("LSm6"),i={render:function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"page-grant"},[t.loading?t._e():a("div",[a("div",{staticClass:"candy-basic-opera"},[a("group",{staticClass:"assets-type vux-1px-b"},[a("popup-radio",{attrs:{title:"资产类型",options:t.coin_info},on:{"on-hide":t.hide_change},model:{value:t.chose_coin,callback:function(e){t.chose_coin=e},expression:"chose_coin"}},[a("p",{staticClass:"vux-1px-b grant-coin-slot",attrs:{slot:"popup-header"},slot:"popup-header"},[t._v("资产类型")])])],1),t._v(" "),a("group",{staticClass:"numb-assets"},[a("cell",{staticClass:"cell-assets",attrs:{title:"转出数额",value:t.asset}}),t._v(" "),a("div",{directives:[{name:"show",rawName:"v-show",value:0==t.chose_coin,expression:"chose_coin==0"}],staticClass:"candy-radio flex-box"},[a("div",{staticClass:"radio-item flex-1 active",attrs:{"data-type":"vc_normal"},on:{click:t.radio_btn}},[a("div",{staticClass:"radio-title"},[t._v("可流通")]),t._v(" "),a("div",{staticClass:"radio-numb"},[t._v(t._s(t.vc_normal))])]),t._v(" "),a("div",{staticClass:"radio-item flex-1",attrs:{"data-type":"vc_untrade"},on:{click:t.radio_btn}},[a("div",{staticClass:"radio-title"},[t._v("不可流通")]),t._v(" "),a("div",{staticClass:"radio-numb"},[t._v(t._s(t.vc_untrade))])])]),t._v(" "),a("x-input",{staticClass:"grant-money",attrs:{placeholder:"请输入转出数额",type:"number",required:!0},model:{value:t.amount,callback:function(e){t.amount=e},expression:"amount"}})],1)],1),t._v(" "),a("div",{staticClass:"candy-basic-opera"},[a("group",{staticClass:"candy-numb vux-1px-b",attrs:{title:"转入人手机号"}},[a("x-input",{attrs:{"is-type":"china-mobile",placeholder:"请输入转入人手机号",keyboard:"number",type:"number",required:!0},model:{value:t.mobile,callback:function(e){t.mobile=e},expression:"mobile"}})],1),t._v(" "),a("group",{staticClass:"candy-numb",attrs:{title:"资产密码"}},[a("x-input",{attrs:{type:"password",placeholder:"请输入资产密码",required:!0},model:{value:t.security,callback:function(e){t.security=e},expression:"security"}})],1)],1),t._v(" "),a("div",{staticClass:"candy-senior-opera"},[a("group",{staticClass:"lock-time"},[a("div",{staticClass:"lock-select flex-box"},[a("div",{staticClass:"lock-se-title flex-1"},[t._v("是否锁仓")]),t._v(" "),a("x-switch",{attrs:{title:"锁仓",disabled:!0},model:{value:t.lock,callback:function(e){t.lock=e},expression:"lock"}})],1),t._v(" "),a("x-input",{directives:[{name:"show",rawName:"v-show",value:t.lock,expression:"lock"}],staticClass:"candy-numb",attrs:{disabled:1==t.auth_type,placeholder:"请输入锁定时间",keyboard:"number",type:"number",max:5},model:{value:t.lock_time,callback:function(e){t.lock_time=e},expression:"lock_time"}},[a("x-button",{attrs:{slot:"right",type:"primary",mini:""},slot:"right"},[t._v(t._s(t.show_time_type))])],1),t._v(" "),a("x-input",{directives:[{name:"show",rawName:"v-show",value:t.lock,expression:"lock"}],staticClass:"candy-numb",attrs:{placeholder:"请输入开始释放天数",keyboard:"number",type:"number",max:5},model:{value:t.start_day,callback:function(e){t.start_day=e},expression:"start_day"}},[a("x-button",{attrs:{slot:"right",type:"primary",mini:""},slot:"right"},[t._v("天后开始释放")])],1),t._v(" "),a("group",{directives:[{name:"show",rawName:"v-show",value:t.lock,expression:"lock"}],attrs:{title:t.day_release}})],1)],1),t._v(" "),a("group",{staticClass:"grant-bottom"},[a("cell",[a("div",{directives:[{name:"show",rawName:"v-show",value:t.lock,expression:"lock"}]},[a("span",{staticStyle:{color:"red"}},[t._v(t._s(t.sugar_fee))])]),t._v(" "),a("div",{directives:[{name:"show",rawName:"v-show",value:t.disable,expression:"disable"}]},[a("span",{staticStyle:{color:"red"}},[t._v(t._s(t.disable_desc))])])])],1),t._v(" "),a("box",{staticClass:"grant-btn-box",attrs:{gap:"0 0"}},[(t.disable,a("x-button",{staticStyle:{"border-radius":"0",height:"2.875rem","font-size":"0.875rem"},attrs:{type:"primary"},nativeOn:{click:function(e){return t.grant(e)}}},[t._v("转出")]))],1)],1),t._v(" "),a("div",{directives:[{name:"transfer-dom",rawName:"v-transfer-dom"}]},[a("loading",{attrs:{show:t.loading}})],1)])},staticRenderFns:[]};var o=function(t){a("f/XE")},n=a("C7Lr")(s.a,i,!1,o,"data-v-263eb81a",null);e.default=n.exports}});