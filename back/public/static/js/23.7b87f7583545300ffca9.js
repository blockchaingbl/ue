webpackJsonp([23],{H9B5:function(t,e){},J7eK:function(t,e,i){var s={"./views/deals/push":"im/+"};function a(t){return i(o(t))}function o(t){var e=s[t];if(!(e+1))throw new Error("Cannot find module '"+t+"'.");return e}a.keys=function(){return Object.keys(s)},a.resolve=o,t.exports=a,a.id="J7eK"},"im/+":function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0});i("kQ6d");var s=i("nfzk"),a={render:function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{staticClass:"deals-push-box"},[i("div",{staticClass:"head flex-box"},[i("div",{staticClass:"head-text flex-1"},[t._v("剩余"+t._s(t.$store.state.init.coin_uint)+"："),i("span",[t._v(t._s(t.vc_total))]),t._v("  可流通："),i("span",[t._v(t._s(t.vc_normal))])])]),t._v(" "),i("div",{staticClass:"push-content"},[i("div",{staticClass:"push-item title"},[t._v("出让数量")]),t._v(" "),i("div",{staticClass:"push-item vux-1px-b"},[i("x-input",{attrs:{type:"number",placeholder:"请输入出让数量",required:!0,"is-type":t.checkSaleNum},model:{value:t.sale_amount,callback:function(e){t.sale_amount=e},expression:"sale_amount"}})],1),t._v(" "),i("div",{directives:[{name:"show",rawName:"v-show",value:t.$store.state.init.otc_sale_price,expression:"$store.state.init.otc_sale_price"}],staticClass:"push-item title"},[t._v("单价")]),t._v(" "),i("div",{directives:[{name:"show",rawName:"v-show",value:t.$store.state.init.otc_sale_price,expression:"$store.state.init.otc_sale_price"}],staticClass:"push-item input-box",attrs:{id:"sale_price"}},[i("x-number",{attrs:{min:0,max:999999,step:(t.sale_price_max-t.sale_price_min)/10,fillable:!0,"button-style":"round",align:"left",width:"100px"},model:{value:t.coin_price,callback:function(e){t.coin_price=e},expression:"coin_price"}})],1),t._v(" "),i("div",{staticClass:"push-item title"},[t._v("出让行情")]),t._v(" "),i("div",{staticClass:"push-item input-box vux-1px-b"},[i("input",{directives:[{name:"model",rawName:"v-model",value:t.sale_total,expression:"sale_total"}],staticClass:"price",attrs:{type:"text",readonly:"true"},domProps:{value:t.sale_total},on:{input:function(e){e.target.composing||(t.sale_total=e.target.value)}}})]),t._v(" "),i("div",{staticClass:"push-item title"},[t._v("手续费("+t._s(t.$store.state.init.coin_uint)+")")]),t._v(" "),i("div",{staticClass:"push-item input-box"},[i("input",{directives:[{name:"model",rawName:"v-model",value:t.otc_fee,expression:"otc_fee"}],staticClass:"price",attrs:{type:"text",readonly:"true"},domProps:{value:t.otc_fee},on:{input:function(e){e.target.composing||(t.otc_fee=e.target.value)}}})])]),t._v(" "),i("div",{staticClass:"candy-senior-opera"},[i("group",{staticClass:"lock-time"},[i("div",{staticClass:"lock-select flex-box"},[i("div",{staticClass:"lock-se-title flex-1"},[t._v("是否锁仓")]),t._v(" "),i("x-switch",{attrs:{title:"锁仓",disabled:!0},model:{value:t.lock,callback:function(e){t.lock=e},expression:"lock"}})],1),t._v(" "),i("x-input",{directives:[{name:"show",rawName:"v-show",value:t.lock,expression:"lock"}],staticClass:"candy-numb",attrs:{placeholder:"请输入锁定时间",keyboard:"number",type:"number",max:5,readonly:2==t.otc_auth_type},model:{value:t.lock_day,callback:function(e){t.lock_day=e},expression:"lock_day"}},[i("x-button",{attrs:{slot:"right",type:"primary",mini:""},slot:"right"},[t._v("天")])],1),t._v(" "),i("group",{directives:[{name:"show",rawName:"v-show",value:t.lock,expression:"lock"}],attrs:{title:t.day_release}})],1)],1),t._v(" "),i("group",{directives:[{name:"show",rawName:"v-show",value:0==t.otc_auth_type,expression:"otc_auth_type==0"}]},[i("cell",[i("div",{directives:[{name:"show",rawName:"v-show",value:0==t.otc_auth_type,expression:"otc_auth_type==0"}]},[i("span",{staticStyle:{color:"red"}},[t._v("您没有锁仓权限")])])])],1),t._v(" "),i("box",{attrs:{gap:"32px 35px 0"}},[i("x-button",{staticClass:"push-btn",staticStyle:{"border-radius":"99px"},attrs:{type:"primary"},nativeOn:{click:function(e){t.sure_push()}}},[t._v("出让")])],1)],1)},staticRenderFns:[]};var o=function(t){i("H9B5")},c=i("C7Lr")(s.a,a,!1,o,null,null);e.default=c.exports},kQ6d:function(t,e,i){"use strict";(function(t){var e=i("EjLo"),s=i("DEX7");e.a,s.a}).call(e,i("L7Pj"))},nfzk:function(t,e,i){"use strict";(function(t){var s=i("EjLo"),a=i("DEX7");e.a={components:{XNumber:s.a,XSwitch:a.a},data:function(){return{vc_total:0,vc_normal:0,coin_price:0,sale_amount:"",sale_price_min:0,sale_price_max:0,lock:!1,lock_day:"",otc_auth_type:null}},computed:{sale_total:function(){var t=this.sale_amount;return t||(t=0),(t=parseFloat(t))<this.$store.state.init.min_otc_sale?0:(1e9*t*this.coin_price/1e9).toFixed(2)||0},otc_fee:function(){var t=this.sale_amount;return t||(t=0),(t=parseFloat(t))<this.$store.state.init.min_otc_sale?0:(1e9*t*this.otc_fee_rate/1e9).toFixed(8)||0},day_release:function(){var t=parseFloat(this.sale_amount),e=parseInt(this.lock_day);return t&&e?"每日释放"+(t/e).toFixed(5):""}},mounted:function(){this.getUserinfo(),this.coin_price=parseFloat(this.$store.state.init.coin_price),this.otc_fee_rate=parseFloat(this.$store.state.init.otc_fee_rate),this.sale_price_min=this.coin_price-this.coin_price*parseFloat(this.$store.state.init.otc_saleprice_rate),this.sale_price_max=this.coin_price+this.coin_price*parseFloat(this.$store.state.init.otc_saleprice_rate),t("#sale_price").find("input").unbind("blur");var e=this;t("#sale_price").find("input").bind("blur",function(){e.checkSalePrice()})},methods:{getUserinfo:function(){var t=this;this.$http.post("/api/app.user/account/info",{}).then(function(e){t.vc_total=e.data.account_info.vc_total,t.vc_normal=e.data.account_info.vc_normal,t.otc_auth_type=e.data.account_info.otc_auth_type,2==t.otc_auth_type&&(t.lock_day=e.data.account_info.limit_day),0!=t.otc_auth_type&&(t.lock=!0)}).catch(function(e){e.errcode&&t.$vux.toast.text(e.message)})},checkSalePrice:function(){this.coin_price<this.sale_price_min?this.$vux.toast.text("行情不得小于最低价"+this.sale_price_min):this.coin_price>this.sale_price_max&&this.$vux.toast.text("行情不得大于最高价"+this.sale_price_max)},sure_push:function(){var t=this;if(this.coin_price<this.sale_price_min)this.$vux.toast.text("行情不得小于最低价"+this.sale_price_min);else if(this.coin_price>this.sale_price_max)this.$vux.toast.text("行情不得大于最高价"+this.sale_price_max);else{var e={amount:this.sale_amount,price:this.coin_price,is_lock:Number(this.lock),lock_day:this.lock_day};this.$http.post("/api/app.otc/deals/push",e).then(function(e){"0"==e.errcode&&(t.$vux.toast.show({text:"挂单成功"}),t.$router.push({path:"/deals/center"}))}).catch(function(e){e.errcode&&t.$vux.toast.text(e.message)})}},checkSaleNum:function(t){return{valid:parseFloat(t)>=parseFloat(this.$store.state.init.min_otc_sale),msg:"出让数量不得低于"+this.$store.state.init.min_otc_sale}}}}}).call(e,i("L7Pj"))}});