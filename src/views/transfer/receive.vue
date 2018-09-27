<template lang="html">
    <div class="page-receive">
        <div class="invitbox">
            <h3 class="invite-title"></h3>
            <div class="invite-decs"></div>
            <div class="invite-content">
                <div class="invite-head">
                    <div class="invi-card-title">扫一扫,向我转出</div>
                    <div class="invite-code"></div>
                </div>
                <div class="invite-user flex-box">
                    <qrcode :value="id" type="img" class="user-qrcode" :size="200">
                    </qrcode>
                    <div class="bottom-text">
                        <router-link  :to="{path:'/transfer/order'}">
                            <div class="bottom-txt">转入记录</div>
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import {Qrcode} from 'vux'
    export default {
    components: {
        Qrcode
    },
    data () {
        return {
            id:"",
            loading:true
        }
    },
    mounted () {
        this.getUserinfo();
    },
    methods: {
        getUserinfo(){
            var loading = this.$loading({text:"加载中"});
            this.$http.post('/api/app.user/account/info',{}).then(res => {
                this.id=res.data.account_info.id.toString();
                loading.close();
            }).catch(err => {
                if (err.errcode) {
                    this.$vux.toast.text(err.message);
                }
                loading.close();
                //  this.Toast(err || '网络异常，请求失败');
            });
        },
    },
}
</script>
<style lang="less" scoped>
@import "../../assets/css/variable.less";
.page-receive{
    .invitbox{
        background: #4964cc;
        /*background-image: url(../../assets/images/invita_card_bg.jpg);*/
        background-repeat: no-repeat;
        background-position: top center;
        background-size: 100%;
        padding: 0.9375rem 0.625rem 1.25rem;
        text-align: center;
        .invite-title {
            font-size: 1.5625rem;
            height: 2.1875rem;
            color: #fff;
            font-weight: bold;
        }
        .invite-decs{
            font-size: @fs-middle;
            color: #fff;
            height: 1.5rem;
            margin-bottom: 1rem;
        }
        .invite-content{
            background-color: #fff;
            border-radius: 0.25rem;

            position: absolute;
            left: 0.875rem;
            right: 0.875rem;
            bottom: 1.25rem;
            top: 1.625rem;
            .invite-head{
                padding: 6rem 0;
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
                padding-top: 6%;
                flex-direction: column;
                justify-content: space-between;
                position: absolute;
                right: 0;
                left: 0;
                bottom: 0;
                top: 7.25rem;
                /*.user-info{*/
                    /*font-size: 0.8125rem;*/
                    /*line-height: 1.25rem;*/
                    /*color: #4c4c51;*/
                /*}*/
                .user-qrcode{
                    border: 1px solid #e3e3e3;
                    padding: 0.625rem;
                    img{
                        height: 100% !important;
                        width: 100% !important;
                        display: block;
                    }
                }
                .bottom-text{
                    height: 1.5625rem;
                    position: relative;
                    bottom: 3rem;
                    display: inline;
                    .bottom-txt{
                        display: inline;
                        width: 100%;
                        text-align: center;
                    }
                }

            }
        }
    }
}
</style>


