webpackJsonp([60],{"6khp":function(t,o,e){"use strict";Object.defineProperty(o,"__esModule",{value:!0});var n=e("3cXf"),i=e.n(n),s=e("xSLg"),a=e("PcJl"),c=e("oa6C"),l=e("crx6"),r=e("QOpP"),d=(a.a,c.a,l.a,r.default,{components:{Scroller:a.a,Divider:c.a,LoadMore:l.a,Nodata:r.default},data:function(){return{money_lists:[],formData:{page:1},lock:!1,loading:0}},mounted:function(){this.loadMyMoney()},methods:{loadMyMoney:function(){var t=this;if(this.lock)return!1;this.lock=!0,this.loading=1,this.$http.post("/api/app.user/account/asset",this.formData).then(function(o){o.data.asset.length>0&&(t.money_lists=t.money_lists.concat_unk(o.data.asset,"id"),t.lock=!1),t.loading=0,t.formData.page++}).catch(function(o){t.loading=0})},onScrollBottom:function(){this.lock||this.loadMyMoney()},goPage:function(t){var o=this;Object(s.d)("setCoinInfo",i()(t)),this.$store.dispatch("setCoinInfo",t).then(function(t){o.$router.push({name:"userSelfbcty"})})}}}),f={render:function(){var t=this,o=t.$createElement,e=t._self._c||o;return e("scroller",{ref:"scrollerEvent",attrs:{height:"100%","lock-x":""},on:{"on-scroll-bottom":t.onScrollBottom}},[e("div",{staticClass:"page-slefmoney"},[t.money_lists.length>0?e("div",{staticClass:"task-block"},[t._l(t.money_lists,function(o){return e("div",{staticClass:"item flex-box",on:{click:function(e){t.goPage(o)}}},[e("div",{staticClass:"item-img"},[e("img",{attrs:{src:o.coin_type.icon,alt:""}})]),t._v(" "),e("div",{staticClass:"item-info flex-1"},[e("div",{staticClass:"item-title flex-box"},[e("div",{staticClass:"title-text flex-1"},[t._v(t._s(o.coin_type.coin_unit))]),t._v(" "),e("div",{staticClass:"item-money flex-box"},[e("div",{staticClass:"item-type"},[t._v("\r\n                            "+t._s(o.vc_amount)+"\r\n                        ")]),t._v(" "),o.coin_type.price>0?e("div",{staticClass:"item-decs flex-box"},[e("div",{staticClass:"item-decs-list"},[t._v("≈  ¥ "+t._s((o.vc_amount*o.coin_type.price).toFixed(2)))])]):t._e()])])])])}),t._v(" "),t.loading?e("load-more",{attrs:{tip:"正在加载 . . ."}}):e("load-more",{attrs:{"show-loading":!1,tip:"没有更多了","background-color":"#fbf9fe"}})],2):e("nodata",{attrs:{datatip:"暂无数据"}})],1)])},staticRenderFns:[]};var u=e("C7Lr")(d,f,!1,function(t){e("BGI7")},null,null);o.default=u.exports},BGI7:function(t,o){},"F/XD":function(t,o,e){var n={"./views/user/selfmoney":"6khp"};function i(t){return e(s(t))}function s(t){var o=n[t];if(!(o+1))throw new Error("Cannot find module '"+t+"'.");return o}i.keys=function(){return Object.keys(n)},i.resolve=s,t.exports=i,i.id="F/XD"}});