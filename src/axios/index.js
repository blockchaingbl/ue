import axios from "axios";
import qs from "qs";
import { Toast } from "vux";
import router from "../router";
import store from '@/vuex/store'
import { setCookie, getCookie, deleteCookie } from "../assets/js/cookieHandle";

axios.cancelTokens = [];

//POST传参序列化(添加请求拦截器)
axios.interceptors.request.use(
  config => {
    // 在发送请求之前做某件事
    if (
      config.method === "post" ||
      config.method === "put" ||
      config.method === "delete"
    ) {
      // 序列化
      const name = Object.prototype.toString.call(config.data).match(/\[object (.*?)\]/)[1]
        if(name!=='FormData'){
            var obj = config.data;
            var arr = Object.keys(obj);
            var len = arr.length;
            //console.log("值"+len);

            if (len>0){
                config.data._user_token = JSON.parse(localStorage.getItem('token'));
                //config.data.test = "有data";
            }else{
                // config.data={};
                config.data._user_token =  JSON.parse(localStorage.getItem('token'));
                //config.data.test = "无data";
            }
            config.data = qs.stringify(config.data);
        }else{
            config.data.append('_user_token',JSON.parse(localStorage.getItem('token')))
        }
    }


    // 若是有做鉴权token , 就给头部带上token
    config.headers.common['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content');
    config.cancelToken =   new axios.CancelToken(function (cancel) {
        axios.cancelTokens.push(cancel);
    });
    return config;
  },
  error => {
    console.log(error);
   // this.$vux.toast.text(error.data.errmsg);
    return Promise.reject(error.data.errmsg);
  }
);



//返回状态判断(添加响应拦截器)
axios.interceptors.response.use(
  res => {
      var serverVersion = parseInt(res.data.front_version);
      window.localVersion = localStorage.getItem("front_version");
      if(window.localVersion) {
          window.localVersion = parseInt(localVersion);
          if(serverVersion>localVersion){
              try{
                  App.cleanCache("1");
                  localStorage.front_version = serverVersion;
                  window.localVersion = serverVersion;
                  if(res.data.update_message)
                    alert(res.data.update_message);
              }catch(ex){

              }
          }
      }else
      {
          localStorage.front_version = serverVersion;
          window.localVersion = serverVersion;
          try{
              App.cleanCache("1");
              localStorage.front_version = serverVersion;
              window.localVersion = serverVersion;
              if(res.data.update_message)
                  alert(res.data.update_message);
          }catch(ex){

          }
      }

      var init_version = parseInt(res.data.init_version)

      window.initVersion = parseInt(localStorage.getItem("init_version")) || 0;
      if(init_version>parseInt(window.initVersion))
      {
          localStorage.init_version = init_version
          console.log(store)
          axios.post('/api/app.util/init',{}).then(res => {
              if(res.data)
              {
                  store.commit('set_init',res.data)
              }
          }).catch(err => {
              // this.$vux.toast.text(err.message);
              console.log(err);
          });
      }





    //对响应数据做些事
    if (res.data && res.data.errcode != 0) {
      if (res.data.errcode == 60001) { // 用户未登录或登录状态信息过期
        deleteCookie("token");
        //this.$vux.toast.text("err.message");
        if(router.currentRoute.meta.pageType!="login_auth")
        {
            //只有非授权页才跳转
            setTimeout(() => {
                    router.push({
                    path: "/login"
                });
            }, 3000);
        }
      }
      else {
       // this.$vux.toast.text("lalalal");
       /* Message({
          //  饿了么的消息弹窗组件,类似toast
          message: res.data.message,
          type: "error"
        });*/
      }

      return Promise.reject(res.data);
    }
    return res.data;
  },
  error => {
    console.log(error);
    // 用户登录的时候会拿到一个基础信息,比如用户名,token,过期时间戳
    // 直接丢localStorage或者sessionStorage
    // if (!JSON.parse(localStorage.getItem('token'))) {
    //   // 若是接口访问的时候没有发现有鉴权的基础信息,直接返回登录页
    //   router.push({
    //     path: "/login"
    //   });
    // } else {
      // 若是有基础信息的情况下,判断时间戳和当前的时间,若是当前的时间大于服务器过期的时间
      // 乖乖的返回去登录页重新登录
      // let lifeTime =
      //   JSON.parse(window.localStorage.getItem("loginUserBaseInfo")).lifeTime *
      //   1000;
      // let nowTime = new Date().getTime(); // 当前时间的时间戳
      // console.log(nowTime, lifeTime);
      // console.log(nowTime > lifeTime);
      // if (nowTime > lifeTime) {
      //   Message({
      //     showClose: true,
      //     message: "登录状态信息过期,请重新登录",
      //     type: "error"
      //   });
      //   router.push({
      //     path: "/login"
      //   });
      // } else {
        // 下面是接口回调的satus ,因为我做了一些错误页面,所以都会指向对应的报错页面
/*        if (error.response.status === 403) {
          router.push({
            path: "/error/403"
          });
        }
        if (error.response.status === 500) {
          // router.push({
          //   path: "/error/500"
          // });
        }
        if (error.response.status === 502) {
          router.push({
            path: "/error/502"
          });
        }
        if (error.response.status === 404) {
          // router.push({
          //   path: "/error/404"
          // });
        }*/
      // }
      
    // }
      if(error.response.hasOwnProperty('response'))
      {
          if (error.response.status === 504 || !error.hasOwnProperty('response') || !error.response.hasOwnProperty('status')) {
              router.push({path: "/error/504"});
          }
      }

    console.log(error.response.status);


    // 返回 response 里的错误信息
    let errorInfo = error.data && error.data.errcode != 0 ? error.data.errmsg : error.data;
    return Promise.reject(errorInfo);
  }
);


// 对axios的实例重新封装成一个plugin ,方便 Vue.use(xxxx)
export default {
  install: function (Vue, Option) {
    Object.defineProperty(Vue.prototype, "$http", { value: axios });
  },
  cancelTokens:axios.cancelTokens,
    http:axios
};