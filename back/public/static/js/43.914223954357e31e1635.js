webpackJsonp([43],{"4lcQ":function(t,a){},INjX:function(t,a,e){var s={"./views/deals/record":"mnVo"};function i(t){return e(o(t))}function o(t){var a=s[t];if(!(a+1))throw new Error("Cannot find module '"+t+"'.");return a}i.keys=function(){return Object.keys(s)},i.resolve=o,t.exports=i,i.id="INjX"},mnVo:function(t,a,e){"use strict";Object.defineProperty(a,"__esModule",{value:!0});var s=e("SPhl"),i=e("sNOJ"),o=e("PcJl"),r=e("crx6"),n=e("oa6C"),c=e("ocpe"),l=e("jH8X"),d=e("QOpP"),p=(l.a,s.a,i.a,o.a,r.a,n.a,d.default,c.a,{directives:{TransferDom:l.a},components:{Tab:s.a,TabItem:i.a,Scroller:o.a,LoadMore:r.a,Divider:n.a,Nodata:d.default,Loading:c.a},data:function(){return{hasLogin:0,index01:0,formData:{page:1,type:1},lock:!1,loading:!1,order_list:[],loadings:!0}},mounted:function(){2==this.$route.params.type&&(this.formData.type=2),this.loadOtcOrders()},methods:{loadOtcOrders:function(){var t=this;if(this.lock)return!1;this.lock=!0,this.loading=!0,this.$http.post("/api/app.otc/order",this.formData).then(function(a){a.data.order_list.length>0&&(t.lock=!1,t.order_list=t.order_list.concat_unk(a.data.order_list,"id")),t.formData.page++,t.loading=!1,t.loadings=!1}).catch(function(a){t.loading=!1})},onScrollBottom:function(){this.loadOtcOrders()},switchType:function(t){t!=this.formData.type&&this.reload(t)},reload:function(t){this.$refs.scrollerEvent.reset({top:0}),this.formData={type:t,page:1},this.loadings=!0,this.order_list=[],this.lock=!1,this.loadOtcOrders()},appeal:function(t){var a=this;this.$vux.confirm.show({content:"是否确认申诉",onConfirm:function(){var e={order_id:t,type:a.formData.type};a.$http.post("/api/app.otc/order/appeal",e).then(function(t){a.$vux.toast.text("申诉成功"),setTimeout(function(){a.reload(a.formData.type)},2e3)}).catch(function(t){t.errcode&&a.$vux.toast.text(t.message)})}})},turnOrder:function(t){1!=this.formData.type||0!=t.status&&1!=t.status?2==this.formData.type&&1==t.status&&this.$router.push({name:"dealsBuy",params:{otc_order_id:t.id,type:2}}):this.$router.push({name:"dealsBuy",params:{otc_order_id:t.id,type:1}})},cancel:function(t){var a=this;this.$vux.confirm.show({content:"是否确认取消订单",onConfirm:function(){var e={order_id:t.id};a.$http.post("/api/app.otc/order/cancel",e).then(function(t){0==t.errcode?(a.$vux.toast.text("已取消"),setTimeout(function(){a.reload(a.formData.type)},2e3)):a.$vux.toast.text(t.message)}).catch(function(t){t.errcode&&a.$vux.toast.text(t.message)})}})}}}),v={render:function(){var t=this,a=t.$createElement,e=t._self._c||a;return e("scroller",{ref:"scrollerEvent",attrs:{height:"100%","lock-x":""},on:{"on-scroll-bottom":t.onScrollBottom}},[e("div",{staticClass:"deals-record-box"},[e("div",{staticClass:"head"},[t._v("\n            我的订单\n        ")]),t._v(" "),e("tab",{attrs:{"line-width":2,"custom-bar-width":"60px"}},[e("tab-item",{attrs:{selected:1==this.formData.type},nativeOn:{click:function(a){t.switchType(1)}}},[t._v("受让")]),t._v(" "),e("tab-item",{attrs:{selected:2==this.formData.type},nativeOn:{click:function(a){t.switchType(2)}}},[t._v("出让")])],1),t._v(" "),t.loadings?t._e():e("div",[t.order_list.length>0?e("div",{staticClass:"record-block"},[t._l(t.order_list,function(a){return e("div",{staticClass:"item flex-box",on:{click:function(e){t.turnOrder(a)}}},[e("div",{staticClass:"item-text flex-1"},[e("div",{staticClass:"title"},[t._v("数量："),e("span",[t._v(t._s(a.vc_amount))])]),t._v(" "),e("div",{staticClass:"money flex-box"},[e("div",{staticClass:"get flex-1"},[t._v("行情："),e("span",[t._v("¥"+t._s(a.vc_total_price))])]),t._v(" "),e("div",{staticClass:"price flex-1"},[t._v("单价：¥ "+t._s(a.vc_uint_price))])]),t._v(" "),e("div",{staticClass:"account-number"},[t._v("下单时间："+t._s(a.create_time))])]),t._v(" "),e("div",{staticClass:"order-sn"},[t._v("单号："+t._s(a.order_sn))]),t._v(" "),1==t.formData.type?e("div",{staticClass:"item-type-box"},[0==a.status?e("div",{staticClass:"item-type unaudited"},[t._v("待付款")]):t._e(),t._v(" "),1==a.status?e("div",{staticClass:"item-type paid"},[t._v("已付款")]):t._e(),t._v(" "),2==a.status?e("div",{staticClass:"item-type fail"},[t._v("已完成")]):t._e(),t._v(" "),3==a.status?e("div",{staticClass:"item-type fail"},[t._v("已取消")]):t._e()]):t._e(),t._v(" "),1==t.formData.type?e("div",{staticClass:"item-btn-box"},[0!=a.appeal_status&&2!=a.appeal_status||1!=a.status||!a.seller_time_over?3==a.appeal_status?e("div",{staticClass:"item-btn appeal",staticStyle:{width:"5rem"}},[t._v("申诉已处理")]):t._e():e("div",{staticClass:"item-btn appeal",on:{click:function(e){e.stopPropagation(),t.appeal(a.id)}}},[t._v("申诉")]),t._v(" "),1==a.appeal_status||4==a.appeal_status?e("div",{staticClass:"item-btn appeal"},[t._v("申诉中")]):t._e()]):t._e(),t._v(" "),2==t.formData.type?e("div",{staticClass:"item-type-box"},[0==a.status?e("div",{staticClass:"item-type unaudited"},[t._v("待付款")]):t._e(),t._v(" "),1==a.status?e("div",{staticClass:"item-type paid"},[t._v("待发GBL")]):t._e(),t._v(" "),2==a.status?e("div",{staticClass:"item-type fail"},[t._v("已完成")]):t._e(),t._v(" "),3==a.status?e("div",{staticClass:"item-type fail"},[t._v("已取消")]):t._e()]):t._e(),t._v(" "),2==t.formData.type?e("div",{staticClass:"item-bnt-box"},[0!=a.appeal_status&&1!=a.appeal_status||1!=a.status?t._e():e("div",{staticClass:"item-btn appeal",on:{click:function(e){e.stopPropagation(),t.appeal(a.id)}}},[t._v("申诉")]),t._v(" "),2==a.appeal_status||4==a.appeal_status?e("div",{staticClass:"item-btn appeal"},[t._v("申诉中")]):t._e(),t._v(" "),3==a.appeal_status?e("div",{staticClass:"item-btn appeal",staticStyle:{width:"5rem"}},[t._v("申诉已处理")]):t._e()]):t._e()])}),t._v(" "),t.loading?e("load-more",{attrs:{tip:"正在加载 . . ."}}):e("load-more",{attrs:{"show-loading":!1,tip:"没有更多了","background-color":"#fbf9fe"}}),t._v(" "),e("div",{staticClass:"loadmore"})],2):e("nodata",{attrs:{datatip:"暂无数据"}})],1),t._v(" "),e("div",{directives:[{name:"transfer-dom",rawName:"v-transfer-dom"}]},[e("loading",{attrs:{show:t.loadings}})],1)],1)])},staticRenderFns:[]};var _=e("C7Lr")(p,v,!1,function(t){e("4lcQ")},"data-v-ab0be412",null);a.default=_.exports}});