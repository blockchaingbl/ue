webpackJsonp([75],{"3pQd":function(t,i,e){"use strict";Object.defineProperty(i,"__esModule",{value:!0});var s=e("YaEn"),n={components:{},data:function(){return{id:"",info:[],list:[],un_mined:0}},created:function(){this.id=s.a.currentRoute.query.id,this.getUserMiner(),this.getMinerMined()},mounted:function(){},methods:{fetch:function(){var t=this;this.$http.post("/api/app.miner/miner/fetch",{user_miner_id:this.id}).then(function(i){t.$vux.toast.text("领取成功"),t.getMinerMined()}).catch(function(i){i.errcode&&t.$vux.toast.text(i.message)})},getUserMiner:function(){var t=this;this.$http.post("/api/app.miner/miner/user_miner_detail",{user_miner_id:this.id}).then(function(i){t.info=i.data.info}).catch(function(i){i.errcode&&t.$vux.toast.text(i.message)})},getMinerMined:function(){var t=this;this.$http.post("/api/app.miner/miner/mined",{user_miner_id:this.id}).then(function(i){t.list=i.data.log,t.un_mined=i.data.un_mined}).catch(function(i){i.errcode&&t.$vux.toast.text(i.message)})}}},a={render:function(){var t=this,i=t.$createElement,e=t._self._c||i;return e("div",{staticClass:"page-self-detail"},[e("x-header",{staticStyle:{"z-index":"1"},attrs:{"left-options":{showBack:!0,backText:""}}},[t._v(t._s(t.info.name))]),t._v(" "),e("div",{staticClass:"content"},[e("div",{staticClass:"banner"},[e("div",{staticClass:"banner-title"},[t._v("累计收益")]),t._v(" "),e("div",{staticClass:"banner-profit"},[t._v(t._s(t.info.income)+" "+t._s(t.info.coin_unit))]),t._v(" "),e("div",{staticClass:"banner-footer flex-box"},[e("div",{staticClass:"banner-item"},[e("div",{staticClass:"item-title"},[t._v("剩余期限（天）")]),t._v(" "),e("div",{staticClass:"item-numb"},[t._v(t._s(t.info.expire_day))])]),t._v(" "),e("div",{staticClass:"banner-item flex-1"},[e("div",{staticClass:"item-title"},[t._v("算力")]),t._v(" "),e("div",{staticClass:"item-numb"},[t._v(t._s(t.info.cp_total))])]),t._v(" "),e("div",{staticClass:"banner-item flex-2"},[e("div",{staticClass:"item-title"},[t._v("每日产量")]),t._v(" "),e("div",{staticClass:"item-numb"},[t._v(t._s(t.info.low)+"-"+t._s(t.info.high)+"/"+t._s(t.info.coin_unit))])]),t._v(" "),e("div",{staticClass:"banner-item"},[e("div",{staticClass:"item-title"},[t._v("到期时间")]),t._v(" "),e("div",{staticClass:"item-numb"},[t._v(t._s(t.info.expire_date))])])])]),t._v(" "),e("div",{staticClass:"detail-con"},[e("div",{staticClass:"detail-title vux-1px-b"},[t._v("历史收益")]),t._v(" "),e("div",{staticClass:"profit-block"},t._l(t.list,function(i,s){return e("div",{key:s,staticClass:"profit-item vux-1px-b"},[e("div",{staticClass:"profit-item-text flex-box"},[e("div",{staticClass:"text-title flex-1"},[t._v("收益")]),t._v(" "),e("div",{staticClass:"profit-numb"},[t._v("+"+t._s(i.amount)+" "+t._s(i.coin_type))])]),t._v(" "),e("div",{staticClass:"profit-item-time"},[t._v(t._s(i.create_time))])])}))])]),t._v(" "),e("box",{staticClass:"buy-btn",attrs:{gap:"0 0"}})],1)},staticRenderFns:[]};var c=e("C7Lr")(n,a,!1,function(t){e("meyG")},null,null);i.default=c.exports},meyG:function(t,i){}});