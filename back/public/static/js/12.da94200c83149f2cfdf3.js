webpackJsonp([12],{K97K:function(t,e){},MzHd:function(t,e,i){"use strict";var n={render:function(){var t=this.$createElement;return(this._self._c||t)("div",{staticClass:"weui-cells__title"},[this._t("default")],2)},staticRenderFns:[]};var s=i("C7Lr")({name:"group-title"},n,!1,function(t){i("K97K")},null,null);e.a=s.exports},"Q+vJ":function(t,e){},Xexo:function(t,e,i){var n={"./views/article/index":"wm3/"};function s(t){return i(o(t))}function o(t){var e=n[t];if(!(e+1))throw new Error("Cannot find module '"+t+"'.");return e}s.keys=function(){return Object.keys(n)},s.resolve=o,t.exports=s,s.id="Xexo"},vpQo:function(t,e){},vre1:function(t,e){},"wm3/":function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var n=i("crx6"),s=i("oa6C"),o=i("mAm1"),r=i("NyFS"),a=i("3cXf"),c=i.n(a),h=i("AA3o"),l=i.n(h),u=i("xSur"),d=i.n(u),v=i("+Up5"),p=i.n(v),_=function(t){return Array.prototype.slice.call(t)},f=function(){function t(e){if(l()(this,t),this._default={container:".vux-swiper",item:".vux-swiper-item",direction:"vertical",activeClass:"active",threshold:50,duration:300,auto:!1,loop:!1,interval:3e3,height:"auto",minMovingDistance:0},this._options=p()(this._default,e),this._options.height=this._options.height.replace("px",""),this._start={},this._move={},this._end={},this._eventHandlers={},this._prev=this._current=this._goto=0,this._width=this._height=this._distance=0,this._offset=[],this.$box=this._options.container,this.$container=this._options.container.querySelector(".vux-swiper"),this.$items=this.$container.querySelectorAll(this._options.item),this.count=this.$items.length,this.realCount=this.$items.length,this._position=[],this._firstItemIndex=0,this.count)return this._init(),this._auto(),this._bind(),this._onResize(),this}return d()(t,[{key:"_auto",value:function(){var t=this;t.stop(),t._options.auto&&(t.timer=setTimeout(function(){t.next()},t._options.interval))}},{key:"updateItemWidth",value:function(){this._width=this.$box.offsetWidth||document.documentElement.offsetWidth,this._distance="horizontal"===this._options.direction?this._width:this._height}},{key:"stop",value:function(){this.timer&&clearTimeout(this.timer)}},{key:"_loop",value:function(){return this._options.loop&&this.realCount>=3}},{key:"_onResize",value:function(){var t=this;this.resizeHandler=function(){setTimeout(function(){t.updateItemWidth(),t._setOffset(),t._setTransform()},100)},window.addEventListener("orientationchange",this.resizeHandler,!1)}},{key:"_init",value:function(){this._height="auto"===this._options.height?"auto":this._options.height-0,this.updateItemWidth(),this._initPosition(),this._activate(this._current),this._setOffset(),this._setTransform(),this._loop()&&this._loopRender()}},{key:"_initPosition",value:function(){for(var t=0;t<this.realCount;t++)this._position.push(t)}},{key:"_movePosition",value:function(t){if(t>0){var e=this._position.splice(0,1);this._position.push(e[0])}else if(t<0){var i=this._position.pop();this._position.unshift(i)}}},{key:"_setOffset",value:function(){var t=this,e=t._position.indexOf(t._current);t._offset=[],_(t.$items).forEach(function(i,n){t._offset.push((n-e)*t._distance)})}},{key:"_setTransition",value:function(t){var e="none"===(t=t||this._options.duration||"none")?"none":t+"ms";_(this.$items).forEach(function(t,i){t.style.webkitTransition=e,t.style.transition=e})}},{key:"_setTransform",value:function(t){var e=this;t=t||0,_(e.$items).forEach(function(i,n){var s=e._offset[n]+t,o="translate3d("+s+"px, 0, 0)";"vertical"===e._options.direction&&(o="translate3d(0, "+s+"px, 0)"),i.style.webkitTransform=o,i.style.transform=o})}},{key:"_bind",value:function(){var t=this,e=this;e.touchstartHandler=function(t){e.stop(),e._start.x=t.changedTouches[0].pageX,e._start.y=t.changedTouches[0].pageY,e._setTransition("none")},e.touchmoveHandler=function(i){if(1!==e.count){e._move.x=i.changedTouches[0].pageX,e._move.y=i.changedTouches[0].pageY;var n=e._move.x-e._start.x,s=e._move.y-e._start.y,o=s,r=Math.abs(n)>Math.abs(s);"horizontal"===e._options.direction&&r&&(o=n),t._options.loop||t._current!==t.count-1&&0!==t._current||(o/=3),(e._options.minMovingDistance&&Math.abs(o)>=e._options.minMovingDistance||!e._options.minMovingDistance)&&r&&e._setTransform(o),r&&i.preventDefault()}},e.touchendHandler=function(t){if(1!==e.count){e._end.x=t.changedTouches[0].pageX,e._end.y=t.changedTouches[0].pageY;var i=e._end.y-e._start.y;"horizontal"===e._options.direction&&(i=e._end.x-e._start.x),0!==(i=e.getDistance(i))&&e._options.minMovingDistance&&Math.abs(i)<e._options.minMovingDistance||(i>e._options.threshold?e.move(-1):i<-e._options.threshold?e.move(1):e.move(0),e._loopRender())}},e.transitionEndHandler=function(t){e._activate(e._current);var i=e._eventHandlers.swiped;i&&i.apply(e,[e._prev%e.count,e._current%e.count]),e._auto(),e._loopRender(),t.preventDefault()},e.$container.addEventListener("touchstart",e.touchstartHandler,!1),e.$container.addEventListener("touchmove",e.touchmoveHandler,!1),e.$container.addEventListener("touchend",e.touchendHandler,!1),e.$items[1]&&e.$items[1].addEventListener("webkitTransitionEnd",e.transitionEndHandler,!1)}},{key:"_loopRender",value:function(){var t=this;t._loop()&&(0===t._offset[t._offset.length-1]?(t.$container.appendChild(t.$items[0]),t._loopEvent(1)):0===t._offset[0]&&(t.$container.insertBefore(t.$items[t.$items.length-1],t.$container.firstChild),t._loopEvent(-1)))}},{key:"_loopEvent",value:function(t){var e=this;e._itemDestoy(),e.$items=e.$container.querySelectorAll(e._options.item),e.$items[1]&&e.$items[1].addEventListener("webkitTransitionEnd",e.transitionEndHandler,!1),e._movePosition(t),e._setOffset(),e._setTransform()}},{key:"getDistance",value:function(t){return this._loop()?t:t>0&&0===this._current?0:t<0&&this._current===this.realCount-1?0:t}},{key:"_moveIndex",value:function(t){0!==t&&(this._prev=this._current,this._current+=this.realCount,this._current+=t,this._current%=this.realCount)}},{key:"_activate",value:function(t){var e=this._options.activeClass;Array.prototype.forEach.call(this.$items,function(i,n){i.classList.remove(e),t===Number(i.dataset.index)&&i.classList.add(e)})}},{key:"go",value:function(t){var e=this;return e.stop(),t=t||0,t+=this.realCount,t%=this.realCount,t=this._position.indexOf(t)-this._position.indexOf(this._current),e._moveIndex(t),e._setOffset(),e._setTransition(),e._setTransform(),e._auto(),this}},{key:"next",value:function(){return this.move(1),this}},{key:"move",value:function(t){return this.go(this._current+t),this}},{key:"on",value:function(t,e){return this._eventHandlers[t],this._eventHandlers[t]=e,this}},{key:"_itemDestoy",value:function(){var t=this;this.$items.length&&_(this.$items).forEach(function(e){e.removeEventListener("webkitTransitionEnd",t.transitionEndHandler,!1)})}},{key:"destroy",value:function(){if(this.stop(),this._current=0,this._setTransform(0),window.removeEventListener("orientationchange",this.resizeHandler,!1),this.$container.removeEventListener("touchstart",this.touchstartHandler,!1),this.$container.removeEventListener("touchmove",this.touchmoveHandler,!1),this.$container.removeEventListener("touchend",this.touchendHandler,!1),this._itemDestoy(),this._options.loop&&2===this.count){var t=this.$container.querySelector(this._options.item+"-clone");t&&this.$container.removeChild(t),(t=this.$container.querySelector(this._options.item+"-clone"))&&this.$container.removeChild(t)}}}]),t}(),m=i("+Ln8"),g=(Array,String,Boolean,Boolean,String,String,Boolean,Boolean,Number,Number,Number,String,Number,Number,Number,{name:"swiper",created:function(){this.index=this.value||0,this.index&&(this.current=this.index)},mounted:function(){var t=this;this.hasTwoLoopItem(),this.$nextTick(function(){t.list&&0===t.list.length||t.render(t.index),t.xheight=t.getHeight(),t.$emit("on-get-height",t.xheight)})},methods:{hasTwoLoopItem:function(){2===this.list.length&&this.loop?this.listTwoLoopItem=this.list:this.listTwoLoopItem=[]},clickListItem:function(t){Object(m.a)(t.url,this.$router),this.$emit("on-click-list-item",JSON.parse(c()(t)))},buildBackgroundUrl:function(t){return t.fallbackImg?"url("+t.img+"), url("+t.fallbackImg+")":"url("+t.img+")"},render:function(){var t=this,e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0;this.swiper&&this.swiper.destroy(),this.swiper=new f({container:this.$el,direction:this.direction,auto:this.auto,loop:this.loop,interval:this.interval,threshold:this.threshold,duration:this.duration,height:this.height||this._height,minMovingDistance:this.minMovingDistance,imgList:this.imgList}).on("swiped",function(e,i){t.current=i%t.length,t.index=i%t.length}),e>0&&this.swiper.go(e)},rerender:function(){var t=this;this.$el&&!this.hasRender&&(this.hasRender=!0,this.hasTwoLoopItem(),this.$nextTick(function(){t.index=t.value||0,t.current=t.value||0,t.length=t.list.length||t.$children.length,t.destroy(),t.render(t.value)}))},destroy:function(){this.hasRender=!1,this.swiper&&this.swiper.destroy()},getHeight:function(){var t=parseInt(this.height,10);return t?this.height:t?void 0:this.aspectRatio?this.$el.offsetWidth*this.aspectRatio+"px":"180px"}},props:{list:{type:Array,default:function(){return[]}},direction:{type:String,default:"horizontal"},showDots:{type:Boolean,default:!0},showDescMask:{type:Boolean,default:!0},dotsPosition:{type:String,default:"right"},dotsClass:String,auto:Boolean,loop:Boolean,interval:{type:Number,default:3e3},threshold:{type:Number,default:50},duration:{type:Number,default:300},height:{type:String,default:"auto"},aspectRatio:Number,minMovingDistance:{type:Number,default:0},value:{type:Number,default:0}},data:function(){return{hasRender:!1,current:this.index||0,xheight:"auto",length:this.list.length,index:0,listTwoLoopItem:[]}},watch:{auto:function(t){t?this.swiper&&this.swiper._auto():this.swiper&&this.swiper.stop()},list:function(t){this.rerender()},current:function(t){this.index=t,this.$emit("on-index-change",t)},index:function(t){var e=this;t!==this.current&&this.$nextTick(function(){e.swiper&&e.swiper.go(t)}),this.$emit("input",t)},value:function(t){this.index=t}},beforeDestroy:function(){this.destroy()}}),y={render:function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{staticClass:"vux-slider"},[i("div",{staticClass:"vux-swiper",style:{height:t.xheight}},[t._t("default"),t._v(" "),t._l(t.list,function(e,n){return i("div",{staticClass:"vux-swiper-item",attrs:{"data-index":n},on:{click:function(i){t.clickListItem(e)}}},[i("a",{attrs:{href:"javascript:"}},[i("div",{staticClass:"vux-img",style:{backgroundImage:t.buildBackgroundUrl(e)}}),t._v(" "),t.showDescMask?i("p",{staticClass:"vux-swiper-desc"},[t._v(t._s(e.title))]):t._e()])])}),t._v(" "),t._l(t.listTwoLoopItem,function(e,n){return t.listTwoLoopItem.length>0?i("div",{staticClass:"vux-swiper-item vux-swiper-item-clone",attrs:{"data-index":n},on:{click:function(i){t.clickListItem(e)}}},[i("a",{attrs:{href:"javascript:"}},[i("div",{staticClass:"vux-img",style:{backgroundImage:t.buildBackgroundUrl(e)}}),t._v(" "),t.showDescMask?i("p",{staticClass:"vux-swiper-desc"},[t._v(t._s(e.title))]):t._e()])]):t._e()})],2),t._v(" "),i("div",{directives:[{name:"show",rawName:"v-show",value:t.showDots,expression:"showDots"}],class:[t.dotsClass,"vux-indicator","vux-indicator-"+t.dotsPosition]},t._l(t.length,function(e){return i("a",{attrs:{href:"javascript:"}},[i("i",{staticClass:"vux-icon-dot",class:{active:e-1===t.current}})])}))])},staticRenderFns:[]};var x=i("C7Lr")(g,y,!1,function(t){i("vre1")},null,null).exports,w={render:function(){var t=this.$createElement;return(this._self._c||t)("div",{staticClass:"vux-swiper-item"},[this._t("default")],2)},staticRenderFns:[]},k=i("C7Lr")({name:"swiper-item",mounted:function(){var t=this;this.$nextTick(function(){t.$parent.rerender()})},beforeDestroy:function(){var t=this.$parent;this.$nextTick(function(){t.rerender()})}},w,!1,null,null,null).exports,$=i("MzHd");
/*!
 * HEX <=> RGB Conversion
 * Copyright(c) 2011 Daniel Lamb <daniellmb.com>
 * MIT Licensed
 */
