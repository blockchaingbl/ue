<template lang="html">
    <div class="page-login">
        <group>

            <div class="login-mobile flex-box">
                <popup-picker title="" :data="code_list" :columns="3" v-model="mobile_code" ref="picker"></popup-picker>
                <x-input class="mobile-input flex-1" ref="mobile" title="" name="mobile" placeholder="请输入手机号码" type="number" keyboard="number" v-model="form.mobile" :required="true"></x-input>
            </div>
            <x-input ref="verifycode"  class="weui-vcode"  placeholder="请输入验证码"   type="number" keyboard="number"  v-model="form.verifycode" :min="6" :required="true">
                <x-button v-if="!ifgetcode"   slot="right" plain  type="primary" mini v-on:click.native="getCode()">发送验证码</x-button>
                <x-button v-if="ifgetcode" slot="right" plain  type="primary" mini v-on:click.native="endtimer()">重新发送{{countimer}}s'</x-button>
            </x-input>
            <x-input type="password" title="新的密码" name="password" placeholder="请输入密码" v-model="form.password" :min="6" :max="16" ref="password" :required="true"></x-input>
            <x-input type="password" title="确认密码" name="check_password" placeholder="请输入密码" v-model="form.check_password" :min="6" :max="16" :equal-with="form.password" ref="check_password" :required="true"></x-input>
        </group>
        <box gap="30px 10px 20px">
            <x-button type="primary" v-on:click.native="resetPwd()">确认</x-button>
        </box>
        <div class="backlogin" style="text-align:center;" v-on:click="backlogin()">
            返回登录页
        </div>
    </div>
</template>
<script>
import { Flexbox, FlexboxItem, Divider, Tab, TabItem ,PopupPicker} from 'vux'
import {Drawer} from 'vux'
import { mapGetters ,mapActions} from 'vuex';
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
        PopupPicker,
    },
    data () {
        return {
            countimer:"",
            ifgetcode:false,
            wait:null,
            form: {
                mobile: '',
                verifycode: '',
                password:'',
                check_password:''
            },
            mobile_code: ['+86'],
            code_list:[]
        }
    },
    mounted () {
        this.$http.post('api/app.util/init/get_mobile_code',{}).then(res=>{
            this.code_list = res.data.code_list;
        })
    },
    methods:{
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
                    this.counttime(60);
                }).catch(err => {
                    this.$vux.toast.text(err.message);
                    if (err.errcode==10000) {
                        this.ifgetcode=true;
                        this.countimer=err.data.second;
                        this.counttime(err.data.second);
                    }
                    //  this.Toast(err || '网络异常，请求失败');
                });
            }else{
                this.$refs.mobile.forceShowError = true;
                this.$vux.toast.text("请输入正确的手机号");
            }
        },
        resetPwd(){
            this.$refs.mobile.validate();
            this.$refs.verifycode.validate();
            this.$refs.password.validate();
            this.$refs.check_password.validate();
            if(this.$refs.mobile.valid&&this.$refs.verifycode.valid&&this.$refs.password.valid&&this.$refs.check_password.valid)
            {
                this.$http.post('/api/app.user/user/resetpwd',this.form).then(res => {
                    console.log(res.errcode);
                    if (res.errcode=="0") {
                        this.$vux.toast.text("重置成功");
                        this.$router.go(-1);
                    }
                }).catch(err => {
                    this.$vux.toast.text(err.message);
                    console.log(err);
                    //  this.Toast(err || '网络异常，请求失败');
                });
            }else{
                this.$refs.mobile.forceShowError = true;
                this.$refs.verifycode.forceShowError = true;
                this.$refs.password.forceShowError = true;
                this.$refs.check_password.forceShowError = true;
                if(!this.form.check_password)
                {
                    this.$refs.check_password.errors.equal = '输入不一致';
                    this.$refs.check_password.getError();
                }
            }
        },
        backlogin(){
            this.$router.push({path:'/login'});
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
        }
    }
}
</script>

<style lang="less">
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
    }

</style>
