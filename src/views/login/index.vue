<template lang="html">
<div class="page-login">
    <tab :line-width="2" custom-bar-width="60px">
        <tab-item @click.native="switchStatus('smsLogin')">短信登录</tab-item>
        <tab-item selected @click.native="switchStatus('pwdLogin')">密码登录</tab-item>
    </tab>
    <!-- 短信登录/注册 -->
    <div v-if="loginType=='smsLogin'">
        <!-- 发送验证码 -->
        <div class="" v-if="step==1">
            <div class="clear"></div>
            <group>
                <div class="login-mobile flex-box">
                    <popup-picker title="" :data="code_list" :columns="3" v-model="mobile_code" ref="picker"></popup-picker>
                    <x-input class="mobile-input flex-1" ref="mobile" title="" name="mobile" placeholder="请输入手机号码" type="number" keyboard="number" v-model="form.mobile" :required="true"></x-input>
                </div>
                <x-input ref="verifycode"  class="weui-vcode vux-1px-b"  placeholder="请输入验证码" type="number" keyboard="number"  v-model="form.verifycode" :min="6" :required="true">
                    <x-button v-if="!ifgetcode"   slot="right" plain  type="primary" mini v-on:click.native="getCode()">发送验证码</x-button>
                    <x-button v-if="ifgetcode" slot="right" plain  type="primary" mini v-on:click.native="endtimer()">重新发送{{countimer}}s'</x-button>
                </x-input>
            </group>
            <box gap="40px 10px">
                <x-button type="primary" v-on:click.native="login()">登录</x-button>
            </box>
        </div>
        <!-- 完善资料 -->
        <div class="" v-if="step==2">
            <flexbox orient="vertical" justify='center' class="l-tit">
                <div class="user-portrait">
                    <img src="@/assets/images/avatar.png" alt="">
                </div>
                <div class="modeil">{{form.mobile}}</div>
            </flexbox>
            <group>
                <x-input title="用户名" name="username" placeholder="请输入用户名" v-model="form.username" ref="username" :required="true"></x-input>
                <x-input type="password" title="密码" name="password" placeholder="请输入密码" v-model="form.password" :min="6" :max="16" ref="password" :required="true"></x-input>
                <x-input type="password" title="确认密码" name="check_password" placeholder="请输入确认密码" v-model="form.check_password" :min="6" :max="16" :equal-with="form.password" ref="check_password" :required="true"></x-input>
                <x-input title="邀请码" name="invite_code" placeholder="请输入邀请码（选填）" v-model="form.invite_code"></x-input>
            </group>
            <box gap="30px 10px 20px">
                <x-button type="primary" style="border-radius:99px;" v-on:click.native="register()">完成</x-button>
            </box>
            <div class="backlogin" style="text-align:center;" v-on:click="backlogin()">
                返回登录页
            </div>
        </div>
    </div>
    <!-- 密码登录 -->
    <div v-if="loginType=='pwdLogin'">
        <!-- <div class="login-tit">区块链身份创建/登录</div> -->
        <div class="clear"></div>
        <group>
            <x-input title="账号" name="username" placeholder="用户名/手机号" type="text"  v-model="form.username" ref="username" :required="true"></x-input>
            <x-input class="input-possword vux-1px-b" type="password" title="密码" name="password" placeholder="请输入密码"  v-model="form.password" ref="password" :required="true"></x-input>
        </group>
        <box gap="40px 10px 20px">
            <x-button type="primary" v-on:click.native="loginForPwd()">登录</x-button>
        </box>
        <div class="backlogin" style="text-align:center;" v-on:click="toResetPwd()">
            找回密码
        </div>
    </div>
