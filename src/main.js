// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue';
import Vuex from 'vuex';
import axios from './axios/index.js';
import App from './App';
import router from './router';
import api from '@/config/api';
import { getCookie } from '@/assets/js/cookieHandle.js';
import '../theme/index.css';
import '@/assets/js/vueFilter.js';
import VueRouter from 'vue-router'
import FastClick from 'fastclick'
import store from '@/vuex/store'


import '@/assets/iconfont/iconfont.css'

import JSON from "JSON";
import fanweCrypto from '@/crypto';
//插件形式调用
import  {LoadingPlugin ,ConfirmPlugin,Scroller,Confirm ,XInput,Box,XButton,Group,Cell,AlertPlugin,ToastPlugin,Toast,XDialog,XHeader,XImg,XNumber,XSwitch,XTable,XTextarea,Tabbar, TabbarItem,Tab, TabItem  ,Card} from 'vux'
import { Loading } from 'element-ui';

import VueClipboard from 'vue-clipboard2'
import scroll from 'vue-seamless-scroll'
Vue.use(scroll)
FastClick.attach(document.body)

Vue.config.productionTip = false;
Vue.component('x-input',XInput);
Vue.component('group',Group);
Vue.component('x-button', XButton);
Vue.component('box',Box);
Vue.component('toast', Toast);
Vue.component(XDialog);
Vue.component('x-header', XHeader);
Vue.component('cell', Cell);
Vue.component(XImg);
Vue.component(XNumber);
Vue.component(XSwitch);
Vue.component(XTable);
Vue.component('x-textarea', XTextarea);
Vue.component('tabbar-item',TabbarItem);
Vue.component('tabbar',Tab);
Vue.component(TabItem);
Vue.component(Card);
Vue.component('tabbar', Tabbar);
Vue.component('tabbar-item', TabbarItem);
Vue.component('confirm', Confirm);
Vue.component('scroller', Scroller)
Vue.use(LoadingPlugin)

 
Vue.use(AlertPlugin);
Vue.use(ConfirmPlugin);
Vue.use(ToastPlugin);

Vue.use(VueClipboard)

axios.install(Vue);
Vue.prototype.$loading = Loading.service;
Vue.prototype.$store = store;
Vue.prototype.shortStr = function(str,start,length,replace){
    try {
        var firstStr = str.substr(0, start);
        var endStr = str.substr(start + length, str.length);
        return firstStr + replace + endStr;
    }catch(ex){

    }
};

Vue.prototype.toInt = function(number,decimals){
    var number = parseFloat(number);
    for(var i=0;i<decimals;i++){
        number*=10;
    }
    return number;
};

//唯一的方式加入数组
Array.prototype.concat_unk = function(items,keyname) {
  try{
    var arr = []; //建立新的数组
    var hash = {}; //用于比对的k->v对象
    var origin = this;
    for(var k in origin)
    {
        var item = origin[k];
        if(!hash[item[keyname]]&&item[keyname])
        {
           arr.push(item);
        }
        hash[item[keyname]] = item;
    }
    for(var k in items){
      var item = items[k];
      if(!hash[item[keyname]]&&item[keyname])
      {
        arr.push(item);
      }
    }
  }catch(ex)
  {
    console.log(ex);
  }
  return arr;
};


/*
router.beforeEach((to, from, next) => {
  let apiIsLogin = process.env.NODE_ENV == 'production' ? api.isLogin() : '/api/mapi/index.php?ctl=login&act=is_login',
    apiAppInit = process.env.NODE_ENV == 'production' ? api.getAppInit() : '/api/mapi/index.php?ctl=app&act=init',
    apiUserInfo = process.env.NODE_ENV == 'production' ? api.getUserHome() : '/api/mapi/index.php?ctl=user&act=user_home';

  $("input").blur(); // 切换组件前，input失去焦点
  document.title = to.meta.title;
  // store.dispatch('showHeadBar', to.meta.showHeadBar);
  // store.dispatch('showFooterBar', to.meta.showFooterBar);
  // store.dispatch('setHasBack', to.meta.hasBack);
  // store.dispatch('setPageTitle', to.meta.title);

  if (to.meta.requireAuth !== false) {
    if (!getCookie("user_id")) {
      // 第一次访问
      console.log('授权登录')
      next({
        path: '/login',
        query: { redirect: to.fullPath } //把要跳转的地址作为参数传到下一步
      });
    }
    else if (!store.state.appInit || !store.state.userInfo) {
      // 刷新页面，获取数据存入vuex
      if (!store.state.appInit) {
        Indicator.open('正在加载中');
        axios.get(apiAppInit).then(res => {
          store.dispatch('setAppInit', res.data);
          if (!store.state.userInfo) {
            axios.get(apiUserInfo).then(res => {
              Indicator.close();
              store.dispatch('setUserInfo', res.data.user);
              next();
            });
          }
          else {
            next();
          }
        });
      }
    }
    else {
      // 已经登录
      next();
    }
  }
  else {
    next();
  }
});*/
//切换路由取消之前发起的请求
router.beforeEach((to, from, next) => {
    /*$("#app").css("position","absolute");
    $(".app-body").css("position","absolute");
    $(".app-body").css("height","auto");*/
    while (axios.cancelTokens.length > 0) {
        var cancel = axios.cancelTokens.pop();
        cancel();
    }
    next();
})

/* eslint-disable no-new */
var $vue = new Vue({
  router,
  store,
  components: { App },
  template: '<App/>'
}).$mount('#app')


window.tx_callback = null;

window.convertBase64UrlToBlob = function(urlData){

    var bytes=window.atob(urlData.split(',')[1]);        //去掉url的头，并转换为byte

    //处理异常,将ascii码小于0的转换为大于0
    var ab = new ArrayBuffer(bytes.length);
    var ia = new Uint8Array(ab);
    for (var i = 0; i < bytes.length; i++) {
        ia[i] = bytes.charCodeAt(i);
    }

    return new Blob( [ab] , {type : 'image/png'});
}
