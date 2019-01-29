<template lang="html">
<div class="invitation">
    <div class="banner">
        <img src="@/assets/images/invitation_banner.jpg" alt="">
    </div>
    <div class="invita-box">
        <div class="invita-info flex-box">
            <div class="info-item flex-1">
                <router-link   to="/relations/myinvite" >
                <div class="item-title">邀请好友数</div>
                <div class="item-decs">{{invite_num}}</div>
                </router-link>
            </div>
            <div class="info-item flex-1">
                <div class="item-title">累计获得算力</div>
                <div class="item-decs item-blod">{{cp_invite_total}}</div>
            </div>
        </div>
    </div>
    <div class="invita-text">奖励规则</div>
    <div class="invita-text">每邀请一个好友完成注册，您就可以获得{{invite_cp}}算力 </div>
    <div class="invita-bottom" v-on:click='open_invitation()'>
        <div class="bottom-title">生成邀请卡</div>
        <div class="bottom-decs">截图发给好友并叮嘱其注册时填写邀请码</div>
    </div>

    <div v-transfer-dom>
        <popup v-model="showpopup1" height="100%" class="invite-pup">
            <div class="invitbox">
                <h3 class="invite-title">{{app_name}}</h3>
                <div class="invite-decs">{{app_describe}}</div>
                <div class="invite-content">
                    <div class="invite-head">
                        <div class="invi-card-title">我的邀请码</div>
                        <div class="invite-code">{{invite_code}}</div>
                    </div>
                    <div class="invite-user flex-box">
                        <div class="user-info">我是“{{username}}”，{{app_name}}第{{register_no}}号居民,我在{{app_name}}等你，不见不散</div>
                        <qrcode :value="app_download_url" type="img" class="user-qrcode"></qrcode>
                        <div class="bottom-text vux-1px-t">
                            <div class="bottom-txt">扫码下载{{app_name}}APP</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="popup1" v-on:click='close_invitation()'></div>
        </popup>
    </div>
</div>
</template>
<script>

import { TransferDom, Popup,Qrcode} from 'vux'

export default {
    directives: {
        TransferDom
    },
    components: {
        Popup,
        Qrcode
    },
    data () {
        return {
            showpopup1:false,
            invite_num:0,
            cp_invite_total:0,
            invite_cp:0,
            app_download_url: "暂无下载链接",
            invite_code:"",
            app_name:"",
            username:"",
            register_no:"",
            app_describe:"",

        }
    },
    mounted () {
        this.getUserinfo();
        this.invite_cp=this.$store.state.init.invite_cp;
        this.app_name=this.$store.state.init.app_name;
        this.app_describe=this.$store.state.init.app_describe;
        this.app_download_url=this.$store.state.init.app_download_url;
    },
    methods:{
        getUserinfo(){
            this.$http.post('/api/app.user/account/info',{}).then(res => {
                this.invite_num=res.data.account_info.invite_num;
                this.cp_invite_total=res.data.account_info.cp_invite;
                this.invite_code=res.data.account_info.invite_code;
                this.username=res.data.account_info.username;
                this.register_no=res.data.account_info.register_no;
            }).catch(err => {
                if (err.errcode) {
                    this.$vux.toast.text(err.message);
                }
                console.log(err);
                //  this.Toast(err || '网络异常，请求失败');
            });
        },
        open_invitation(){
            this.showpopup1=true;
        },
        close_invitation(){
            this.showpopup1=false;
        }
    }
}
</script>

