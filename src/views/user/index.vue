<template lang="html">
<div class="page-user-center">
        <div class="center-banner">
            <div class="modify" @click="changeName">
                <i class="iconfont">&#xe6f9;</i>
            </div>
            <router-link class="setting" :to="{path: '/user/setting'}">
                <img src="@/assets/images/user_setting.png" alt="">
            </router-link>
            <div class="user-info">

                    <div class="user-portrait">
                        <img src="@/assets/images/avatar.png" alt="" v-if="useravatar==''">
                        <img v-bind:src="useravatar" v-else>
                        <div class="upload-btn" v-if="!$store.state.init.is_app">
                            <vue-core-image-upload
                                    class="btn btn-primary"
                                    :crop="false"
                                    @imageuploaded="imageuploaded"
                                    :data="params"
                                    :max-file-size="5242880"
                                    inputOfFile="file"
                                    :url="uploadUrl"
                                    text="">
                            </vue-core-image-upload>
                        </div>
                        <div class="upload-btn" v-else @click="AppUpload"></div>
                    </div>
                    <div class="user-name">
                        <div class="name">{{username}}</div>

                    </div>
                    <div class="user-modile">{{usermobile|phonereplace}}</div>
            </div>

        </div>
        <div class="find-box">
            <div class="find-block flex-box">
                <router-link class="item flex-box" :to="{path:'/user/selfmoney'}">
                    <div class="user_icon">
                        <img src="@/assets/images/user_assets.png" alt="">
                    </div>
                    <div class="item-text">我的资产</div>
                </router-link>
                <router-link class="item flex-box" :to="{path:'/wallet'}">
                    <div class="user_icon">
                        <img src="@/assets/images/user_wallets.png" alt="">
                    </div>
                    <div class="item-text">我的钱包</div>
                </router-link>
                <div class="item flex-box"  @click="jubaopen">
                    <div class="user_icon">
                        <img src="@/assets/images/user_cornucopia.png" alt="">
                    </div>
                    <div class="item-text">聚宝盆</div>
                </div>
                <div class="item flex-box" @click="tis_btn">
                    <div class="user_icon">
                        <img src="@/assets/images/xinyong.png" alt="">
                    </div>
                    <div class="item-text">信用值</div>
                </div>
                <div class="item flex-box" @click="open_scan">
                    <div class="user_icon">
                        <img src="@/assets/images/user_sendout.png" alt="">
                    </div>
                    <div class="item-text">发&nbsp;&nbsp;送</div>
                </div>
                <router-link class="item flex-box" :to="{path:'/transfer/receive'}">
                    <div class="user_icon">
                        <img src="@/assets/images/user_receive.png" alt="">
                    </div>
                    <div class="item-text">接&nbsp;&nbsp;收</div>
                </router-link>
                <div class="item flex-box" @click="turnToSend">
                    <div class="user_icon">
                        <img src="@/assets/images/user_turnout.png" alt="">
                    </div>
                    <div class="item-text">转&nbsp;&nbsp;出</div>
                </div>
                <div class="item flex-box" @click="turn_shop()">
                    <div class="user_icon">
                        <img src="@/assets/images/user_shopping.png" alt="">
                    </div>
                    <div class="item-text">商&nbsp;&nbsp;城</div>
                </div>
                <div class="item flex-box" @click="liutong()">
                    <div class="user_icon">
                        <img src="@/assets/images/user_deals.png" alt="">
                    </div>
                    <div class="item-text">令牌流通</div>
                </div>
                <router-link class="item flex-box" :to="{path: '/circulate/index'}" >
                    <div class="user_icon">
                        <img src="@/assets/images/user_circulation.png" alt="">
                    </div>
                    <div class="item-text">资产流通</div>
                </router-link>
                <div class="item flex-box" @click="zjg">
                    <div class="user_icon">
                        <img src="@/assets/images/zjg.png" alt="">
                    </div>
                    <div class="item-text">中奖购</div>
                </div>
                <div class="item flex-box" @click="tis_btn">
                    <div class="user_icon">
                        <img src="@/assets/images/vote.png" alt="">
                    </div>
                    <div class="item-text">投&nbsp;&nbsp;票</div>
                </div>
                <div class="item flex-box" @click="turn_node()">
                    <div class="user_icon">
                        <img src="@/assets/images/user_node.png" alt="">
                    </div>
                    <div class="item-text">行业节点</div>
                </div>
                <div class="item flex-box" @click="society()">
                    <div class="user_icon">
                        <img src="@/assets/images/user_build.png" alt="">
                    </div>
                    <div class="item-text">社群建设</div>
                </div>
                <div class="item flex-box" @click="credit">
                    <div class="user_icon">
                        <img src="@/assets/images/user_credit.png" alt="">
                    </div>
                    <div class="item-text">我的信用</div>
                </div>
                <div class="item flex-box"  @click="zc">
                    <div class="user_icon">
                        <img src="@/assets/images/choubao.png" alt="">
                    </div>
                    <div class="item-text">筹&nbsp;&nbsp;宝</div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import {Confirm,Group, XSwitch, XButton, TransferDomDirective as TransferDom } from 'vux'