</div>
</template>
<script>
import { Flexbox, FlexboxItem, Divider, Tab, TabItem ,PopupPicker} from 'vux'
import {Drawer} from 'vux'
import { mapGetters ,mapActions} from 'vuex';
import cookie from '../../utils/cookie'
import { setCookie, getCookie, deleteCookie } from "../../assets/js/cookieHandle";
export default {
    components: {
        //stepone:() => import('@/views/login/inc/stepone'),
        Flexbox,
        FlexboxItem,
        Divider,
        Drawer,
        Tab,
        TabItem,
        PopupPicker
    },
    data () {
        return {
            step:1,
            countimer:"",
            ifgetcode:false,
            wait:null,
            form: {
                mobile: '',
                verifycode: '',
                username:'',
                password:'',
                check_password:'',
                mobile_code:86
            },
            loginType:'pwdLogin',
            mobile_code: ['+86'],
            code_list:[]
        }
    },
    mounted () {
      if(this.$route.params.hasOwnProperty('reload') && this.$route.params.reload==1)
      {
        window.location.reload()
      }
        this.$http.post('api/app.util/init/get_mobile_code',{}).then(res=>{
            this.code_list = res.data.code_list;
        })
    },
    methods:{
        ...mapActions([
            'setToken',
        ]),
        getCode(){
            this.$refs.mobile.validate();
            if (this.$refs.mobile.valid&&this.form.mobile!="") {
                this.$http.post('/api/app.util/sms/send',{
                    mobile:this.form.mobile,
                    type:0,
                    mobile_code:parseInt(this.mobile_code)
                }).then(res => {
                    this.$vux.toast.text("发送成功");
                    this.$vux.toast.text(res.message);
                    console.log(res);
                    this.counttime(60);
                }).catch(err => {
                    this.$vux.toast.text(err.message);
                    console.log(err);
                    if (err.errcode==10000) {
                        this.ifgetcode=true;
                        this.countimer=err.data.second;
                        this.counttime(err.data.second);
                    }
                });
            }else{
                this.$refs.mobile.forceShowError = true;
                this.$vux.toast.text("请输入正确的手机号");
            }
        },
        login() {
            this.$refs.mobile.validate();
            this.$refs.verifycode.validate();
            this.form.mobile_code = parseInt(this.mobile_code);
            if (this.$refs.mobile.valid && this.$refs.verifycode.valid) {
                this.$http.post('/api/app.user/user/login',this.form).then(res => {

                    if (res.errcode=="0") {
                        //登录成功
                        this.setToken(res.data._user_token);
                        localStorage.setItem('token', JSON.stringify(res.data._user_token));
                        cookie.setCookie('uid', res.data.accid)
                        cookie.setCookie('sdktoken',res.data.token)
                        this.$router.push({path:'/mine/center'});
                    }
                }).catch(err => {
                    if(err.errcode=="50001"){
                        //未注册
                        this.step=2;
                    }else{
                        this.$vux.toast.text(err.message);
                    }
                    console.log(err);
                    //  this.Toast(err || '网络异常，请求失败');
                });
            }else{
                this.$refs.mobile.forceShowError = true;
                this.$refs.verifycode.forceShowError = true;
            }
        },
        register(){
            this.$refs.username.validate();
            this.$refs.password.validate();
            this.$refs.check_password.validate();
            this.form.mobile_code = parseInt(this.mobile_code);
            if(this.$refs.username.valid&&this.$refs.password.valid&&this.$refs.check_password.valid)
            {
                this.$http.post('/api/app.user/user/register',this.form).then(res => {
                    if (res.errcode=="0") {
                        //登录成功
                        this.setToken(res.data._user_token);
                        localStorage.setItem('token', JSON.stringify(res.data._user_token));
                        cookie.setCookie('uid', res.data.accid)
                        cookie.setCookie('sdktoken',res.data.token)
                        //setCookie("token",res.data._user_token);
                        this.$router.push({path:'/mine/center'});
                    }
                }).catch(err => {
                    if (err.errcode) {
                        this.$vux.toast.text(err.message);
                    }
                    if(err.errcode==50010){
                        this.step=1;
                    }
                    //  this.Toast(err || '网络异常，请求失败');
                });
            }else{
                this.$refs.username.forceShowError = true;
                this.$refs.password.forceShowError = true;
                this.$refs.check_password.forceShowError = true;
                if(!this.form.check_password)
                {
                    this.$refs.check_password.errors.equal = '输入不一致';
                    this.$refs.check_password.getError();
                }
            }
        },
        loginForPwd(){
            this.$refs.username.validate();
            this.$refs.password.validate();
            if(this.$refs.username.valid&&this.$refs.password.valid)
            {
                this.$http.post('/api/app.user/user/loginForPwd',this.form).then(res => {
                    console.log(res.errcode);
                    if (res.errcode=="0") {
                        //登录成功
                        this.setToken(res.data._user_token);
                        localStorage.setItem('token', JSON.stringify(res.data._user_token));
                        //setCookie("token",res.data._user_token);
                        cookie.setCookie('uid', res.data.accid)
                        cookie.setCookie('sdktoken',res.data.token)
                        this.$router.push({path:'/mine/center'});
                    }
                }).catch(err => {
                    if(err.errcode=="50001"){
                        this.$vux.toast.text("您还未注册，请使用短信登录完成注册");
                    }else{
                        this.$vux.toast.text(err.message);
                    }
                    console.log(err);
                    //  this.Toast(err || '网络异常，请求失败');
                });
            }else{
                this.$refs.username.forceShowError = true;
                this.$refs.password.forceShowError = true;
            }
        },
        backlogin(){
            this.step=1;
        },
        toResetPwd(){
            this.$router.push({path:'/login/pwdreset'});
        },
        counttime(TIME_COUNT){
        if (!this.wait) {
          this.countimer = TIME_COUNT;
          this.ifgetcode = true;
          this.wait =setInterval(() =>{
              if (this.countimer > 0 && this.countimer <= TIME_COUNT) {
                this.countimer--;
              } else {
                this.ifgetcode = false;
                clearInterval(this.wait);
                this.wait = null;
              }
          }, 1000);
        }
        },
        endtimer(){
            this.$vux.toast.text("请于倒计时结束后再获取验证码");
        },
        switchStatus(type){
            this.loginType = type;
        }
    }
}
</script>

<style lang="less">
  @import '~vux/src/styles/1px.less';
  @import '../../assets/css/variable.less';
.clear{
    clear: both;
}
.backlogin{
  color: #628cf8;
}
.page-login {
  background: #fff;
  height: 100%;
    .login-tit{
        padding:0 10px;
        font-size: 1rem;
        line-height: 1.6rem;padding-top: 2rem;

    }
    .login-line{
        padding:0 10px;
        font-size: .8rem;
        height: 1.6rem;
        line-height: 1.6rem;
        &:after{
            display: block;
            content: "";
            clear: both;
        }
        .login-left{
            float: left;
        }
        .login-right{
            float: right;
        }
    }
    .l-tit{
        padding-top:2rem;
        line-height: 1.6rem;
    }
    .user-portrait{
    	width: 5rem;
    	height: 5rem;
    	overflow: hidden;
    	border-radius: 50%;
    	img{
    		display: block;
    		width: 100%;
    		height: 100%;
    	}
    }
}
.login-mobile{
  .weui-cell_access{
      .vux-cell-primary{
          min-width: 2rem;
          .vux-popup-picker-select{
              text-align: left!important;
          }
      }
      .vux-cell-value{
          color: #111;
      }
      .weui-cell__ft{
          padding-right: 1rem;
          &::after{
              border-width: 0 1px 1px 0;
              border-color: #111;
          }
      }
  }
  .weui-cell__hd,.vux-cell-bd{
      display: none;
  }
  .mobile-input{
      padding-left: 0.625rem;
  }
}

</style>
