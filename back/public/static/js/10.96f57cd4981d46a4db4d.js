webpackJsonp([10],{"4fan":function(t,e){t.exports="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAZCAYAAAAmNZ4aAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NTBCNkU1Njg3QTlFMTFFOEE0OTM5ODQyNTIwM0ZDQzYiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6NTBCNkU1Njk3QTlFMTFFOEE0OTM5ODQyNTIwM0ZDQzYiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo1MEI2RTU2NjdBOUUxMUU4QTQ5Mzk4NDI1MjAzRkNDNiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo1MEI2RTU2NzdBOUUxMUU4QTQ5Mzk4NDI1MjAzRkNDNiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PqfgmikAAABmSURBVHjaYowo/P+fgU5geR8DI4zNxDBAYMAsZsEVFNQCkUUM/wlaDFQ0/IN6NHGRnVhITWgjPKgpKQJHg5qmQU1JCh95Qc2I1hBgpIEd/0cbAkOrkiAjyxG2mJJ8ORrHhABAgAEAFcwaYVNdNEsAAAAASUVORK5CYII="},"62p5":function(t,e,a){var s={"./views/wallet_international/index":"d2lk"};function i(t){return a(o(t))}function o(t){var e=s[t];if(!(e+1))throw new Error("Cannot find module '"+t+"'.");return e}i.keys=function(){return Object.keys(s)},i.resolve=o,t.exports=i,i.id="62p5"},"6VMa":function(t,e,a){"use strict";var s={render:function(){var t=this.$createElement;return(this._self._c||t)("i",{staticClass:"weui-loading"})},staticRenderFns:[]};var i=a("C7Lr")({name:"inline-loading"},s,!1,function(t){a("AHLr")},null,null);e.a=i.exports},AHLr:function(t,e){},NQkE:function(t,e){},d2lk:function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var s=a("jH8X"),i=a("1a94"),o=a("ZXFd"),r=a("tXkb"),n=a("j996"),l=a("crx6"),c=a("5zIW"),d=a("EOtl"),h=a("eT+W"),u=a("CltZ"),p=a("PcJl"),v=a("oa6C"),f=a("6VMa"),m=a("hlrq"),w=a("s5gs"),k=a("KDzu"),_=a("DEX7"),g=a("YaEn"),x=a("dcCu"),b=a("QOpP"),y=(s.a,i.a,o.a,r.a,n.a,l.a,c.a,d.a,h.a,u.a,x.a,k.a,p.a,v.a,l.a,b.default,f.a,m.a,w.a,_.a,{directives:{TransferDom:s.a},components:{"x-header":i.a,actionsheet:o.a,cell:r.a,badge:n.a,"load-more":l.a,popup:c.a,radio:d.a,"x-button":h.a,box:u.a,"pull-to":x.a,qrcode:k.a,Scroller:p.a,Divider:v.a,LoadMore:l.a,Nodata:b.default,InlineLoading:f.a,InlineXSwitch:m.a,Search:w.a,"x-switch":_.a},data:function(){return{page_title:g.a.currentRoute.meta.title,page_name:g.a.currentRoute.name,iconfont:"iconfont",showRightBar:!1,wallets:[],wallet_address:"",showTokenListPage:!1,token_lists:[],formData:{page:1},lock:!1,loading:0,keyword:"",isShowSearch:!1,qrcodeshow:!1,ontop:!0,displayType:0}},mounted:function(){var t=this;this.$store.commit("loadWallets",function(){t.refresh_wallet()})},methods:{goback:function(){this.$router.push("/user/center")},showQrcode:function(){this.qrcodeshow=!0},refresh_wallet:function(){if(this.$store.state.wallets){for(var t in this.$store.state.wallets){var e={key:t,value:this.$store.state.wallets[t].name};this.wallets.push(e)}this.wallet_address=this.$store.state.wallet.address}else g.a.replace("wallet/create")},walletItemClass:function(t){return this.$store.state.wallet.address==t.address?"current":""},showMenus:function(){this.showRightBar=!0},create_wallet:function(){g.a.push("wallet_international/create")},import_wallet:function(){g.a.push("wallet_international/import")},manage_wallet:function(){g.a.push("wallet_international/manage")},refresh:function(t){this.$store.commit("loadProperties",function(){t("done")})},onCopy:function(t){this.$vux.toast.text("复制成功")},onError:function(t){this.$vux.toast.text("复制失败，您可以尝试手动记录")},showTokenList:function(){this.showTokenListPage=!0,this.loadTokenList()},hideTokenList:function(){this.showTokenListPage=!1,this.clearListData(),this.$store.commit("loadProperties")},loadTokenList:function(t){var e=this;if(this.lock)return!1;this.lock=!0,this.loading=1,this.formData.keyword=t,this.formData.address=this.$store.state.wallet.address,this.$http.post("/api/app.wallet/token",this.formData).then(function(t){t.data.tokens.length>0&&(e.token_lists=e.token_lists.concat_unk(t.data.tokens,"id"),e.lock=!1),e.loading=0,e.formData.page++}).catch(function(t){e.loading=0})},onScrollBottom:function(){this.lock||(this.isShowSearch?this.searchTokenList():this.loadTokenList())},subscribeSet:function(t){var e=this,a=this;this.$http.post("/api/app.wallet/token/subscribe",{token_id:t,address:this.$store.state.wallet.address}).then(function(t){"0"==t.errcode&&a.isShowSearch&&(a.clearListData(),a.searchTokenList())}).catch(function(t){err.errcode&&e.$vux.toast.text(err.message)})},showSearch:function(){this.$refs.search_token.setFocus(),this.isShowSearch=!0,this.clearListData()},search:function(){this.clearListData(),this.searchTokenList()},cancel:function(){this.isShowSearch=!1,this.clearListData(),this.loadTokenList()},searchTokenList:function(){var t=this;if(this.lock)return!1;this.lock=!0,this.loading=1,this.$http.post("/api/app.wallet/token/search",{keyword:this.keyword,address:this.$store.state.wallet.address}).then(function(e){e.data.tokens.length>0&&(t.token_lists=t.token_lists.concat_unk(e.data.tokens,"id"),t.lock=!1),t.loading=0,t.formData.page++}).catch(function(e){t.loading=0})},clearListData:function(){this.token_lists=[],this.lock=!1,this.formData.page=1}},watch:{wallet_address:{handler:function(t,e){!e&&this.$store.state.properties.length>0||(this.$store.commit("changeWallet",t),this.$store.commit("loadProperties"),this.showRightBar=!1)}},$route:function(){this.$store.commit("loadWallets",function(){_this.refresh_wallet()}),this.$store.commit("loadProperties")}}}),C={render:function(){var t=this,e=t.$createElement,s=t._self._c||e;return s("div",{attrs:{id:t.page_name}},[s("pull-to",{staticStyle:{"z-index":"0"},attrs:{"top-load-method":t.refresh,"is-bottom-bounce":!1,"is-top-bounce":t.ontop}},[s("x-header",{staticClass:"inter_wallet_header",staticStyle:{"z-index":"1"},attrs:{"left-options":{showBack:!0,backText:"",preventGoBack:!0},"right-options":{showMore:!1}},on:{"on-click-back":t.goback}},[t._v(t._s(t.page_title)+"\n            "),s("i",{staticClass:"iconfont",staticStyle:{fill:"#fff",position:"relative",top:"-2px","font-size":"0.875rem"},attrs:{slot:"right"},on:{click:function(e){t.showMenus()}},slot:"right"},[t._v("")])]),t._v(" "),s("div",{staticClass:"wallet-card-box"},[s("div",{staticClass:"wallet-card"},[s("div",{staticClass:"wallet-info flex-box"},[s("div",{staticClass:"wallet-img"},[s("img",{attrs:{src:a("SA/o"),alt:""}})]),t._v(" "),s("div",{staticClass:"wallet-info-text flex-1"},[s("div",{staticClass:"wall-name",on:{click:function(e){t.showQrcode()}}},[t._v(t._s(this.$store.state.wallet.name)+"  "),s("i",{staticClass:"iconfont"},[t._v("")])]),t._v(" "),s("div",{directives:[{name:"clipboard",rawName:"v-clipboard:copy",value:this.$store.state.wallet.address,expression:"this.$store.state.wallet.address",arg:"copy"},{name:"clipboard",rawName:"v-clipboard:success",value:t.onCopy,expression:"onCopy",arg:"success"},{name:"clipboard",rawName:"v-clipboard:error",value:t.onError,expression:"onError",arg:"error"}],staticClass:"wallet-address"},[t._v(t._s(t.shortStr(this.$store.state.wallet.address,12,20,"..."))+" "),s("i",{staticClass:"iconfont"},[t._v("")])])])]),t._v(" "),s("div",{directives:[{name:"show",rawName:"v-show",value:0==t.$store.state.balance_loading,expression:"$store.state.balance_loading==false"}],staticClass:"wallet-assets"},[s("em",[t._v("¥")]),t._v(" "+t._s(t.$store.state.properties_amount.toFixed(2)))])])]),t._v(" "),s("div",{staticClass:"total-assets flex-box"},[s("div",{staticClass:"total-assets-txt flex-1"},[t._v("资产")]),t._v(" "),s("div",{staticClass:"iconfont token_add",on:{click:function(e){t.showTokenList()}}},[t._v("")])]),t._v(" "),s("div",{directives:[{name:"show",rawName:"v-show",value:!this.$store.state.balance_loading,expression:"!this.$store.state.balance_loading"}],staticClass:"wallet-box"},t._l(this.$store.state.properties,function(e,a){return s("router-link",{key:a,staticClass:"item flex-box vux-1px-b",attrs:{to:"/wallet_international/detail/"+e.type}},[s("div",{staticClass:"item-img"},[s("img",{attrs:{src:e.icon,alt:""}})]),t._v(" "),s("div",{staticClass:"item-info flex-1"},[s("div",{staticClass:"item-title flex-box"},[s("div",{staticClass:"title-text flex-1"},[t._v(t._s(e.name))]),t._v(" "),s("div",{staticClass:"item-money flex-box"},[s("div",{staticClass:"item-type"},[t._v("\n                                "+t._s(e.amount)+"\n                            ")]),t._v(" "),e.amount>0&&e.price>0?s("div",{directives:[{name:"show",rawName:"v-show",value:t.$store.state.init.token_add,expression:"$store.state.init.token_add"}],staticClass:"item-decs flex-box"},[s("div",{staticClass:"item-decs-list"},[t._v("≈  ¥ "+t._s((e.amount*e.price).toFixed(2)))])]):t._e()])])])])})),t._v(" "),s("box",{directives:[{name:"show",rawName:"v-show",value:this.$store.state.balance_loading,expression:"this.$store.state.balance_loading"}],staticClass:"page"},[s("load-more",{directives:[{name:"show",rawName:"v-show",value:this.$store.state.balance_loading,expression:"this.$store.state.balance_loading"}],attrs:{tip:"正在加载"}})],1)],1),t._v(" "),s("popup",{directives:[{name:"transfer-dom",rawName:"v-transfer-dom"}],staticStyle:{width:"70%",background:"#fff"},attrs:{position:"right"},model:{value:t.showRightBar,callback:function(e){t.showRightBar=e},expression:"showRightBar"}},[s("div",{staticStyle:{width:"100%","padding-top":"30px"}},[s("group",{attrs:{title:"钱包列表"}},[s("radio",{staticClass:"right_item",attrs:{options:t.wallets},scopedSlots:t._u([{key:"each-item",fn:function(e){return[s("p",[s("img",{staticClass:"vux-radio-icon",attrs:{src:a("4fan")}}),t._v(" "+t._s(e.label)+"\n                        ")])]}}]),model:{value:t.wallet_address,callback:function(e){t.wallet_address=e},expression:"wallet_address"}})],1),t._v(" "),s("box",{attrs:{gap:"25px 10px"}},[s("x-button",{staticClass:"right_item  rbt",staticStyle:{"background-color":"#5871ff","border-radius":"99px"},attrs:{type:"primary"},nativeOn:{click:function(e){return t.create_wallet(e)}}},[t._v("创建钱包")]),t._v(" "),s("x-button",{staticClass:"right_item rbt",staticStyle:{"background-color":"#ff6678","border-radius":"99px"},attrs:{type:"primary"},nativeOn:{click:function(e){return t.import_wallet(e)}}},[t._v("导入钱包")]),t._v(" "),s("x-button",{staticClass:"right_item rbt",staticStyle:{"background-color":"#fff",padding:"0"},attrs:{type:"default"},nativeOn:{click:function(e){return t.manage_wallet(e)}}},[t._v("管理钱包")])],1)],1)]),t._v(" "),s("popup",{directives:[{name:"transfer-dom",rawName:"v-transfer-dom"}],staticStyle:{width:"100%",background:"#fff"},attrs:{position:"right"},model:{value:t.showTokenListPage,callback:function(e){t.showTokenListPage=e},expression:"showTokenListPage"}},[s("x-header",{directives:[{name:"show",rawName:"v-show",value:!t.isShowSearch,expression:"!isShowSearch"}],staticStyle:{"z-index":"1"},attrs:{"left-options":{showBack:!0,backText:"",preventGoBack:!0},"right-options":{showMore:!1}},on:{"on-click-back":function(e){t.hideTokenList()}}},[t._v("添加新资产\n            "),s("i",{staticClass:"weui-icon-search",staticStyle:{fill:"#fff",position:"relative",top:"-2px","font-size":"1.4rem"},attrs:{slot:"right"},on:{click:function(e){t.showSearch()}},slot:"right"})]),t._v(" "),s("search",{directives:[{name:"show",rawName:"v-show",value:t.isShowSearch,expression:"isShowSearch"}],ref:"search_token",staticClass:"token-search",attrs:{placeholder:"搜索Token名称或合约地址","auto-fixed":!1},on:{"on-submit":t.search,"on-cancel":t.cancel},model:{value:t.keyword,callback:function(e){t.keyword=e},expression:"keyword"}}),t._v(" "),t.loading?s("load-more",{attrs:{tip:"正在处理中 . . ."}}):t._e(),t._v(" "),s("div",{staticClass:"page-tokenlist page-tokenlist-inter"},[t.token_lists.length>0?s("scroller",{ref:"scrollerEvent",attrs:{height:"-52","lock-x":""},on:{"on-scroll-bottom":t.onScrollBottom}},[s("div",{staticClass:"task-block"},[t._l(t.token_lists,function(e){return s("div",{staticClass:"item flex-box vux-1px-b"},[s("div",{staticClass:"item-img"},[s("img",{attrs:{src:e.icon,alt:""}})]),t._v(" "),s("div",{staticClass:"item-info flex-1"},[s("div",{staticClass:"item-title flex-box"},[s("div",{staticClass:"item-money flex-box"},[s("div",{staticClass:"item-decs flex-box"},[s("div",{staticClass:"item-type"},[t._v(t._s(e.token_symbol))])]),t._v(" "),s("div",{staticClass:"item-decs flex-box"},[s("div",{staticClass:"item-decs-list"},[t._v(t._s(e.token_name))])]),t._v(" "),s("div",{staticClass:"item-decs flex-box"},[s("div",{staticClass:"item-decs-list"},[t._v(t._s(e.contract_address.substr(0,8)+"..."+e.contract_address.substr(-8,8)))])])])])]),t._v(" "),t.isShowSearch?s("div",{staticClass:"item-decs flex-box"},[1!=e.status?s("x-button",{attrs:{mini:"",type:"primary"},nativeOn:{click:function(a){t.subscribeSet(e.id)}}},[t._v("添加")]):s("span",{staticClass:"status-txt"},[t._v("已添加")])],1):s("group",{staticClass:"item-decs flex-box"},[s("inline-x-switch",{attrs:{"value-map":[0,1]},on:{"on-change":function(a){t.subscribeSet(e.id)}},model:{value:e.status,callback:function(a){t.$set(e,"status",a)},expression:"token.status"}})],1)],1)}),t._v(" "),t.loading?s("load-more",{attrs:{tip:"正在加载 . . ."}}):s("load-more",{attrs:{"show-loading":!1,tip:"没有更多了","background-color":"#fbf9fe"}})],2)]):s("nodata",{attrs:{datatip:"暂无数据"}})],1)],1),t._v(" "),s("popup",{directives:[{name:"transfer-dom",rawName:"v-transfer-dom"}],attrs:{height:"100%",position:"top","is-transparent":""},model:{value:t.qrcodeshow,callback:function(e){t.qrcodeshow=e},expression:"qrcodeshow"}},[s("div",{staticStyle:{width:"240px","background-color":"#fff",height:"220px",margin:"150px auto 0","border-radius":"5px","padding-top":"20px","text-align":"center","z-index":"999"}},[s("qrcode",{staticClass:"qrcode",attrs:{value:this.$store.state.wallet.address}}),t._v(" "),s("div",{staticStyle:{"line-height":"30px",height:"30px"}},[t._v(t._s(t.shortStr(this.$store.state.wallet.address,12,20,"...")))]),t._v(" "),s("div",{staticStyle:{padding:"20px 15px"}},[s("x-button",{directives:[{name:"clipboard",rawName:"v-clipboard:copy",value:this.$store.state.wallet.address,expression:"this.$store.state.wallet.address",arg:"copy"},{name:"clipboard",rawName:"v-clipboard:success",value:t.onCopy,expression:"onCopy",arg:"success"},{name:"clipboard",rawName:"v-clipboard:error",value:t.onError,expression:"onError",arg:"error"}],staticStyle:{"font-size":"14px"},attrs:{type:"primary"}},[t._v("复制")]),t._v(" "),s("x-button",{staticStyle:{"font-size":"14px"},nativeOn:{click:function(e){t.qrcodeshow=!1}}},[t._v("关闭")])],1)],1)])],1)},staticRenderFns:[]};var S=a("C7Lr")(y,C,!1,function(t){a("uydd")},"data-v-663ae88c",null);e.default=S.exports},hlrq:function(t,e,a){"use strict";Boolean,Boolean,String,Number,Array;var s={name:"x-switch",methods:{toBoolean:function(t){return this.valueMap?1===this.valueMap.indexOf(t):t},toRaw:function(t){return this.valueMap?this.valueMap[t?1:0]:t}},props:{disabled:Boolean,value:{type:[Boolean,String,Number],default:!1},valueMap:{type:Array,default:function(){return[!1,!0]}}},data:function(){return{currentValue:this.toBoolean(this.value)}},watch:{currentValue:function(t){var e=this.toRaw(t);this.$emit("input",e),this.$emit("on-change",e)},value:function(t){this.currentValue=this.toBoolean(t)}}},i={render:function(){var t=this,e=t.$createElement;return(t._self._c||e)("input",{directives:[{name:"model",rawName:"v-model",value:t.currentValue,expression:"currentValue"}],staticClass:"inline-x-switch weui-switch",attrs:{type:"checkbox",disabled:t.disabled},domProps:{checked:Array.isArray(t.currentValue)?t._i(t.currentValue,null)>-1:t.currentValue},on:{change:function(e){var a=t.currentValue,s=e.target,i=!!s.checked;if(Array.isArray(a)){var o=t._i(a,null);s.checked?o<0&&(t.currentValue=a.concat([null])):o>-1&&(t.currentValue=a.slice(0,o).concat(a.slice(o+1)))}else t.currentValue=i}}})},staticRenderFns:[]};var o=a("C7Lr")(s,i,!1,function(t){a("NQkE")},null,null);e.a=o.exports},uydd:function(t,e){}});