import { Flexbox, FlexboxItem, Divider } from 'vux'
import { setCookie, getCookie, deleteCookie,clearCookie } from "../../assets/js/cookieHandle";
import VueCoreImageUpload from 'vue-core-image-upload'
import cookie from '../../utils/cookie'

export default {
    directives: {
        TransferDom
    },
    components: {
        Confirm,
        Group,
        XSwitch,
        XButton,
        Flexbox,
        FlexboxItem,
        Divider,
        'vue-core-image-upload': VueCoreImageUpload
    },
    data () {
        return {
            show5: true,
            useravatar:"",
            usermobile:"",
            username:"",
            editname:"",
            uploadUrl:"/api/app.util/upload",
            address:'',
            params:{
                type:"avatar",
                _user_token:this.$store.state.token
            },
            vc_total:0,
            loading:true,
            vc_normal:0,
            is_node:0,
            is_society:0,
            j_order:0,
            ban:0
        }
    },
    mounted () {
        this.getUserinfo();
        const _this = this
        window.CutCallBack =function (base64Str) {
            _this.CutCallBack(base64Str)
        };
        window.js_qr_code_scan = function(qr_code){
            if(qr_code.substr(0,4)=="http")
            {
                App.open_type('{"url":"'+qr_code+'"}');
            }else{
                const id = qr_code;
                _this.$http.post('/api/app.user/transfer/userexrate',{id:id}).then(res => {
                    _this.$router.push({path: '/transfer/send/'+id})
                }).catch(err => {
                    _this.$vux.toast.text('二维码内容错误');
                });
            }
        };

    },
    methods:{
        onConfirm5(){},
        onShow5(){},
        getUserinfo(){
            this.$http.post('/api/app.user/account/info',{}).then(res => {
                this.$store.state.init.usd_rate = res.data.usd_rate;
                this.usermobile=res.data.account_info.mobile;
                this.username=res.data.account_info.username;
                this.useravatar=res.data.account_info.avatar;
                this.address = res.data.account_info.address
                this.vc_normal = res.data.account_info.vc_normal;
                this.$store.state.init.sugar_auth = res.data.account_info.sugar_auth;
                this.vc_total =res.data.account_info.vc_total;
                this.is_society= res.data.account_info.is_society;
                this.is_node= res.data.account_info.is_node
                this.$store.state.is_society =  res.data.account_info.is_society;
                this.$store.state.is_node =  res.data.account_info.is_node;
                this.loading  = false;
                this.level = res.data.account_info.level;
                this.j_order = res.data.account_info.j_order;
                this.b2c_url = res.data.account_info.b2c_url;
                this.ban = res.data.account_info.ban;
                this.$store.state.b2c_url_member =  res.data.account_info.b2c_url_member;
                this.$store.state.self_wallet_auth = res.data.account_info.self_wallet_auth
                cookie.setCookie('uid', res.data.account_info.accid)
                cookie.setCookie('sdktoken',res.data.account_info.token)
            }).catch(err => {
                this.loading  = false;
                if (err.errcode) {
                    this.$vux.toast.text(err.message);
                }
                console.log(err);
                //  this.Toast(err || '网络异常，请求失败');
            });
        },
        changeName(){
            const _this = this;
            var _name= $('.name').html();
            this.$vux.confirm.prompt('请输入昵称', {
                title: '修改名称',
                inputAttrs: {
                    type: 'text',
                    maxlength:8,

                },
                //  close-on-confirm:flase,
                onShow () {
                    console.log('promt show')
                    _this.$vux.confirm.setInputValue(_name);
                },
                onHide () {
                    console.log('prompt hide')
                },
                onCancel () {
                    console.log('prompt cancel')
                },
                onConfirm (msg) {
                    _this.$http.post('/api/app.user/account/editinfo',{username:msg}).then(res => {
                        _this.$vux.toast.text(res.message);
                        _this.username=msg;
                        console.log(res);
                    }).catch(err => {
                        if (err.errcode) {
                            _this.$vux.toast.text(err.message);
                        }
                        console.log(err);
                        //  this.Toast(err || '网络异常，请求失败');
                    });
                    //alert(msg)
                }
            });
        },
        logout(){
            let _this = this;
            this.$vux.confirm.show({
                title: '确定要退出吗？',
                onCancel(){},
                onConfirm(){
                    console.log("退出");
                   // setCookie("token","");
                    localStorage.removeItem('token');
                    localStorage.clear();
                    clearCookie();
                    setCookie("isSign","");
                    _this.$store.commit("reset_state");
                    _this.$vux.toast.text("退出成功");
                    _this.$router.push({path:'/login'});
                }
            })
        },
        savename(){
            this.$http.post('/api/app.user/account/editinfo',{username:this.editname}).then(res => {
                console.log(res);
            }).catch(err => {
                if (err.errcode) {
                    this.$vux.toast.text(err.message);
                }
                console.log(err);
                //  this.Toast(err || '网络异常，请求失败');
            });
        },
        imageuploaded(res) {
            if (res.errcode == 0) {
                this.useravatar = res.data.file_url;
            }
        },
        turnPay(){
            if(this.address){
                this.$router.push({path: '/user/setpay'})
            }else{
                this.$vux.toast.text('请先创建钱包');
            }
        },

        AppUpload(){
            App.CutPhoto('{"w":"200","h":"200"}')
        },
        CutCallBack(base64Str){
            var formData = new FormData();
            formData.append('type','avatar');
            formData.append('file',convertBase64UrlToBlob(base64Str));
            let config = {
                header:{
                    'Content-Type': 'multipart/form-data'
                }
            }
            this.$http.post('/api/app.util/upload', formData,config)
                .then(res=>{
                    if (res.errcode == 0) {
                        this.useravatar = res.data.file_url;
                    }
                })
        },
      turn_shop(){
            if(this.loading)
            {
              return false;
            }
            if(this.ban){
              this.$vux.toast.text('资产不足');
              return false;
            }
            if(this.b2c_url)
            {
              App.open_type('{"url":"'+this.b2c_url+'"}');
            }else{
              this.tis_btn();
            }
          },
        tis_btn(){
            this.$vux.toast.text('敬请期待');
        },
        open_scan(){
            if(this.vc_total<=this.$store.state.init.transfer_over_limit)
            {
                this.$vux.toast.text(`资产超出${this.$store.state.init.transfer_over_limit}部分才可以转出`);
            }else{
                try {
                    App.qr_code_scan();
                }catch (e){
                    this.$vux.toast.text('请在App端使用');
                }

            }

        },
        turnToSend(){
            if(parseFloat(this.vc_total)<=parseFloat(this.$store.state.init.transfer_over_limit))
            {
                this.$vux.toast.text(`资产超出${this.$store.state.init.transfer_over_limit}部分才可以转出`);
            }else{
                this.$router.push({path:'/transfer/to'});
        }},
        society(){
            if(this.loading && this.$store.state.is_society)
            {
                return false;
            }
            if(this.is_society || this.$store.state.is_society){
                this.$router.push({path:'/society/calc'});
            }else{
                this.$router.push({path:'/society/choice'});
            }
        },
        turn_node(){
            if(this.loading && !this.$store.state.is_node)
            {
                return false;
            }
            if(this.is_node || this.$store.state.is_node){
                this.$router.push({path:'/society/calc_node'});
            }else{
                this.$router.push({path:'/society/apply_node'});
            }
        },
        jubaopen(){
          if(this.loading)
          {
            return false;
          }
          if(this.ban){
            this.$vux.toast.text('资产不足');
            return false;
          }
          if(parseFloat(this.vc_normal)<=parseFloat(this.$store.state.init.jubaopen) && this.j_order==0)
          {
            this.$vux.toast.text(`资产不足,需要达到${this.$store.state.init.jubaopen}`);
          }else{
            this.$router.push({path:'/finance'});
          }
        },
        zc(){
          if(this.loading)
          {
            return false;
          }
          if(this.ban){
            this.$vux.toast.text('资产不足');
            return false;
          }
          if(parseFloat(this.vc_normal)<parseFloat(this.$store.state.init.crowd_limit))
          {
            this.$vux.toast.text(`资产不足,需要达到${this.$store.state.init.crowd_limit}`);
          }else{
            this.$router.push({path:'/zc'});
          }
        },
          zjg(){
            if(this.loading)
            {
              return false;
            }
            if(this.ban){
              this.$vux.toast.text('资产不足');
              return false;
            }
            this.$router.push({path:'/crowd_buy'});
          },
        liutong(){
          if(this.loading)
          {
            return false;
          }
          if(this.ban){
            this.$vux.toast.text('资产不足');
            return false;
          }
          if(parseFloat(this.vc_normal)<parseFloat(this.$store.state.init.crowd_limit))
          {
            this.$vux.toast.text(`资产不足,需要达到${this.$store.state.init.crowd_limit}`);
          }else{
            this.$router.push({path:'/token_otc'});
          }
        },
        credit(){
            if(this.loading)
            {
                return false;
            }
            if(this.level<=0)
            {
                this.$vux.toast.text('需要社群身份');
            }else{
                this.$router.push({path:'/credit/choice'});
            }
        }
    }
}
</script>

