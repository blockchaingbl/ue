webpackJsonp([87],{EtIf:function(t,a,i){"use strict";Object.defineProperty(a,"__esModule",{value:!0});var s=i("SPhl"),e=i("sNOJ"),o=i("ocpe"),r=i("jH8X"),n=(i("EOtl"),i("8Dyd")),l=i("QOpP"),d=(r.a,s.a,e.a,n.a,l.default,o.a,{directives:{TransferDom:r.a},components:{Tab:s.a,TabItem:e.a,Scroller:n.a,Nodata:l.default,Loading:o.a},data:function(){return{formData:{page:1,type:2},orders:[],total:0,loading:!1,first_loading:!0}},mounted:function(){this.loadList()},methods:{loadList:function(){var t=this;this.loading||(null!=this.formData.page?(this.loading=!0,this.$http.post("/api/app.user/transfer/orders",this.formData).then(function(a){t.total=a.data.total,t.first_loading=!1,null==a.data?(t.formData.page=null,t.loading=!1):a.data.orders.length<1?(t.formData.page=null,t.loading=!1):(t.orders=t.orders.concat_unk(a.data.orders,"id"),t.formData.page++,t.loading=!1)}).catch(function(a){t.first_loading=!1,t.formData.page=null,t.loading=!1})):this.loading=!1)}}}),c={render:function(){var t=this,a=t.$createElement,i=t._self._c||a;return i("div",{staticClass:"page-candy-distribute"},[i("div",{staticClass:"distri-content"},[i("div",{staticClass:"distri-title"},[t._v("历史转出记录（"+t._s(t.total)+"）")]),t._v(" "),i("div",{directives:[{name:"show",rawName:"v-show",value:!t.first_loading,expression:"!first_loading"}],staticClass:"distri-con"},[i("div",{staticClass:"distri-block"},t._l(t.orders,function(a,s){return i("a",{key:s,staticClass:"item",attrs:{href:"#/candy/distribute_detail"},on:{click:function(a){a.preventDefault(),t.turnDetail(t.sugar)}}},[i("div",{staticClass:"item-item flex-box"},[i("div",{staticClass:"item-time flex-1"},[t._v(t._s(a.create_time))]),t._v(" "),i("div",{staticClass:"item-text"},[t._v("接收人："+t._s(a.to_user.username))])]),t._v(" "),i("div",{staticClass:"item-item flex-box"},[i("div",{staticClass:"item-text flex-1"},[t._v("转出数额 "+t._s(a.amount)+" "+t._s(t.$store.state.init.coin_uint))]),t._v(" "),i("div",{staticClass:"item-text"},[t._v("手机：**********"+t._s(a.to_user.mobile.substr(-1,1)))])])])})),t._v(" "),t.orders.length>0?i("Scroller",{attrs:{loading:t.loading,container:".distri-block"},on:{load:t.loadList}}):i("nodata",{attrs:{datatip:"暂无数据"}})],1)]),t._v(" "),i("div",{directives:[{name:"transfer-dom",rawName:"v-transfer-dom"}]},[i("loading",{attrs:{show:t.first_loading}})],1)])},staticRenderFns:[]};var u=i("C7Lr")(d,c,!1,function(t){i("tuKE")},null,null);a.default=u.exports},tuKE:function(t,a){}});