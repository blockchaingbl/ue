webpackJsonp([91],{B7m5:function(t,a,s){"use strict";Object.defineProperty(a,"__esModule",{value:!0});var i=s("8Dyd"),e=s("QOpP"),l=s("ocpe"),d=s("jH8X"),n=(d.a,i.a,e.default,l.a,{directives:{TransferDom:d.a},components:{Scroller:i.a,Nodata:e.default,Loading:l.a},data:function(){return{sugar:{},formData:{page:1},sugars_detail:[],loading:!1,total:0,first_loading:!0}},created:function(){this.sugar=this.$route.params,this.formData.sugar_id=this.sugar.id},mounted:function(){this.loadDetail()},methods:{loadDetail:function(){var t=this;this.loading||(null!=this.formData.page?(this.loading=!0,this.$http.post("api/app.user/sugar/grant_detail",this.formData).then(function(a){t.first_loading=!1,null==a.data?(t.formData.page=null,t.loading=!1):a.data.detail.length<1?(t.formData.page=null,t.loading=!1):(t.total=a.data.total,t.sugars_detail=t.sugars_detail.concat_unk(a.data.detail,"id"),t.formData.page++,t.loading=!1)}).catch(function(a){t.first_loading=!1,t.formData.page=null,t.loading=!1})):this.loading=!1)}}}),o={render:function(){var t=this,a=t.$createElement,s=t._self._c||a;return s("div",{staticClass:"page-distribute-detail"},[s("x-header",{attrs:{"left-options":{showBack:!0,backText:""}}},[t._v(t._s(this.$route.meta.title))]),t._v(" "),s("div",{staticClass:"detail-head"},[s("div",{staticClass:"condy-time"},[t._v("发糖时间："+t._s(t.sugar.create_time))]),t._v(" "),s("div",{staticClass:"condy-time"},[t._v("领糖截止："+t._s(t.sugar.receive_end_time))]),t._v(" "),s("div",{staticClass:"candy-info"},[s("div",{staticClass:"info-item flex-box"},[s("div",{staticClass:"item-text flex-1"},[t._v("资产("+t._s(t.sugar.coin_info.coin_unit)+")："+t._s(t.sugar.amount))]),t._v(" "),s("div",{staticClass:"item-text"},[t._v("已释放："+t._s(t.sugar.free_amount))])]),t._v(" "),s("div",{staticClass:"info-item flex-box"},[s("div",{staticClass:"item-text flex-1"},[t._v("糖果(份)："+t._s(t.sugar.copys))]),t._v(" "),s("div",{staticClass:"item-text"},[t._v("被领取(份)："+t._s(t.sugar.copys-t.sugar.copys_less))])]),t._v(" "),s("div",{staticClass:"info-item flex-box"},[s("div",{staticClass:"item-text flex-1"},[t._v("锁定："+t._s(t.sugar.lock_day)+"天")]),t._v(" "),s("div",{staticClass:"item-text"},[t._v("总释放："+t._s(t.sugar.amount))])])])]),t._v(" "),s("div",{directives:[{name:"show",rawName:"v-show",value:!t.first_loading,expression:"!first_loading"}],staticClass:"detail-history"},[s("div",{staticClass:"history-title"},[t._v("领糖果记录（"+t._s(t.sugar.copys-t.sugar.copys_less)+"）")]),t._v(" "),s("div",{staticClass:"his-table"},[t._m(0),t._v(" "),s("div",{staticClass:"his-tab-con"},t._l(t.sugars_detail,function(a){return s("div",{staticClass:"table-item flex-box vux-1px-b"},[s("div",{staticClass:"item-item his-time"},[t._v(t._s(a.create_time))]),t._v(" "),s("div",{staticClass:"item-item his-name"},[t._v(t._s(a.to_username))]),t._v(" "),s("div",{staticClass:"item-item his-numb"},[t._v(t._s(a.sugar_amount))]),t._v(" "),a.free?s("div",{staticClass:"item-item his-distr"},[t._v("是")]):s("div",{staticClass:"item-item his-distr"},[t._v("否")])])}))]),t._v(" "),t.sugars_detail.length>0?s("Scroller",{attrs:{loading:t.loading,container:".his-tab-con"},on:{load:t.loadDetail}}):s("nodata",{attrs:{datatip:"暂无数据"}})],1),t._v(" "),s("div",{directives:[{name:"transfer-dom",rawName:"v-transfer-dom"}]},[s("loading",{attrs:{show:t.first_loading}})],1)],1)},staticRenderFns:[function(){var t=this.$createElement,a=this._self._c||t;return a("div",{staticClass:"his-table-head flex-box"},[a("div",{staticClass:"table-head-item his-time"},[this._v("领取时间")]),this._v(" "),a("div",{staticClass:"table-head-item his-name"},[this._v("领取人")]),this._v(" "),a("div",{staticClass:"table-head-item his-numb"},[this._v("领取数量")]),this._v(" "),a("div",{staticClass:"table-head-item his-distr"},[this._v("是否释放")])])}]};var r=s("C7Lr")(n,o,!1,function(t){s("dveV")},null,null);a.default=r.exports},dveV:function(t,a){}});