<style lang="less">
    @import '../../assets/css/variable.less';
    .page-user-center{
        .center-banner{
            background: url(../../assets/images/user_index_banner.png);
            background-repeat: no-repeat;
            background-position: top center;
            background-color: #fff;
            background-size: 100%;
            height: 51.735vw;
            position: relative;
            .modify{
                position: absolute;
                width: 2.5rem;
                height: 2.5rem;
                line-height: 2.5rem;
                text-align: center;
                border-radius: 50%;
                background: -webkit-linear-gradient(top, #2882d2, #1f69d5);
                background: linear-gradient(to bottom, #2882d2, #1f69d5);
                top: 25%;
                right: 2rem;
                box-shadow: 0 2px 5px 0 rgba(0,0,0,0.2);
                .iconfont{
                    width: 1.25rem;
                    font-size: 1.25rem;
                    display: block;
                    font-weight: 500;
                    color: #fff;
                    margin: 0 auto;

                }
                .modify-btn{
                    position: absolute;
                    top:0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    opacity: 0;
                    width: 100%;
                    height: 100%;
                }
            }
            .setting{
                position: absolute;
                left: 2.625rem;
                bottom: 31%;
                width: 2.375rem;
                height: 2.375rem;
                z-index: 2;
                img{
                    width: 100%;
                    height: 100%;
                    display: block;
                }
            }
        }
        .user-info{
            position: absolute;
            padding: 2.5rem 0 0.25rem;
            text-align: center;
            width: 100%;
            left: 0;
            top: 46%;
            z-index: 1;
            .user-portrait{
                width: 5rem;
                height: 5rem;
                position: absolute;
                top: -2.5rem;
                transform: translateX(-50%);
                left: 50%;
                box-sizing: border-box;
                padding: 4px;
                background: #fff;
                border-radius: 2.5rem;
                box-shadow: 0 2px 15px 0 rgba(33, 93, 247, 0.2);
                img{
                    width: 100%;
                    height: 100%;
                    border-radius: 50%;
                    display: block;
                }
            }
            .user-name{
                margin-top: 0.5rem;
                display: -webkit-box;
                display: -ms-flexbox;
                display: flex;
                display: -webkit-flex;
                box-align: center;
                -webkit-box-align: center;
                -ms-flex-align: center;
                align-items: center;
                font-size: 0.875rem;
                line-height: 1.5rem;
                height: 1.5rem;
                font-weight: bold;
                color: #5d5d61;
                .name{
                    margin: 0 auto;
                }
            }

            .user-modile{
                font-size: @fs-middle;
                font-family: arial;
                line-height: 1.25rem;
                height: 1.25rem;
                margin-top: 0.25rem;
                color: #888;
            }
        }
        .user-list-box{
            margin-top: 10px;
            .weui-cells{
                margin: 0;
                &:before{
                    display: none;
                }
                &:after{
                    display: none;
                }
            }
            .weui-cell{
                p{
                    font-size: @fs-middle;
                    color: #5d5d61;
                }
                .iconfont{
                    font-size: 20px;
                }
                .icon_assets{
                    color: #69a5f5;
                }
                .icon_wallet{
                    color: #ff5853;
                }
                .icon_payset{
                    color: #f1a30b;
                }
                .icon_accset{
                    color: #68a4f2;
                }
                .icon_back{
                    color: #f35a5e;
                }
            }
        }
        .find-box {
            background: #fff;
            margin-top: 0.625rem;
            &:first-child{
                margin: 0;
            }
            .title {
                padding: 0 0.9375rem;
                line-height: 2.5rem;
                font-size: 0.875rem;
                position: relative;
                &::after{
                    position: absolute;
                    content: '';
                    bottom: -1px;
                    display: block;
                    width: 100%;
                    height: 1px;
                    background: #eee;
                    left: 0;
                }
            }
            .find-block {
                flex-wrap: wrap;
                border-bottom: 1px solid #eee;
                .item {
                    width: 25%;
                    height: 6.75rem;
                    color: #363840;
                    flex-direction: column;
                    justify-content: center;
                    border-right: 1px solid #eee;
                    border-bottom: 1px solid #eee;
                    &:nth-child(4n+4) {
                        border-right: none;
                    }
                    .user_icon {
                        width: 2.5625rem;
                        height: 2.5625rem;
                        margin: 0 auto 0.5rem;
                        img{
                            display: block;
                            width: 100%;
                            height: 100%;
                        }
                    }
                    .item-num{
                        color: #999999;
                        font-size: 12px;
                        padding-top: 2px;
                    }
                }
            }
        }
        .upload-btn{
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5rem;
            .btn{
                height: 100%;
            }
        }
    }
    @media screen and (max-width: 320px){
        .page-user-center .center-banner .setting{
            top: 47%;
        }
    }
</style>
