webpackJsonp([54],{"338C":function(t,a,e){"use strict";Object.defineProperty(a,"__esModule",{value:!0});var s=e("crx6"),r=e("oa6C"),i=e("mAm1"),n=e("NyFS"),o=e("ocpe"),l=e("PcJl"),c=e("jH8X"),m=(c.a,i.a,n.a,s.a,r.a,l.a,o.a,{directives:{TransferDom:c.a},components:{Flexbox:i.a,FlexboxItem:n.a,LoadMore:s.a,Divider:r.a,Scroller:l.a,Loading:o.a},data:function(){return{formData:{page:1},market_lists:[],lock:!1,loadings:!0}},mounted:function(){this.getMarket()},methods:{getMarket:function(){var t=this;if(this.lock)return!1;this.lock=!0,this.loading=1,this.$http.post("api/app.market/market",{}).then(function(a){a.data.market_lists.length>0&&(t.market_lists=t.market_lists.concat_unk(a.data.market_lists,"id"),t.lock=!1,t.loadings=!1),t.loading=0,t.formData.page++}).catch(function(a){t.loading=0})},onScrollBottom:function(){this.lock||this.getMarket()},turnDetail:function(t){this.$router.push({name:"marketDetail",params:{market:t}})}}}),_={render:function(){var t=this,a=t.$createElement,e=t._self._c||a;return e("scroller",{ref:"scrollerEvent",staticStyle:{background:"#2e3d6c"},attrs:{height:"100%","lock-x":""},on:{"on-scroll-bottom":t.onScrollBottom}},[e("div",{staticClass:"market-index"},[e("flexbox",{staticClass:"main_head vux-1px-b",attrs:{gutter:0}},[e("flexbox-item",{attrs:{span:4}},[e("div",{staticClass:"market_box"},[t._v("资产名称")])]),t._v(" "),e("flexbox-item",{staticClass:"t_r",attrs:{span:4}},[e("div",{staticClass:"market_box"},[t._v("最新价")])]),t._v(" "),e("flexbox-item",{staticClass:"t_r",attrs:{span:4}},[e("div",{staticClass:"market_box"},[t._v("涨跌幅")])])],1),t._v(" "),e("div",t._l(t.market_lists,function(a,s){return t.market_lists.length>0?e("flexbox",{key:s,staticClass:"main_item vux-1px-b",attrs:{gutter:0},nativeOn:{click:function(e){t.turnDetail(a)}}},[e("flexbox-item",{attrs:{span:4}},[e("div",{staticClass:"market_box"},[t._v(t._s(a.name))]),t._v(" "),e("div",{staticClass:"market_box color_white"},[t._v(t._s(a.coin_unit))])]),t._v(" "),e("flexbox-item",{staticClass:"t_r",attrs:{span:4}},[e("div",{staticClass:"market_box"},[t._v("￥"+t._s(a.price))]),t._v(" "),e("div",{staticClass:"market_box color_white"},[t._v("\r\n                    $"+t._s(a.price_usd)+"\r\n                ")])]),t._v(" "),e("flexbox-item",{staticClass:"t_r",attrs:{span:4}},[e("div",{staticClass:"market_box"},[0==a.trend?e("x-button",{staticStyle:{background:"#d3483e",color:"#fff",width:"4.875rem",padding:"0"},attrs:{type:"warn",mini:!0}},[t._v("-"+t._s(a.rate_abs)+"%")]):e("x-button",{staticStyle:{background:"#79d576",color:"#fff",width:"4.875rem",padding:"0"},attrs:{mini:!0}},[t._v("+"+t._s(a.rate_abs)+"%")])],1)])],1):t._e()})),t._v(" "),e("div",{directives:[{name:"transfer-dom",rawName:"v-transfer-dom"}]},[e("loading",{attrs:{show:t.loadings}})],1)],1)])},staticRenderFns:[]};var d=e("C7Lr")(m,_,!1,function(t){e("MmsH")},null,null);a.default=d.exports},MmsH:function(t,a){},xL0b:function(t,a,e){var s={"./views/market/index":"338C"};function r(t){return e(i(t))}function i(t){var a=s[t];if(!(a+1))throw new Error("Cannot find module '"+t+"'.");return a}r.keys=function(){return Object.keys(s)},r.resolve=i,t.exports=r,r.id="xL0b"}});