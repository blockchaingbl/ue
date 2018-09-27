<template lang="html">
<div class="update">
    <div class="update-box">
        <div class="update-gxs vux-1px-b">
            <x-input type="number" v-model="amount" @on-change="changeAmount" :show-clear="false" :required="true" :is-type="checkAmount" placeholder="填写接收数量"></x-input>
        </div>
        <qrcode class="qrcode" :value="qrcodeurl" type="img"></qrcode>
        <div class="account-name">账户名：{{username}}</div>
    </div>
</div> 
</template>
<script>
import { Qrcode } from 'vux'
export default {
    components: {
        Qrcode
    },
    data () {
        return {
            qrcodeurl:'',
            amount:'',
            address:'',
            username:'',
            url:"",
        }
    },
    mounted () {
        this.getUserinfo();
    },
    methods:{
        getUserinfo(){
            this.$http.post('/api/app.user/account/info',{}).then(res => {
                this.username=res.data.account_info.username;
                this.address=res.data.account_info.address;
            }).catch(err => {
                if (err.errcode) {
                    this.$vux.toast.text(err.message);
                }
                console.log(err);
                //  this.Toast(err || '网络异常，请求失败');
            });
        },
        changeAmount:function(){
            this.qrcodeurl = "/user/wallet/send?amount="+this.amount+"&to_address="+this.address;
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
    @import '../../assets/css/variable.less';
    @import '~vux/src/styles/1px.less';
    
    .update{
        height: 100%;
        background: #fff;
        .update-box{
            padding: 4.125rem;
            text-align: center;
            .weui-cell{
                input{
                    text-align: center;
                    color: #4c4c51;
                }
                input::-webkit-input-placeholder {color:#999;
                    color: #bbb;
                }
            }
            .qrcode{
                margin: 1.25rem 0 0.75rem;
            }
            .account-name{
                line-height: 1.875rem;
                color: #4c4c51;
            }
        }
        .vux-1px-b:after{
            border-color: #7b7b7b;
        }
    }
</style>
