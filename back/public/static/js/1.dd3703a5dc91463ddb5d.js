webpackJsonp([1],{HR9D:function(t,s,a){"use strict";var e=a("+Ln8"),i={name:"msg",props:["icon","title","description","buttons"],methods:{onClick:function(t,s){"function"==typeof t&&t(),s&&Object(e.a)(s,this.$router)}}},o={render:function(){var t=this,s=t.$createElement,a=t._self._c||s;return a("div",{staticClass:"weui-msg"},[a("div",{staticClass:"weui-msg__icon-area"},[a("i",{staticClass:"weui-icon_msg",class:"weui-icon-"+(t.icon||"success")})]),t._v(" "),a("div",{staticClass:"weui-msg__text-area"},[a("h2",{staticClass:"weui-msg__title",domProps:{innerHTML:t._s(t.title)}}),t._v(" "),a("p",{staticClass:"weui-msg__desc"},[t._t("description")],2),t._v(" "),t.description?a("p",{staticClass:"weui-msg__desc",domProps:{innerHTML:t._s(t.description)}}):t._e()]),t._v(" "),a("div",{staticClass:"weui-msg__opr-area"},[a("p",{staticClass:"weui-btn-area"},[t._t("buttons",t._l(t.buttons,function(s){return a("a",{staticClass:"weui-btn",class:"weui-btn_"+s.type,attrs:{href:"javascript:;"},on:{click:function(a){t.onClick(s.onClick,s.link)}}},[t._v(t._s(s.text))])}))],2)])])},staticRenderFns:[]};var r=a("C7Lr")(i,o,!1,function(t){a("dwiu")},null,null);s.a=r.exports},QLfY:function(t,s){},R1c4:function(t,s){},VnbQ:function(t,s,a){var e={"./views/wallet_international/detail":"txX1"};function i(t){return a(o(t))}function o(t){var s=e[t];if(!(s+1))throw new Error("Cannot find module '"+t+"'.");return s}i.keys=function(){return Object.keys(e)},i.resolve=o,t.exports=i,i.id="VnbQ"},dwiu:function(t,s){},mlvW:function(t,s,a){"use strict";var e=a("IfgW"),i=(e.a,Number,Boolean,{name:"x-progress",mixins:[e.a],props:{percent:{type:Number,default:0},showCancel:{type:Boolean,default:!0}},methods:{cancel:function(){this.$emit("on-cancel")}}}),o={render:function(){var t=this.$createElement,s=this._self._c||t;return s("div",{staticClass:"weui-progress"},[s("div",{staticClass:"weui-progress__bar"},[s("div",{staticClass:"weui-progress__inner-bar",style:{width:this.percent+"%"}})]),this._v(" "),s("a",{directives:[{name:"show",rawName:"v-show",value:this.showCancel,expression:"showCancel"}],staticClass:"weui-progress_opr",attrs:{href:"javascript:;"}},[s("i",{staticClass:"weui-icon-cancel",on:{click:this.cancel}})])])},staticRenderFns:[]};var r=a("C7Lr")(i,o,!1,function(t){a("QLfY")},null,null);s.a=r.exports},txX1:function(t,s,a){"use strict";Object.defineProperty(s,"__esModule",{value:!0});var e=a("jH8X"),i=a("1a94"),o=a("ZXFd"),r=a("tXkb"),n=a("j996"),l=a("CltZ"),c=a("crx6"),_=a("5zIW"),u=a("oa6C"),p=a("HR9D"),d=a("mlvW"),m=a("YaEn"),v=(e.a,i.a,o.a,r.a,n.a,l.a,c.a,_.a,u.a,p.a,d.a,{directives:{TransferDom:e.a},components:{"x-header":i.a,actionsheet:o.a,cell:r.a,badge:n.a,box:l.a,"load-more":c.a,popup:_.a,divider:u.a,msg:p.a,"x-progress":d.a,nodata:function(){return a.e(94).then(a.bind(null,"QOpP"))}},data:function(){var t=0,s="ethereum";return m.a.currentRoute.params.coin&&(s=m.a.currentRoute.params.coin),this.$store.state.properties.length>0&&(t=this.$store.state.properties_set[s].amount+" "+this.$store.state.properties_set[s].name),{page_title:m.a.currentRoute.meta.title,page_name:m.a.currentRoute.name,coin_amount:t,tx_list:{},tx_listlenght:1,load_more_tip:"点击加载更多",page:0,list_end:!1,tx_detail:!1,tx_item:{},icon:"success",unconfirm_tx_list:[],unconfirm_tx_items:[],ethereum_confirm_count:12,base_coin:""}},mounted:function(){var t=this;this.$store.state.wallet.address?this.loadMore():this.$store.commit("loadWallets",function(){t.loadMore(),t.$store.commit("loadProperties",function(){var s="ethereum";m.a.currentRoute.params.coin&&(s=m.a.currentRoute.params.coin),t.coin_amount=t.$store.state.properties_set[s].amount+" "+t.$store.state.properties_set[s].name})}),window.js_qr_code_scan=function(s){t.js_qr_code_scan(s)}},methods:{goback:function(){this.$router.push("/wallet")},confirmTx:function(){var t=this;this.unconfirm_tx_list.length>0&&setTimeout(function(){t.$http.post("/api/app.wallet/transaction/confirm",{tx_hashes:t.unconfirm_tx_list}).then(function(s){for(var a=0;a<s.data.tx_list.length;a++)for(var e=s.data.tx_list[a].hash,i=0;i<t.unconfirm_tx_items.length;i++)t.unconfirm_tx_items[i].hash==e&&(t.unconfirm_tx_items[i].confirmations=s.data.tx_list[a].confirmations,t.unconfirm_tx_items[i].pending=s.data.tx_list[a].pending,t.unconfirm_tx_items[i].is_error=s.data.tx_list[a].is_error);s.data.confirmed_list.length==t.unconfirm_tx_list.length&&(t.unconfirm_tx_list=[],t.unconfirm_tx_items=[]),t.confirmTx()}).catch(function(s){t.$store.state.page_loading=!1})},5e3)},showTx:function(t){this.tx_detail=!0,this.tx_item=this.tx_list[t]},closeTxDetail:function(){this.tx_detail=!1},loadMore:function(){var t=this;if(!this.list_end){this.page++;var s="ethereum";m.a.currentRoute.params.coin&&(s=m.a.currentRoute.params.coin),this.$store.state.page_loading||(this.$store.state.page_loading=!0,this.$http.post("/api/app.wallet/transaction/txlistdb",{address:this.$store.state.wallet.address,page:this.page,coin:s}).then(function(s){if(t.$store.state.page_loading=!1,t.base_coin=s.data.base_coin,t.ethereum_confirm_count=s.data.ethereum_confirm_count,0==s.data.tx_list.length)t.list_end=!0,t.load_more_tip="到底了";else{s.data.tx_list.length<30&&(t.list_end=!0,t.load_more_tip="到底了");for(var a=0;a<s.data.tx_list.length;a++)t.tx_list[s.data.tx_list[a].hash]=s.data.tx_list[a],s.data.tx_list[a].confirmations<t.ethereum_confirm_count&&0==s.data.tx_list[a].is_error&&(t.unconfirm_tx_list.push(s.data.tx_list[a].hash),t.unconfirm_tx_items.push(s.data.tx_list[a]))}if(isNaN(t.tx_list)){var e=0;for(var i in t.tx_list)e++;t.tx_listlenght=e}else t.tx_listlenght=0;t.confirmTx()}).catch(function(s){t.$store.state.page_loading=!1}))}},open_scan:function(){App.qr_code_scan();var t=this;window.js_qr_code_scan=function(s){if("0x"==s.substr(0,2)){var a="ethereum";m.a.currentRoute.params.coin&&(a=m.a.currentRoute.params.coin);var e="/wallet_international/send/"+a+"?address="+s;t.$router.push({path:e})}else s=s.replace(t.$store.state.init.route_domain+"/#",""),t.$router.push({path:s})}},onCopy:function(t){this.$vux.toast.text("复制成功")},onError:function(t){this.$vux.toast.text("复制失败，您可以尝试手动记录")}}}),x={render:function(){var t=this,s=t.$createElement,a=t._self._c||s;return a("div",{attrs:{id:t.page_name}},[a("x-header",{staticClass:"inter_wallet_header",attrs:{"left-options":{showBack:!0,backText:"",preventGoBack:!0},"right-options":{showMore:!1}},on:{"on-click-back":t.goback}},[t._v(t._s(t.page_title)+"\n        "),a("i",{directives:[{name:"show",rawName:"v-show",value:t.$store.state.init.is_app,expression:"$store.state.init.is_app"}],staticClass:"iconfont",staticStyle:{fill:"#fff",position:"relative",top:"-2px","font-size":"1.4rem"},attrs:{slot:"right"},on:{click:function(s){t.open_scan()}},slot:"right"},[t._v("")])]),t._v(" "),a("div",{staticClass:"main_card"},[a("div",{staticClass:"wallet_amount"},[t._v(t._s(t.coin_amount))]),t._v(" "),a("div",{directives:[{name:"clipboard",rawName:"v-clipboard:copy",value:this.$store.state.wallet.address,expression:"this.$store.state.wallet.address",arg:"copy"},{name:"clipboard",rawName:"v-clipboard:success",value:t.onCopy,expression:"onCopy",arg:"success"},{name:"clipboard",rawName:"v-clipboard:error",value:t.onError,expression:"onError",arg:"error"}],staticClass:"wallet_address"},[t._v(t._s(t.shortStr(this.$store.state.wallet.address,12,20,"..."))+" "),a("i",{staticClass:"iconfont"},[t._v("")])])]),t._v(" "),a("group",{staticClass:"tx_list_block"},t._l(this.tx_list,function(s,e){return a("div",{staticClass:"tx_list",on:{click:function(a){t.showTx(s.hash)}}},[s.confirmations>=t.ethereum_confirm_count?a("div",{staticClass:"tx_list-con flex-box"},["shouru"==s.txType?a("div",{staticClass:"formatValue_add iconfont"},[t._v("")]):t._e(),t._v(" "),"zhichu"==s.txType?a("div",{staticClass:"iconfont formatValue_reduce"},[t._v("")]):t._e(),t._v(" "),a("cell",{staticClass:"tx_list-txt flex-1",attrs:{"is-link":""}},[a("span",{attrs:{slot:"title"},slot:"title"},[a("i",{class:["iconfont","icon-"+s.txType,"tx_icon"]}),t._v(" "),a("span",{staticClass:"address_hash"},[a("span",{staticClass:"tx_list_key"},[t._v(t._s(t.shortStr(s.hashShow,12,20,"...")))]),t._v(" "),a("span",{staticClass:"tx_list_time"},[t._v(t._s(s.datetime))])])]),t._v(" "),a("span",{staticClass:"tx_amount",attrs:{slot:"value"},slot:"value"},[t._v(t._s(s.formatValue))])])],1):a("div",{staticClass:"tx_list-con flex-box"},["shouru"==s.txType?a("div",{staticClass:"formatValue_add iconfont"},[t._v("")]):t._e(),t._v(" "),"zhichu"==s.txType?a("div",{staticClass:"iconfont formatValue_reduce"},[t._v("")]):t._e(),t._v(" "),a("cell",{staticClass:"tx_list-txt flex-1",attrs:{"is-link":""}},[a("span",{attrs:{slot:"title"},slot:"title"},[a("i",{class:["iconfont","icon-"+s.txType,"tx_icon"]}),t._v(" "),a("span",{staticClass:"address_hash"},[a("span",{staticClass:"tx_list_key"},[t._v(t._s(t.shortStr(s.hashShow,12,20,"...")))]),t._v(" "),s.confirmations>0?a("span",{staticClass:"tx_list_time"},[a("x-progress",{attrs:{percent:s.confirmations/t.ethereum_confirm_count*100,"show-cancel":!1}}),t._v("\n                                确认中："+t._s(s.confirmations)+"/"+t._s(t.ethereum_confirm_count)+"\n                            ")],1):a("span",{staticClass:"tx_list_time"},[a("x-progress",{attrs:{percent:s.confirmations/t.ethereum_confirm_count*100,"show-cancel":!1}}),t._v(" "),0==s.is_error?a("span",[t._v("等待打包")]):a("span",[t._v("流通失败")])],1)])]),t._v(" "),a("span",{staticClass:"tx_amount",attrs:{slot:"value"},slot:"value"},[t._v(t._s(s.formatValue))])])],1)])})),t._v(" "),t.tx_listlenght<1?a("nodata",{attrs:{datatip:"暂无数据"}}):t._e(),t._v(" "),t.tx_listlenght>0?a("box",{staticClass:"page"},[a("load-more",{directives:[{name:"show",rawName:"v-show",value:this.$store.state.page_loading,expression:"this.$store.state.page_loading"}],attrs:{tip:"正在加载"}}),t._v(" "),a("div",{on:{click:t.loadMore}},[a("load-more",{directives:[{name:"show",rawName:"v-show",value:!this.$store.state.page_loading,expression:"!this.$store.state.page_loading"}],attrs:{"show-loading":!1,tip:t.load_more_tip,"background-color":"#fbf9fe"}})],1)],1):t._e(),t._v(" "),a("popup",{directives:[{name:"transfer-dom",rawName:"v-transfer-dom"}],staticClass:"wallet-inter-pop",attrs:{height:"100%"},model:{value:t.tx_detail,callback:function(s){t.tx_detail=s},expression:"tx_detail"}},[a("div",{staticClass:"tx_detail"},[a("x-header",{attrs:{"left-options":{showBack:!1,backText:"",preventGoBack:!0},"right-options":{showMore:!1}},on:{"on-click-back":t.closeTxDetail}},[t._v("流通记录 "),a("i",{staticClass:"iconfont",staticStyle:{fill:"#fff",position:"relative",top:"-2px","font-size":"1.4rem"},attrs:{slot:"right"},on:{click:function(s){t.closeTxDetail()}},slot:"right"},[t._v("")])]),t._v(" "),a("div",{staticClass:"tx_detail-top"},[0==t.tx_item.is_error?a("div",{staticClass:"detail-top-icon iconfont"},[t._v("")]):a("div",{staticClass:"detail-top-icon iconfont",staticStyle:{"font-size":"3rem","text-align":"center","line-height":"3.65625rem",background:"#ff3300"}},[t._v("")]),t._v(" "),a("div",{staticClass:"detail_value"},[t._v(t._s(t.tx_item.value)+" "+t._s(t.tx_item.unit))]),t._v(" "),a("div",{directives:[{name:"clipboard",rawName:"v-clipboard:copy",value:t.tx_item.hash,expression:"tx_item.hash",arg:"copy"},{name:"clipboard",rawName:"v-clipboard:success",value:t.onCopy,expression:"onCopy",arg:"success"},{name:"clipboard",rawName:"v-clipboard:error",value:t.onError,expression:"onError",arg:"error"}],staticClass:"detail_txhash"},[t._v(t._s(t.shortStr(t.tx_item.hash,12,40,"..."))+"  "),a("i",{staticClass:"iconfont"},[t._v("")])]),t._v(" "),a("divider")],1),t._v(" "),a("div",{staticClass:"tx_detail-people"},[a("group",{staticClass:"party vux-1px-b",attrs:{title:"发款方"}},[a("cell",[a("span",{directives:[{name:"clipboard",rawName:"v-clipboard:copy",value:t.tx_item.from,expression:"tx_item.from",arg:"copy"},{name:"clipboard",rawName:"v-clipboard:success",value:t.onCopy,expression:"onCopy",arg:"success"},{name:"clipboard",rawName:"v-clipboard:error",value:t.onError,expression:"onError",arg:"error"}],staticClass:"wallet_address",attrs:{slot:"title"},slot:"title"},[t._v(t._s(t.shortStr(t.tx_item.from,12,20,"..."))+" "),a("i",{staticClass:"iconfont"},[t._v("")])])])],1),t._v(" "),t.tx_item.to?a("group",{staticClass:"party vux-1px-b",attrs:{title:"转入方"}},[a("cell",[a("span",{directives:[{name:"clipboard",rawName:"v-clipboard:copy",value:t.tx_item.to,expression:"tx_item.to",arg:"copy"},{name:"clipboard",rawName:"v-clipboard:success",value:t.onCopy,expression:"onCopy",arg:"success"},{name:"clipboard",rawName:"v-clipboard:error",value:t.onError,expression:"onError",arg:"error"}],staticClass:"wallet_address",attrs:{slot:"title"},slot:"title"},[t._v(t._s(t.shortStr(t.tx_item.to,12,20,"..."))+" "),a("i",{staticClass:"iconfont"},[t._v("")])])])],1):t._e(),t._v(" "),t.tx_item.contractAddress&&!t.tx_item.to?a("group",{staticClass:"party",attrs:{title:"合约发布"}},[a("cell",[a("span",{staticClass:"wallet_address",attrs:{slot:"title"},slot:"title"},[t._v(t._s(t.tx_item.contractAddress))])])],1):t._e()],1),t._v(" "),a("group",{staticClass:"tx_detail_block"},[a("cell",[a("span",{staticClass:"tx_detail_title",attrs:{slot:"title"},slot:"title"},[t._v("矿工费用")]),t._v(" "),a("span",{staticClass:"tx_detail_value"},[t._v(t._s(t.tx_item.gasFee)+" "+t._s(t.base_coin))])]),t._v(" "),a("cell",[a("span",{staticClass:"tx_detail_title",attrs:{slot:"title"},slot:"title"},[t._v("区块编号")]),t._v(" "),a("span",{staticClass:"tx_detail_value"},[t._v(t._s(t.tx_item.blockNumber)+" ")])]),t._v(" "),a("cell",[a("span",{staticClass:"tx_detail_title",attrs:{slot:"title"},slot:"title"},[t._v("流通时间")]),t._v(" "),a("span",{staticClass:"tx_detail_value"},[t._v(t._s(t.tx_item.datetime)+" ")])]),t._v(" "),t.tx_item.confirmations>0?a("cell",[a("span",{staticClass:"tx_detail_title",attrs:{slot:"title"},slot:"title"},[t._v("流通确认")]),t._v(" "),a("span",{staticClass:"tx_detail_value"},[t._v(t._s(t.tx_item.confirmations>=t.ethereum_confirm_count?"已确认":t.tx_item.confirmations+"/"+t.ethereum_confirm_count)+" ")])]):a("cell",[a("span",{staticClass:"tx_detail_title",attrs:{slot:"title"},slot:"title"},[t._v("流通确认")]),t._v(" "),0==t.tx_item.is_error?a("span",{staticClass:"tx_detail_value"},[t._v("等待打包")]):a("span",{staticClass:"tx_detail_value"},[t._v("流通失败")])])],1)],1)])],1)},staticRenderFns:[]};var h=a("C7Lr")(v,x,!1,function(t){a("R1c4")},"data-v-ba069f58",null);s.default=h.exports}});