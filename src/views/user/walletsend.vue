<template lang="html">
<div>
<div class="walletSend">
    <div class="send-item vux-1px-b">
        <div class="send-item-title flex-box">
            <div class="item-title flex-1">发送数量</div>
            <div class="hasBcty">可用{{$store.state.init.coin_uint}}：{{vc_total}}</div>
        </div>
        <div class="send-item-input">
            <x-input type="number" :show-clear="false" :required="true" :is-type="checkAmount" v-model="amount"></x-input>
        </div>
    </div>
    <div class="send-item vux-1px-b">
        <div class="send-item-title flex-box">
            <div class="item-title flex-1">对方钱包地址</div>
        </div>
        <div class="send-item-input">
            <x-input :required="true" v-model="to_address"></x-input>
        </div>
    </div>
    <div class="send-item vux-1px-b">
        <div class="send-item-title flex-box">
            <div class="item-title flex-1">资产密码</div>
        </div>
        <div class="send-item-input">
            <x-input type="password" :show-clear="false" :required="true" v-model="security"></x-input>
        </div>
    </div>
    <div class="send-item">
        <div class="send-item-title flex-box">
            <div class="item-title flex-1">备注</div>
        </div>
        <div class="send-item-input">
            <x-textarea v-model="memo"></x-textarea>
        </div>
    </div>
</div>
    <box gap="40px 35px 0">
        <x-button type="primary" style="border-radius:99px;" class='found-btn' v-on:click.native="sureSend()">发送</x-button>
    </box>
</div>
</template>
<script>
import router from '@/router';
import { md5 } from 'vux';
export default {
    components: {
        //stepone:() => import('@/views/login/inc/stepone'), 
    },
    data () {
        return {
            vc_total:0,
            amount:'',
            to_address:'',
            security:'',
            memo:''
        }
    },
    mounted () {
        this.amount = router.currentRoute.query.amount;
        this.to_address = router.currentRoute.query.to_address;
        this.getUserinfo();
    },
    methods:{
        getUserinfo(){
            this.$http.post('/api/app.user/account/info',{}).then(res => {
                this.vc_total=res.data.account_info.vc_total;
            }).catch(err => {
                if (err.errcode) {
                    this.$vux.toast.text(err.message);
                }
                console.log(err);
                //  this.Toast(err || '网络异常，请求失败');
            });
        },
        sureSend(){
            this.$http.post('/api/app.wallet/wallet/send',{
                "amount":this.amount,
                "to_address":this.to_address,
                "security":md5(this.security),
                "memo":this.memo
            }).then(res => {
                if(res.errcode==0){
                    this.$vux.toast.text("发送成功");
                    this.$router.push({path:'/user/wallet'});
                }
            }).catch(err => {
                if (err.errcode) {
                    this.$vux.toast.text(err.message);
                }
                console.log(err);
                //  this.Toast(err || '网络异常，请求失败');
            });
        },
        checkAmount:function(value){
            return {
                valid: value > 0,
                msg: '数量必须大于零'
            }
        }
    }
}
</script>

<style lang="less">
    @import '~vux/src/styles/1px.less';
    @import '../../assets/css/variable.less';
    
    .walletSend{
        padding-left: 0.625rem;
        background: #fff;
        .send-item{
            .send-item-title{
                padding-right: 0.625rem;
                line-height: 2.75rem;
                color: #4c4c51;
            }
            .hasBcty{
                color: #888;
                font-size: @fs-small;
            }
            .send-item-input{
                .weui-cell{
                    font-family:Arial, "Microsoft Yahei";
                    padding-left: 0;
                    font-size: @fs-biger;
                    color: #5d5d61;
                }
            }
        }
        .vux-1px-b:after{
            border-color: #ebebeb;
        }
    }
</style>
