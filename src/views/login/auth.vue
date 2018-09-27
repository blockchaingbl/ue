<template lang="html">
<div class="page-login">
    <div class="auth_panel flex-box" v-show="site_name&&platform_name">
        <div class="auth_item">
            <img :src="site_icon" />
            <!-- <span>{{site_name}}</span> -->
        </div>
        <div class="auth_icon">
            <i class="iconfont">&#xe6ff;</i>
        </div>
        <div class="auth_item">
            <img :src="platform_icon" />
            <!-- <span>{{platform_name}}</span> -->
        </div>
    </div>
    <tab :line-width="2" custom-bar-width="60px" v-if="loginstatus==0">
        <tab-item selected @click.native="switchStatus('smsLogin')">短信登录</tab-item>
        <tab-item @click.native="switchStatus('pwdLogin')">密码登录</tab-item>
    </tab>
    <!-- 短信登录 -->
    <div v-if="loginType=='smsLogin'">
        <!-- 发送验证码 -->
        <div class="" v-if="step==1">
            <div class="clear" v-if="loginstatus==0"></div>
            <group v-if="loginstatus==0">
                <x-input ref="mobile" title="+86" name="mobile" placeholder="请输入手机号码" type="number" keyboard="number" is-type="china-mobile" v-model="form.mobile" :required="true"></x-input>
                <x-input ref="verifycode"  class="weui-vcode"  placeholder="请输入验证码" type="number" keyboard="number"  v-model="form.verifycode" :min="6" :required="true">
                    <x-button v-if="!ifgetcode"   slot="right" plain  type="primary" mini v-on:click.native="getCode()">发送验证码</x-button>
                    <x-button v-if="ifgetcode" slot="right" plain  type="primary" mini v-on:click.native="endtimer()">重新发送{{countimer}}s'</x-button>
                </x-input>
            </group>
            <box gap="40px 10px">
                <x-button type="primary" v-on:click.native="login()">授权登录</x-button>
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
                <x-button type="primary" style="border-radius:99px;" v-on:click.native="register()">注册并授权</x-button>
            </box>
            <div class="backlogin" style="text-align:center;" v-on:click="backlogin()">
                返回登录页
            </div>
        </div>
    </div>
    <!-- 密码登录 -->
    <div v-if="loginType=='pwdLogin'">
        <div class="clear"></div>
        <group>
            <x-input name="username" placeholder="用户名/手机号"  v-model="form.username" ref="username" :required="true"></x-input>
            <x-input type="password" name="password" placeholder="请输入密码"  v-model="form.password" ref="password" :required="true"></x-input>
        </group>
        <box gap="30px 10px 20px">
            <x-button type="primary" v-on:click.native="loginForPwd()">授权登录</x-button>
        </box>
        <div class="backlogin" style="text-align:center;" v-on:click="toResetPwd()">
            找回密码
        </div>
    </div>
