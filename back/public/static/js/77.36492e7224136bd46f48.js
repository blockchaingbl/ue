webpackJsonp([77],{DPs2:function(t,s){},O9TB:function(t,s,a){"use strict";Object.defineProperty(s,"__esModule",{value:!0});var e=a("ZLEe"),i=a.n(e),c={directives:{},components:{},data:function(){return{sugar:{},coin_unit:"",one_more_link:""}},created:function(){0==i()(this.$route.params).length&&this.$router.push({name:"userCenter"}),this.sugar=this.$route.params.sugar_info,this.coin_unit=this.$route.params.coin_unit,this.time_type=this.$route.params.time_type,this.one_more_link="/lock_transfer/grant/"+this.sugar.coin_type},methods:{}},n={render:function(){var t=this,s=t.$createElement,a=t._self._c||s;return a("div",{staticClass:"page-candy-success"},[a("x-header",{attrs:{"left-options":{showBack:!1,backText:""}}},[a("span",[t._v("转出成功")])]),t._v(" "),a("div",{staticClass:"candy-succ-con"},[t._m(0),t._v(" "),a("group",{staticClass:"candy_success_block"},[a("cell",[a("span",{staticClass:"candy_success_title",attrs:{slot:"title"},slot:"title"},[t._v("资产类型")]),t._v(" "),a("span",{staticClass:"candy_success_value"},[t._v(t._s(t.coin_unit))])]),t._v(" "),a("cell",[a("span",{staticClass:"candy_success_title",attrs:{slot:"title"},slot:"title"},[t._v("转出数额")]),t._v(" "),a("span",{staticClass:"candy_success_value"},[t._v(t._s(t.sugar.amount))])]),t._v(" "),t.sugar.lock_time?a("cell",[a("span",{staticClass:"candy_success_title",attrs:{slot:"title"},slot:"title"},[t._v("锁仓时间")]),t._v(" "),a("span",{staticClass:"candy_success_value"},[t._v(t._s(t.sugar.lock_time)+t._s(t.time_type))])]):t._e(),t._v(" "),a("cell",[a("span",{staticClass:"candy_success_title",attrs:{slot:"title"},slot:"title"},[t._v("转入人")]),t._v(" "),a("span",{staticClass:"candy_success_value"},[t._v(t._s(t.sugar.to_user_name))])])],1)],1),t._v(" "),a("box",{staticClass:"candy-succ-btn flex-box",staticStyle:{"text-align":"center"},attrs:{gap:"0 0"}},[a("x-button",{staticClass:"withdraw-btn",staticStyle:{"border-radius":"99px"},attrs:{type:"primary",link:t.one_more_link}},[t._v("再转一笔")]),t._v(" "),a("x-button",{staticClass:"withdraw-btn",staticStyle:{"border-radius":"99px"},attrs:{type:"primary",link:"/mine/center"}},[t._v("返回首页")])],1)],1)},staticRenderFns:[function(){var t=this.$createElement,s=this._self._c||t;return s("div",{staticClass:"success-head"},[s("div",{staticClass:"head-icon iconfont"},[this._v("")]),this._v(" "),s("div",{staticClass:"head-title"},[this._v("转出成功")])])}]};var l=a("C7Lr")(c,n,!1,function(t){a("DPs2")},null,null);s.default=l.exports}});