function b(t){var e=parseInt(t,16);return[e>>16,e>>8&255,255&e]}String,Number,Boolean;var C={name:"masker",props:{color:{type:String,default:"0, 0, 0"},opacity:{type:Number,default:.5},fullscreen:{type:Boolean,default:!1}},computed:{style:function(){return{backgroundColor:"rgba("+(/,/.test(this.color)?this.color:b(this.color.replace("#","")).join(","))+","+this.opacity+")"}}}},T={render:function(){var t=this.$createElement,e=this._self._c||t;return e("div",{staticClass:"vux-masker-box"},[this._t("default"),this._v(" "),e("div",{staticClass:"vux-masker",class:{"vux-masker-fullscreen":this.fullscreen},style:this.style},[this._t("content")],2)],2)},staticRenderFns:[]};var E=i("C7Lr")(C,T,!1,function(t){i("vpQo")},null,null).exports,L=i("QOpP"),D=i("YaEn"),I=(o.a,r.a,n.a,s.a,$.a,L.default,{components:{Flexbox:o.a,FlexboxItem:r.a,LoadMore:n.a,Divider:s.a,Swiper:x,SwiperItem:k,GroupTitle:$.a,Masker:E,Nodata:L.default},data:function(){return{page_title:D.a.currentRoute.meta.title,page_name:D.a.currentRoute.name,formData:{page:1},article_lists:[],lock:!1,adverst:[],index:0}},mounted:function(){this.getAdverst(),this.getArticles()},methods:{getArticles:function(){var t=this;if(this.lock)return!1;this.lock=!0,this.loading=1,this.$http.post("api/app.cms/article",this.formData).then(function(e){e.data.articles.length>0&&(t.article_lists=t.article_lists.concat_unk(e.data.articles,"id"),t.lock=!1),t.loading=0,t.formData.page++}).catch(function(e){t.loading=0})},onScrollBottom:function(){this.lock||this.getArticles()},turnDetail:function(t){var e="http://"+document.domain+"/article/"+t.id;try{App.open_type('{"url":"'+e+'"}')}catch(t){window.location.href=e}},getAdverst:function(){var t=this;this.$http.post("api/app.cms/adverst/article",{}).then(function(e){t.adverst=e.data.adversts})},open:function(t){try{App.open_type('{"url":"'+t.url+'"}')}catch(e){window.location.href=t.url}}}}),H={render:function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("scroller",{ref:"scrollerEvent",attrs:{"lock-x":""},on:{"on-scroll-bottom":t.onScrollBottom}},[i("div",{attrs:{id:t.page_name}},[t.adverst.length>0?i("swiper",{attrs:{auto:""}},t._l(t.adverst,function(e,n){return i("swiper-item",{key:n,staticClass:"swiper_item",nativeOn:{click:function(i){t.open(e)}}},[i("img",{staticClass:"swiper_image",attrs:{src:e.image,alt:""}}),t._v(" "),i("p",{staticClass:"swiper_desc"},[t._v(t._s(e.name))])])})):t._e(),t._v(" "),i("group-title",[t._v("文章")]),t._v(" "),t.article_lists.length>0?i("div",[t._l(t.article_lists,function(e){return i("div",{staticStyle:{margin:"0 0.75rem 0.625rem",overflow:"hidden"},on:{click:function(i){t.turnDetail(e)}}},[i("div",{staticClass:"m-img"},[i("img",{attrs:{src:e.image,alt:""}})]),t._v(" "),i("div",{staticClass:"m-title"},[t._v(t._s(e.title))]),t._v(" "),i("div",{staticClass:"m-time"},[t._v(t._s(e.create_time))])])}),t._v(" "),t.loading?i("load-more",{attrs:{tip:"正在加载 . . ."}}):i("load-more",{attrs:{"show-loading":!1,tip:"没有更多了","background-color":"#fbf9fe"}})],2):t._e(),t._v(" "),0==t.article_lists.length?i("nodata",{attrs:{datatip:"暂无数据"}}):t._e()],1)])},staticRenderFns:[]};var S=i("C7Lr")(I,H,!1,function(t){i("Q+vJ")},null,null);e.default=S.exports}});