</div>   
</template>
<script>
import { Flexbox, FlexboxItem, Divider, Tab, TabItem } from "vux";
import { Drawer } from "vux";
import { mapGetters, mapActions } from "vuex";
import {
  setCookie,
  getCookie,
  deleteCookie
} from "../../assets/js/cookieHandle";
export default {
    components: {
        //stepone:() => import('@/views/login/inc/stepone'),
        Flexbox,
        FlexboxItem,
        Divider,
        Drawer,
        Tab,
        TabItem
    },
     data() {
        return {
            step: 1,
            countimer: "",
            ifgetcode: false,
            wait: null,
            loginstatus: 1,
            jumpurl: "",
            platform_name: "",
            platform_icon: "",
            site_name: "",
            site_icon: "",
            platform_id: "",
            security_params: "",
            form: {
                mobile: "",
                verifycode: "",
                username: "",
                password: "",
                check_password: ""
            },
            loginType: "smsLogin"
        };
    },
    mounted() {
        this.security_params = this.$router.currentRoute.query["_security_params"];
        this.platform_id = this.$router.currentRoute.query["_platform_id"];
        if (this.security_params && this.platform_id) {
            this.getAuth(this.platform_id, this.security_params);
        } else {
            this.$router.replace("/");
        }
        //this.$vux.toast.text('登陆成功')
    },
    methods: {
        ...mapActions(["setToken"]),
        getAuth(_platform_id, _security_params) {
            this.$http
            .post("/api/app.user/user/auth", {
                platform_id: _platform_id,
                security_params: _security_params
            })
            .then(res => {
                if (res.errcode == "0") {
                    this.loginstatus = res.data.loginstatus;
                    this.jumpurl = res.data.jumpurl;
                    this.platform_icon = res.data.platform_icon;
                    this.platform_name = res.data.platform_name;
                    this.site_icon = res.data.site_icon;
                    this.site_name = res.data.site_name;
                } else {
                    this.$router.replace("/");
                }
            })
            .catch(err => {
                console.log(err);
            });
        },
        getCode() {
            this.$refs.mobile.validate();
            if (this.$refs.mobile.valid && this.form.mobile != "") {
                this.$http
                .post("/api/app.util/sms/send", {
                    mobile: this.form.mobile,
                    type: 0
                })
                .then(res => {
                    this.$vux.toast.text("发送成功");
                    this.$vux.toast.text(res.message);
                    console.log(res);
                    this.counttime(60);
                })
                .catch(err => {
                    this.$vux.toast.text(err.message);
                    console.log(err);
                    if (err.errcode == 10000) {
                    this.ifgetcode = true;
                    this.countimer = err.data.second;
                    this.counttime(err.data.second);
                    }
                    //  this.Toast(err || '网络异常，请求失败');
                });
            } else {
                this.$refs.mobile.forceShowError = true;
                this.$vux.toast.text("请输入正确的手机号");
            }
        },
        login() {
            if (this.loginstatus == 1) {
                location.href = this.jumpurl;
            } else {
                this.$refs.mobile.validate();
                this.$refs.verifycode.validate();
                if (this.$refs.mobile.valid && this.$refs.verifycode.valid) {
                var formdata = this.form;
                formdata.platform_id = this.platform_id;
                formdata.security_params = this.security_params;
                formdata.dologin = 1;
                formdata.login_type = this.loginType;
                this.$http
                    .post("/api/app.user/user/auth", formdata)
                    .then(res => {
                    console.log(res.errcode);
                    if (res.errcode == "0") {
                        //登录成功
                        this.setToken(res.data._user_token);
                        localStorage.setItem(
                        "token",
                        JSON.stringify(res.data._user_token)
                        );
                        this.loginstatus = res.data.loginstatus;
                        this.jumpurl = res.data.jumpurl;
                        this.platform_icon = res.data.platform_icon;
                        this.platform_name = res.data.platform_name;
                        this.site_icon = res.data.site_icon;
                        this.site_name = res.data.site_name;
                        this.step = 1;
                        location.href = this.jumpurl;
                    }
                    })
                    .catch(err => {
                    // this.$vux.toast.text(err.message);
                    if (err.errcode == "50001") {
                        //未注册
                        this.step = 2;
                    } else {
                        this.$vux.toast.text(err.message);
                    }
                    console.log(err);
                    //  this.Toast(err || '网络异常，请求失败');
                    });
                } else {
                this.$refs.mobile.forceShowError = true;
                this.$refs.verifycode.forceShowError = true;
                }
            }
        },
        register() {
            this.$refs.username.validate();
            this.$refs.password.validate();
            this.$refs.check_password.validate();
            if (
                this.$refs.username.valid &&
                this.$refs.password.valid &&
                this.$refs.check_password.valid
            ) {
                this.$http
                .post("/api/app.user/user/register", this.form)
                .then(res => {
                    console.log(res);
                    if (res.errcode == "0") {
                    //登录成功
                    this.setToken(res.data._user_token);
                    localStorage.setItem(
                        "token",
                        JSON.stringify(res.data._user_token)
                    );
                    this.step = 1;
                    this.getAuth(this.platform_id, this.security_params);
                    }
                })
                .catch(err => {
                    console.log(err);
                    if (err.errcode) {
                    this.$vux.toast.text(err.message);
                    }
                    if (err.errcode == 50010) {
                    this.step = 1;
                    }
                    //  this.Toast(err || '网络异常，请求失败');
                });
            } else {
                this.$refs.username.forceShowError = true;
                this.$refs.password.forceShowError = true;
                this.$refs.check_password.forceShowError = true;
                if (!this.form.check_password) {
                this.$refs.check_password.errors.equal = "输入不一致";
                this.$refs.check_password.getError();
                }
            }
        },
        loginForPwd() {
            if (this.loginstatus == 1) {
                location.href = this.jumpurl;
            } else {
                this.$refs.username.validate();
                this.$refs.password.validate();
                if (this.$refs.username.valid && this.$refs.password.valid) {
                var formdata = this.form;
                formdata.platform_id = this.platform_id;
                formdata.security_params = this.security_params;
                formdata.dologin = 1;
                formdata.login_type = this.loginType;
                this.$http
                    .post("/api/app.user/user/auth", formdata)
                    .then(res => {
                    console.log(res.errcode);
                    if (res.errcode == "0") {
                        //登录成功
                        this.setToken(res.data._user_token);
                        localStorage.setItem(
                        "token",
                        JSON.stringify(res.data._user_token)
                        );
                        this.loginstatus = res.data.loginstatus;
                        this.jumpurl = res.data.jumpurl;
                        this.platform_icon = res.data.platform_icon;
                        this.platform_name = res.data.platform_name;
                        this.site_icon = res.data.site_icon;
                        this.site_name = res.data.site_name;
                        this.step = 1;
                        location.href = this.jumpurl;
                    }
                    })
                    .catch(err => {
                    if (err.errcode == "50001") {
                        this.$vux.toast.text("您还未注册，请使用短信登录完成注册");
                    } else {
                        this.$vux.toast.text(err.message);
                    }
                    console.log(err);
                    //  this.Toast(err || '网络异常，请求失败');
                    });
                } else {
                this.$refs.username.forceShowError = true;
                this.$refs.password.forceShowError = true;
                }
            }
        },
        backlogin() {
            this.step = 1;
        },
        toResetPwd() {
            this.$router.push({ path: "/login/pwdreset" });
        },
        counttime(TIME_COUNT) {
            if (!this.wait) {
                this.countimer = TIME_COUNT;
                this.ifgetcode = true;
                this.wait = setInterval(() => {
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
        endtimer() {
            this.$vux.toast.text("请于倒计时结束后再获取验证码");
        },
        switchStatus(type) {
            this.loginType = type;
        }
    }
};
</script>

<style lang="less">
@import "../../assets/css/variable.less";
.auth_panel {
    background: url(../../assets/images/auth_banner.jpg) no-repeat top center;
    background-size: 100%;
    height: 8.25rem;
    justify-content: center;
    
    .auth_item{
        width: 3.875rem;
        border:3px solid #9abdeb;
        border-radius: 10px;
        overflow: hidden;
        img {
            width: 100%;
            height: 100%;
            display: block;
        } 
    }
    .auth_icon{
        margin: 0 1.875rem;
        i{
            font-size: 1.625rem;
            color: #fff;
        }
    }
}
.clear {
    clear: both;
}
.backlogin {
    color: #628cf8;
}
.page-login {
    height: 100%;
    .login-tit {
        padding: 0 10px;
        font-size: 1rem;
        line-height: 1.6rem;
        padding-top: 2rem;
        background: #fff;
    }
    .login-line {
        padding: 0 10px;
        font-size: 0.8rem;
        height: 1.6rem;
        line-height: 1.6rem;
        &:after {
            display: block;
            content: "";
            clear: both;
        }
        .login-left {
            float: left;
        }
        .login-right {
            float: right;
        }
    }
    .l-tit {
        padding-top: 2rem;
        line-height: 1.6rem;
    }
    .user-portrait {
        width: 5rem;
        height: 5rem;
        overflow: hidden;
        border-radius: 50%;
        img {
            display: block;
            width: 100%;
            height: 100%;
        }
    }
}
</style>
