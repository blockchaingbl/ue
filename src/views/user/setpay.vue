<template lang="html">
<div class="setpay">
    <div class="setpay-box">
        <div class="setpay-top">注：至少激活一种方式，您激活的方式我们将展现给买家</div>
            <div class="setpay-block">
                <div class="item">
                    <router-link to="/user/setpay/setbank" class="paytype no-paytype flex-box" v-if="bankcard==0">
                        <div class="iconfont">&#xe6ef;</div>
                        <div class="no-paytype-text">点击绑定</div>
                    </router-link>
                    <router-link to="/user/setpay/setbank" class="paytype hasType bank-card" v-else>
                        <div class="item-title"><div class="iconfont">&#xe6ef;</div>银行卡账户</div>
                        <div class="item-list">姓名：{{bankcard.payment_receipt}}</div>
                        <div class="item-list">银行：{{bankcard.payment_org}}</div>
                        <div class="item-list">卡号：{{bankcard.payment_account}}</div>
                    </router-link>
                </div>
                <div class="item">
                    <router-link to="/user/setpay/setalipay" class="paytype no-paytype flex-box" v-if="alipay==0">
                        <div class="iconfont">&#xe6ec;</div>
                        <div class="no-paytype-text">点击绑定</div>
                    </router-link>
                    <router-link to="/user/setpay/setalipay" class="paytype hasType Alipay" v-else>
                        <div class="item-title"><div class="iconfont">&#xe6ed;</div>支付宝账户</div>
                        <div class="item-list">姓名：{{alipay.payment_receipt}}</div>
                        <div class="item-list">账号：{{alipay.payment_account}}</div>
                    </router-link>
                </div>
                <div class="item">
                    <router-link to="/user/setpay/setwechat" class="paytype no-paytype flex-box" v-if="weixin==0">
                        <div class="iconfont">&#xe6ed;</div>
                        <div class="no-paytype-text">点击绑定</div>
                    </router-link>
                    <router-link to="/user/setpay/setwechat" class="paytype hasType WeChat" v-else>
                        <div class="item-title"><div class="iconfont">&#xe6ed;</div>微信支付账户</div>
                        <div class="item-list">姓名：{{weixin.payment_receipt}}</div>
                        <div class="item-list">账号：{{weixin.payment_account}}</div>
                    </router-link>
                </div>
                <div class="item">
                    <router-link to="/user/setpay/setwallet" class="paytype no-paytype flex-box" v-if="wallet==0">
                        <div class="iconfont"><img  style="width: 32px;height: 35px;" src="@/assets/images/wallet.png" alt=""></div>
                        <div class="no-paytype-text">点击绑定</div>
                    </router-link>
                    <router-link to="/user/setpay/setwallet" class="paytype hasType Alipay" v-else>
                        <div class="item-title"><div class="iconfont">&#xe6ed;</div>钱包已绑定</div>
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    components: {

    },
    data () {
        return {
            bankcard:'',
            alipay:'',
            weixin:'',
            wallet:''
        }
    },
    mounted () {
        this.getUserinfo();
        this.getBindInfo();
    },
    methods:{
        getUserinfo(){
            this.$http.post('/api/app.user/account/info',{}).then(res => {
                if(res.data.account_info.security==null){
                    this.$vux.toast.text("还未设置密码，请设置");
                    this.$router.push({path:'/user/edit/security'});
                }
                // this.security =
            }).catch(err => {
                    if (err.errcode) {
                    this.$vux.toast.text(err.message);
                }
                console.log(err);
                //  this.Toast(err || '网络异常，请求失败');
            });
        },
        getBindInfo(){
            this.$http.post('/api/app.payment/payment/bindinfo',{}).then(res => {
                if(res.data.bind_info.bankcard){
                    this.bankcard = res.data.bind_info.bankcard;
                }
                if(res.data.bind_info.alipay){
                    this.alipay = res.data.bind_info.alipay;
                }
                if(res.data.bind_info.weixin){
                    this.weixin = res.data.bind_info.weixin;
                }
                  if(res.data.bind_info.wallet){
                    this.wallet = res.data.bind_info.wallet;
                  }

            }).catch(err => {
                if (err.errcode) {
                    this.$vux.toast.text(err.message);
                }
                console.log(err);
                //  this.Toast(err || '网络异常，请求失败');
            });
        }
    }
}
</script>

<style lang="less">
    @import '../../assets/css/variable.less';
    @import '~vux/src/styles/1px.less';
    
    .setpay{
        background: #fff;
        padding: 0 10px;
        height: 100%;
        .setpay-top{
            line-height: 3.25rem;
            font-size: @fs-small;
            color: #ff6c6c;
        }
        .item{
            margin-bottom: 1.25rem;
        }
        .paytype{

            height: 7.8125rem;
            color: #fff;
        }
        .no-paytype{
            border: 1px dashed #8d97a3;
            border-radius: 5px;
            color: #8d97a3;
            font-size: @fs-small;
            flex-direction: column;
            justify-content: center;
            .iconfont{
                font-size: 2rem;
            }
            .no-paytype-text{
                line-height: 1.75rem;
            }
        }
        .hasType{
            padding: 0.75rem 2.375rem;
            display: block;
            .item-title {
                position: relative;
                line-height: 1.75rem;
                .iconfont {
                    position: absolute;
                    left: -1.375rem;
                    line-height: 1rem;
                    font-size: 1rem;
                    top: 50%;
                    transform: translateY(-50%);
                    border-radius: 50%;
                }
            }
        }
        .bank-card{
            background: url(../../assets/images/bank_card_bg.png) no-repeat top center;
            background-size: 100% 100%;
            .iconfont {
                background: #ff958b;
            }
        }
        .Alipay{
            background: url(../../assets/images/Alipay_bg.png) no-repeat top center;
            background-size: 100% 100%;
        }
        .WeChat{
            background: url(../../assets/images/WeChat_bg.png) no-repeat top center;
            background-size: 100% 100%;
            .iconfont {
                background: #00d394;
            }
        }
    }
</style>