<style lang="less">
    @import '~vux/src/styles/1px.less';
    @import '../../assets/css/variable.less';
    .invitation{
        font-size: @fs-small;
        color: #4c4c51;
        .banner{
            width: 100%;
            img{
                width: 100%;
                display: block;
            }
        }
        .invita-box{
            height: 5.375rem;
            padding-top: 0.1px;
            .invita-info{
                margin: -1.25rem 0.625rem 0;
                height: 5.125rem;
                background: #fff;
                border-radius: 4px;
                box-shadow: 0 5px 10px 0 rgba(0, 0, 0, 0.1);
                position: relative;
            }
        }
        .info-item {
            text-align: center;

            .item-title {
                line-height: 1.5rem;
            }
            .item-decs {
                font-size: @fs-biger;
                line-height: 1.625rem;
                font-family: arial;
            }
            .item-blod{
                font-weight: bold;
                color: #6b94f8;
            }
        }
        .invita-text{
            padding-left: 2.25rem;
            line-height: 1.75rem;
        }
        .invita-bottom{
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 3.625rem;
            color: #fff;
            text-align: center;
            background:-webkit-linear-gradient(left,#62b9d4,#6970e9);
            background:linear-gradient(to right,#62b9d4,#6970e9);
            padding-top: 0.5rem;
            .bottom-title{
                font-size: @fs-normal;
                line-height: 1.4375rem;
                letter-spacing: 0.4rem;
            }
            .bottom-decs{
                font-size: @fs-small;
                line-height: 1.1875rem;
                letter-spacing: 0.2rem;
            }
        }
    }
    .vux-popup-dialog.invite-pup{
        &::-webkit-scrollbar{
          display:none;
        }
        background: #4964cc;
        .invitbox{

            background: #4964cc;
            background-image: url(../../assets/images/invita_card_bg.jpg);
            background-repeat: no-repeat;
            background-position: top center;
            background-size: 100%;
            padding: 0.9375rem 0.625rem 1.25rem;
            text-align: center;
            .invite-title {
                font-size: 1.5625rem;
                line-height: 2.1875rem;
                color: #fff;
                font-weight: bold;
            }
            .invite-decs{
                font-size: @fs-middle;
                color: #fff;
                line-height: 1.5rem;
                margin-bottom: 1rem;
            }
            .invite-content{
                background-color: #fff;
                border-radius: 0.25rem;

                position: absolute;
                left: 0.875rem;
                right: 0.875rem;
                bottom: 1.25rem;
                top: 5.625rem;
                .invite-head{
                    padding: 1rem 0;
                }
                .invi-card-title{
                    font-size: @fs-normal;
                    color: #5d5d61;
                    font-weight: bold;
                    line-height: 1.5625rem;
                    margin-bottom: 0.875rem;
                }
                .invite-code{
                    font-size: 2.8125rem;
                    color: #6597fa;
                    line-height: 2.8125rem;
                }

                .invite-user{
                    padding:6% 6% 2%;
                    border-top: 1px dashed #bababa;
                    flex-direction: column;
                    justify-content: space-between;
                    position: absolute;
                    right: 0;
                    left: 0;
                    bottom: 0;
                    top: 7.25rem;
                    .user-info{
                        font-size: 0.8125rem;
                        line-height: 1.25rem;
                        color: #4c4c51;
                    }
                    .user-qrcode{
                        width: 60%;
                        border: 1px solid #e3e3e3;
                        padding: 0.625rem;
                        height: 200px;
                            // canvas{

                            // }
                            img{
                                height: 100% !important;
                                width: 100% !important;
                                display: block;
                            }
                    }
                    .bottom-text{
                        height: 1.25rem;
                        width: 100%;
                        &:before{
                            transform: translateY(-50%) scaleY(0.5);
                            top: 50%;
                            z-index: 1;
                        }
                        .bottom-txt{
                            position: absolute;
                            top: 0;
                            left: 50%;
                            transform: translateX(-50%);
                            padding: 0 0.5rem;
                            line-height: 1.25rem;
                            height: 1.25rem;
                            font-size: @fs-smaller;
                            color: #4c4c51;
                            background-color: #fff;
                            z-index: 3;
                            white-space:nowrap;
                        }
                    }
                }
            }
        }
        .popup1{
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            z-index: 10;
        }
    }

</style>
